<?php


namespace App\RemoteModel\DataExtractor;


use App\RemoteModel\DataExtractor\Exceptions\DataExtractorException;

class TranslationExtractor implements DataExtractorInterface
{
    public function extract(array $data): array
    {
        if (!isset($data['translations'])) {
            throw DataExtractorException::incorrectData();
        }

        foreach ($data['translations'][0] as $field => $translation) {
            if ($field === 'language') {
                continue;
            }

            $data[$field] = $translation;
        }

        return $data;
    }
}
