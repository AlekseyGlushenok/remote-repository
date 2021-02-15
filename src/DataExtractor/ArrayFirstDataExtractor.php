<?php

namespace App\RemoteModel\DataExtractor;

use App\RemoteModel\DataExtractor\Exceptions\DataExtractorException;

class ArrayFirstDataExtractor implements DataExtractorInterface
{
    /**
     * @param array $data
     * @return array
     *
     * @throws DataExtractorException
     */
    public function extract(array $data): array
    {
        if (isset($data['data']) && is_array($data['data']) && !empty($data['data'])) {
            return $data['data'][0];
        }

        throw DataExtractorException::incorrectData();
    }
}
