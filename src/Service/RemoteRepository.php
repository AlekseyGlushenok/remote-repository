<?php

namespace App\RemoteModel\Service;

use App\RemoteModel\DataExtractor\Exceptions\DataExtractorException;
use App\RemoteModel\Exceptions\RemoteModelException;
use App\RemoteModel\Model;
use App\RemoteModel\ModelConfig;
use App\RemoteModel\ModelFactory;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class RemoteRepository
{
    private Client $client;
    private ModelFactory $factory;
    private ModelConfig $config;

    public function __construct(
        Client $client,
        ModelFactory $factory,
        ModelConfig $config
    )
    {
        $this->client = $client;
        $this->factory = $factory;
        $this->config = $config;
    }

    public function get(string $model, int $id): Model
    {
        $url = $this->config->getLoadUrl($model, $id);
        try {
            $response = $this->client->get($url);
            $data = json_decode($response->getBody()->getContents(), true);
        } catch (GuzzleException $e) {
            throw RemoteModelException::modelActionFail('load', $e);
        }

        return $this->factory->make(
            $this->config->getClass($model),
            $data,
            $this->config->getRequiredFields($model),
            $this->config->getAvailableFields($model),
            $this->config->getDataExtractor($model, 'get')
        );
    }

    public function save(Model $model): Model
    {
        return $this->find($model) ? $this->update($model) : $this->saveRemote($model);
    }

    public function find(Model $model): bool
    {
        if ($model->id !== null) {
            return true;
        }

        $query = [];

        foreach ($this->config->getFindParams($model->getName()) as $field) {
            if ($model->$field !== null) {
                $query[$field] = $model->$field;
            }
        }

        try {
            $response = $this->client->get(
                $this->config->getFindUrl($model->getName(), $model->toArray()),
                [
                    'query' => $query,
                ]
            );
            $data = json_decode($response->getBody()->getContents(), true);
        } catch (GuzzleException $e) {
            return false;
        }

        $extractor = $this->config->getDataExtractor($model->getName(), 'compare');

        if ($extractor !== null) {
            try {
                $data = $extractor->extract($data);
            } catch (DataExtractorException $e) {
                return false;
            }
        }

        $match = $this->compare($model, $data);

        if ($match) {
            $model->id = $data['id'];
        }

        return $match;
    }

    public function delete(Model $model): void
    {
    }

    private function compare(Model $model, array $data)
    {
        foreach ($this->config->getRequiredFields($model->getName()) as $field) {
            if ($data[$field] != $model->$field) {
                return false;
            }
        }

        return true;
    }

    private function update(Model $model): Model
    {
        try {
            $response = $this->client->put(
                $this->config->getUpdateUrl($model->getName(), $model->id, $model->toArray()),
                ['form_params' => $model->toArray()]
            );

            return $model;
        } catch (GuzzleException $e) {
            throw RemoteModelException::modelActionFail('update', $e);
        }
    }

    private function saveRemote(Model $model): Model
    {
        try {
            $response = $this->client->post(
                $this->config->getCreateUrl($model->getName(), $model->toArray()),
                ['form_params' => $model->toArray()]
            );
            $data = json_decode($response->getBody()->getContents(), true);
        } catch (GuzzleException $e) {
            throw RemoteModelException::modelActionFail('save', $e);
        }

        $extractor = $this->config->getDataExtractor($model->getName(), 'save');
        if ($extractor !== null) {
            $data = $extractor->extract($data);
        }

        $model->id = $data['id'];

        return $model;
    }

    public function create(string $model, array $data): Model
    {
        return $this->factory->make(
            $this->config->getClass($model),
            $data,
            $this->config->getRequiredFields($model),
            $this->config->getAvailableFields($model)
        );
    }
}
