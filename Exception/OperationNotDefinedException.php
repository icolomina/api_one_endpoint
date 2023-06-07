<?php

namespace Ict\ApiOneEndpoint\Exception;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class OperationNotDefinedException extends NotFoundHttpException
{
    public function __construct(string $operation, int $code = 0, ?\Throwable $previous = null)
    {
        $message = sprintf('Operation %s is not defined', $operation);
        parent::__construct($message, $previous, $code);
    }
}
