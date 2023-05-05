<?php

namespace Ict\ApiOneEndpoint\EventSubscriber\Event;

use Ict\ApiOneEndpoint\Model\Api\ApiOutput;
use Symfony\Contracts\EventDispatcher\Event;

class OperationPerformedEvent extends Event
{
    public function __construct(
        public readonly string $operation,
        public readonly ApiOutput $operationResult
    ){ }
}
