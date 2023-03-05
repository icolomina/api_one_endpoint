<?php

namespace Ict\ApiOneEndpoint\Message;

use Ict\ApiOneEndpoint\Model\Api\ApiInput;

class OperationMessage
{
    public function __construct(
        public readonly mixed $operationData,
        public readonly string $operation,
        public readonly ?string $userIdentifier = null
    ){ }
}
