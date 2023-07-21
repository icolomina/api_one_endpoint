<?php

namespace Ict\ApiOneEndpoint\Exception;

class ContextOperationRequiredException extends \RuntimeException
{
    public function __construct(string $operation, string $message = "", int $code = 0, ?\Throwable $previous = null)
    {
        $message = sprintf('Operation %s requires context but no context given', $operation);
        parent::__construct($message, $code, $previous);
    }
}
