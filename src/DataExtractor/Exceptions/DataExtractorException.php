<?php

namespace App\RemoteModel\DataExtractor\Exceptions;

use Exception;

class DataExtractorException extends Exception
{
    private const INCORRECT_DATA = 'Provided data cant be extract';

    public static function incorrectData(): self
    {
        return new self(self::INCORRECT_DATA, 500);
    }
}
