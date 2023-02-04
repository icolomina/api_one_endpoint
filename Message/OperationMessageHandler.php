<?php

namespace Ict\ApiOneEndpoint\Message;

use Contract\Api\OperationNotificationInterface;
use Ict\ApiOneEndpoint\Contract\Operation\OperationBackgroundInterface;
use Ict\ApiOneEndpoint\Operation\OperationCollection;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Uid\Uuid;

#[AsMessageHandler]
class OperationMessageHandler
{

    public function __construct(
        private readonly OperationCollection $operationCollection,
        private readonly HubInterface $hub
    ){ }

    public function __invoke(OperationMessage $message): void
    {
        $apiInput  = $message->apiInput;
        $operation = $this->operationCollection->getOperation($apiInput->getOperation());
        $operation->perform($apiInput);

        if($operation instanceof OperationNotificationInterface){
            $topic = $operation->getTopic($message->userIdentifier);
            $this->hub->publish(
                new Update(
                    $topic,
                    $operation->getNotificationData()
                )
            );
        }

    }
}
