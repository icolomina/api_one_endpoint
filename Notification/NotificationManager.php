<?php

namespace Ict\ApiOneEndpoint\Notification;

class NotificationManager
{
    public function __construct(
        private readonly NotificationCollection $notificationCollection,
        private readonly ?string $notificationType
    ){ }

    public function notify(string $notificationData, string $topic): void
    {
        $this
            ->notificationCollection
            ->getNotificationHandler($this->notificationType)
            ->pushNotification($topic, $notificationData)
        ;
    }

    public function getType(): ?string
    {
        return $this->notificationType;
    }
}
