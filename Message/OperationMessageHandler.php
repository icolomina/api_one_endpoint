<?php

namespace Ict\ApiOneEndpoint\Message;

use Ict\ApiOneEndpoint\Contract\Operation\OperationNotificationInterface;
use Ict\ApiOneEndpoint\Notification\NotificationManager;
use Ict\ApiOneEndpoint\Operation\OperationCollection;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class OperationMessageHandler
{

    public function __construct(
        private readonly OperationCollection $operationCollection,
        private readonly NotificationManager $notificationManager
    ){ }

    public function __invoke(OperationMessage $message): void
    {
        $operationHandler = $this->operationCollection->getOperation($message->operation);
        $operationHandler->perform($message->operationData);

        if(!empty($this->notificationManager->getType()) && $operationHandler instanceof OperationNotificationInterface){
            $topic = $operationHandler->getTopic($message->userIdentifier);
            $this->notificationManager->notify($message->operationData, $topic);
        }

    }
}
