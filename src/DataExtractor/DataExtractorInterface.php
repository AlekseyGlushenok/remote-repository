<?php

namespace App\RemoteModel\DataExtractor;

use App\RemoteModel\DataExtractor\Exceptions\DataExtractorException;

interface DataExtractorInterface
{
    /**
     * @param array $data
     * @return array
     * @throws DataExtractorException
     */
    public function extract(array $data): array;
}
