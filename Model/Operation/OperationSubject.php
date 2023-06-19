<?php

namespace Ict\ApiOneEndpoint\Model\Operation;

class OperationSubject
{
    public function __construct(
        public readonly string $operation,
        public readonly string $operationName,
        public readonly ?string $group = null,
        public readonly mixed $operationData = null
    ){ }
}
