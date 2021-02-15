<?php


namespace App\RemoteModel\DataExtractor;

use App\RemoteModel\DataExtractor\Exceptions\DataExtractorException;

class InnerDataExtractor implements DataExtractorInterface
{
    public function extract(array $data): array
    {
        if (isset($data['data'])) {
            return $data['data'];
        }

        throw DataExtractorException::incorrectData();
    }
}