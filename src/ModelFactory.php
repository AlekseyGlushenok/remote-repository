<?php

namespace App\RemoteModel;


use App\RemoteModel\DataExtractor\DataExtractorInterface;
use Iterator;

class ModelFactory
{
    public function make(
        string $model,
        array $data,
        Iterator $requiredFields,
        Iterator $availableFields,
        ?DataExtractorInterface $dataExtractor = null
    ): Model
    {
        $instance = new $model;

        if ($dataExtractor !== null) {
            $data = $dataExtractor->extract($data);
        }

        foreach ($requiredFields as $field) {
            $instance->$field = $data[$field];
        }

        foreach ($availableFields as $field) {
            if (isset($data[$field])) {
                $instance->$field = $data[$field];
            }
        }

        return $instance;
    }
}
