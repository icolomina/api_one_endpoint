<?php

namespace Ict\ApiOneEndpoint\Exception;

class OperationNotDefinedException extends \RuntimeException
{
    public function __construct(string $operation, string $message = "", int $code = 0, ?Throwable $previous = null)
    {
        $message = sprintf('Operation %s is not defined', $operation);
        parent::__construct($message, $code, $previous);
    }
}
