<?php

namespace Ict\ApiOneEndpoint\Contract\Operation;

interface OperationNotificationInterface
{
    public function getNotificationData(): string;
    public function getTopic(?string $userIdentifier = null): string;
}
