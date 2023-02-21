<?php

namespace Ict\ApiOneEndpoint\Operation;

use Ict\ApiOneEndpoint\Contract\Operation\OperationInterface;
use Ict\ApiOneEndpoint\Exception\OperationNotDefinedException;
use Ict\ApiOneEndpoint\Model\Api\ApiInput;

class OperationHandlerDiscover
{
    public function __construct(
        private readonly OperationCollection $operationCollection
    ){ }

    public function discover(ApiInput $apiInput): OperationInterface
    {
        if(!$this->operationCollection->hasOperation($apiInput->getOperation())){
            throw new OperationNotDefinedException($apiInput->getOperation());
        }

        return $this->operationCollection->getOperation($apiInput->getOperation());
    }
}
