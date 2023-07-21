<?php

namespace Ict\ApiOneEndpoint\Operation;

use Ict\ApiOneEndpoint\Contract\Operation\OperationInterface;
use Ict\ApiOneEndpoint\Exception\ContextOperationNotMatchException;
use Ict\ApiOneEndpoint\Exception\ContextOperationRequiredException;
use Ict\ApiOneEndpoint\Exception\OperationNotDefinedException;
use Ict\ApiOneEndpoint\Model\Api\ApiInput;
use Ict\ApiOneEndpoint\Model\Api\Context;

class OperationHandlerDiscover
{
    public function __construct(
        private readonly OperationCollection $operationCollection
    ){ }

    public function discover(ApiInput $apiInput, Context $context): OperationInterface
    {
        if(!$this->operationCollection->hasOperation($apiInput->getOperation())){
            throw new OperationNotDefinedException($apiInput->getOperation());
        }

        $operation = $this->operationCollection->getOperation($apiInput->getOperation());
        if($context->isEmpty() && !empty($operation->getContext())){
            throw new ContextOperationRequiredException($operation->getName());
        }

        if(!$context->isEmpty() && !empty($operation->getContext()) && !in_array($context->context, $operation->getContext()) ) {
            throw new ContextOperationNotMatchException($operation->getName(), $context->context);
        }

        return $this->operationCollection->getOperation($apiInput->getOperation());
    }
}
