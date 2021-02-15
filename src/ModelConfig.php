<?php

namespace App\RemoteModel;

use App\RemoteModel\DataExtractor\DataExtractorInterface;
use App\RemoteModel\DataExtractor\Extractor;
use App\RemoteModel\Exceptions\RemoteModelException;
use Generator;

class ModelConfig
{
    private array $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function getBaseUrl(string $model): string
    {
        $this->assertModel($model);
        $this->assertField($model, 'baseUrl');

        return $this->config[$model]['baseUrl'];
    }

    public function getFindUrl(string $model, array $fields): string
    {
        $this->assertModel($model);

        try {
            $this->assertField($model, 'findUrl');
        } catch (RemoteModelException $e) {
            return $this->getBaseUrl($model);
        }

        try {
            $this->assertField($model, 'findQuery');
        } catch (RemoteModelException $e) {
            return $this->config[$model]['findUrl'];
        }

        $url = $this->config[$model]['findUrl'];
        $queryParams = $this->config[$model]['findQuery'];
        $params = [];

        foreach ($queryParams as $queryParam) {
            $params[] = $fields[$queryParam];
        }

        return sprintf($url, ...$params);
    }

    public function getCreateUrl(string $model, array $fields): string
    {
        $this->assertModel($model);

        try {
            $this->assertField($model, 'createUrl');
        } catch (RemoteModelException $e) {
            return $this->getBaseUrl($model);
        }

        try {
            $this->assertField($model, 'createQuery');
        } catch (RemoteModelException $e) {
            return $this->config[$model]['createUrl'];
        }

        $url = $this->config[$model]['createUrl'];
        $queryParams = $this->config[$model]['createQuery'];
        $params = [];

        foreach ($queryParams as $queryParam) {
            $params[] = $fields[$queryParam];
        }

        return sprintf($url, ...$params);
    }

    public function getUpdateUrl(string $model, int $id, array $fields): string
    {
        $this->assertModel($model);

        try {
            $this->assertField($model, 'updateUrl');
        } catch (RemoteModelException $e) {
            return $this->getBaseUrl($model) . '/' . $id;
        }

        try {
            $this->assertField($model, 'updateQuery');
        } catch (RemoteModelException $e) {
            return sprintf($this->config[$model]['updateUrl'], $id);
        }

        $url = $this->config[$model]['updateUrl'];
        $queryParams = $this->config[$model]['updateQuery'];
        $params = [];

        foreach ($queryParams as $queryParam) {
            $params[] = $fields[$queryParam];
        }
        $params[] = $id;

        return sprintf($url, ...$params);
    }

    public function getDeleteUrl(string $model, int $id): string
    {
        $this->assertModel($model);

        try {
            $this->assertField($model, 'deleteUrl');
        } catch (RemoteModelException $e) {
            return $this->getBaseUrl($model) . '/' . $id;
        }

        return sprintf($this->config[$model]['deleteUrl'], $id);
    }

    public function getLoadUrl(string $model, int $id): string
    {
        $this->asserTModel($model);

        try {
            $this->assertField($model, 'loadUrl');
        } catch (RemoteModelException $e) {
            return $this->getBaseUrl($model) . '/' . $id;
        }

        return sprintf($this->config[$model]['loadUrl'], $id);
    }

    public function getRequiredFields(string $model): Generator
    {
        $this->assertModel($model);
        $this->assertField($model, 'fields');

        foreach ($this->config[$model]['fields'] as $name => $field) {
            if (isset($field['required']) && $field['required'] === true) {
                yield $name;
            }
        }

        return;
    }

    public function getAvailableFields(string $model): Generator
    {
        $this->assertModel($model);
        $this->assertField($model, 'fields');

        foreach ($this->config[$model]['fields'] as $name => $field)
        {
            if (!isset($field['required']) || $field['required'] !== true) {
                yield $name;
            }
        }

        return;
    }

    public function getFields(string $model): array
    {
        $this->assertModel($model);
        $this->assertField($model, 'fields');

        return $this->config[$model]['fields'];
    }

    public function getDataExtractor(string $model, ?string $context = null): ?DataExtractorInterface
    {
        $this->assertModel($model);

        try {
            $this->assertField($model, 'extractor');
        } catch (RemoteModelException $e) {
            return null;
        }

        if (empty($this->config[$model]['extractor'][$context ?? 'default'])) {
            return null;
        }

        $extractors = $this->config[$model]['extractor'][$context ?? 'default'];

        $dataExtractor = new Extractor();

        foreach ($extractors as $extractor) {
            $extractorInstance = new $extractor();
            $dataExtractor->addExtractor($extractorInstance);
        }

        return $dataExtractor;
    }

    public function getClass(string $model): string
    {
        $this->assertModel($model);
        $this->assertField($model, 'class');

        return $this->config[$model]['class'];
    }

    public function getFindParams(string $model): array
    {
        $this->assertModel($model);

        try {
            $this->assertField($model, 'findParams');
        } catch (RemoteModelException $e) {
            return [];
        }

        return $this->config[$model]['findParams'];
    }

    private function assertModel(string $model): void
    {
        if (!isset($this->config[$model])) {
            throw RemoteModelException::modelNotRegistered($model);
        }
    }

    private function assertField(string $model, string $field)
    {
        if (!isset($this->config[$model][$field])) {
            throw RemoteModelException::paramMustBeProvided($field);
        }
    }
}
