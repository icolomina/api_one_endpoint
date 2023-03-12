<?php

namespace Ict\ApiOneEndpoint\Operation;

use Ict\ApiOneEndpoint\Message\OperationMessage;
use Ict\ApiOneEndpoint\Model\Attribute\IsBackground;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DelayStamp;

class OperationBackgroundHandler
{

    public function __construct(
        private readonly MessageBusInterface $bus
    ){ }

    public function sendToBackground(IsBackground $isBackground, object $operationData, string $operationName, ?string $userIdentifier): ?int
    {
        $stamps = ($isBackground->delay > 0) ? [new DelayStamp($isBackground->delay * 1000)] : [];
        $this->bus->dispatch(new OperationMessage($operationData, $operationName, $userIdentifier), $stamps);

        return count($stamps) > 0
            ? $stamps[0]->getDelay()
            : null
        ;
    }
}
