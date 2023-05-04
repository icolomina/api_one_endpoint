<?php

namespace Ict\ApiOneEndpoint\Exception;

class OperationMissedUserException extends \RuntimeException
{
    public function __construct(string $operation, int $code = 0, ?\Throwable $previous = null)
    {
        $message = sprintf('Operation %s required user but not authenticated user found', $operation);
        parent::__construct($message, $code, $previous);
    }
}
