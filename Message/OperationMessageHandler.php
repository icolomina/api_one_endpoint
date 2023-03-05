<?php

namespace Ict\ApiOneEndpoint\Message;

use Ict\ApiOneEndpoint\Contract\Operation\OperationNotificationInterface;
use Ict\ApiOneEndpoint\Operation\OperationCollection;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class OperationMessageHandler
{

    public function __construct(
        private readonly OperationCollection $operationCollection,
        private readonly HubInterface $hub
    ){ }

    public function __invoke(OperationMessage $message): void
    {
        $operationHandler = $this->operationCollection->getOperation($message->operation);
        $operationHandler->perform($message->operationData);

        if($operationHandler instanceof OperationNotificationInterface){
            $topic = $operationHandler->getTopic($message->userIdentifier);
            $this->hub->publish(
                new Update(
                    $topic,
                    $operationHandler->getNotificationData()
                )
            );
        }

    }
}
