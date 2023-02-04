<?php

namespace Contract\Api;

use Symfony\Component\Security\Core\User\UserInterface;

interface OperationNotificationInterface
{
    public function getNotificationData(): string;
    public function getTopic(?string $userIdentifier = null): string;
}
