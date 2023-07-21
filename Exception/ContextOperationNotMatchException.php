<?php

namespace Ict\ApiOneEndpoint\Exception;

class ContextOperationNotMatchException extends \RuntimeException
{
    public function __construct(string $operation, string $context, string $message = "", int $code = 0, ?\Throwable $previous = null)
    {
        $message = sprintf('Operation %s context does not match given %s', $operation, $context);
        parent::__construct($message, $code, $previous);
    }
}
