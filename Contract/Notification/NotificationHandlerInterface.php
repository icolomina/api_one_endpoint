<?php

namespace Ict\ApiOneEndpoint\Contract\Notification;

interface NotificationHandlerInterface
{
    public function pushNotification(string $topic, string $data): void;
    public function getName(): string;
}
