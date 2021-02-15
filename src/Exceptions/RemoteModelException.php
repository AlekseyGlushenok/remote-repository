<?php

namespace App\RemoteModel\Exceptions;

use Exception;
use Throwable;

class RemoteModelException extends Exception
{
    private const NOT_REGISTERED = 'Model %s not registered';
    private const PARAM_NOT_PROVIDED = 'Parameter %s must be provided for remote model';
    private const MODEL_MUST_BE_LOADED = 'Model %s must be loaded before await response';
    private const ACTION_FAIL = 'Model get exception on %s';

    public static function modelNotRegistered(string $name)
    {
        return new self(sprintf(self::NOT_REGISTERED, $name), 404);
    }

    public static function paramMustBeProvided(string $param)
    {
        return new self(sprintf(self::PARAM_NOT_PROVIDED, $param), 500);
    }

    public static function paramsMustBeProvided(array $params)
    {
        $paramNamesMessage = '[' . implode(', ', $params) . ']';

        return self::paramMustBeProvided($paramNamesMessage);
    }

    public static function modelMustBeLoaded(string $model)
    {
        return new self(sprintf(self::MODEL_MUST_BE_LOADED, $model), 500);
    }

    public static function modelActionFail(string $action, Throwable $prev)
    {
        return new self(sprintf(self::ACTION_FAIL, $action), 500, $prev);
    }
}
