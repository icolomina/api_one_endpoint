<?php

namespace Ict\ApiOneEndpoint\Message;

use Ict\ApiOneEndpoint\EventSubscriber\Event\OperationPerformedEvent;
use Ict\ApiOneEndpoint\Operation\OperationCollection;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

#[AsMessageHandler]
class OperationMessageHandler
{

    public function __construct(
        private readonly OperationCollection $operationCollection,
        private readonly EventDispatcherInterface $eventDispatcher
    ){ }

    public function __invoke(OperationMessage $message): void
    {
        $operationHandler = $this->operationCollection->getOperation($message->operation);
        $operationResult  = $operationHandler->perform($message->operationData);

        $this->eventDispatcher->dispatch(new OperationPerformedEvent($operationHandler->getName(), $operationResult));
    }
}
