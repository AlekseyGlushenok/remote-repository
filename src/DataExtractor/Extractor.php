<?php


namespace App\RemoteModel\DataExtractor;


class Extractor implements DataExtractorInterface
{
    /** @var DataExtractorInterface[] */
    private array $extractors = [];

    public function extract(array $data): array
    {
        foreach ($this->extractors as $extractor) {
            $data = $extractor->extract($data);
        }

        return $data;
    }

    public function addExtractor(DataExtractorInterface $extractor)
    {
        $this->extractors[] = $extractor;
    }
}