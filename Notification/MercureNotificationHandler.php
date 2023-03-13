<?php

namespace Ict\ApiOneEndpoint\Notification;

use Ict\ApiOneEndpoint\Contract\Notification\NotificationHandlerInterface;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;

class MercureNotificationHandler implements NotificationHandlerInterface
{

    public function __construct(
        private readonly HubInterface $hub
    ){ }

    public function pushNotification(string $topic, string $data): void
    {
        $this->hub->publish(
            new Update(
                $topic,
                $data
            )
        );
    }

    public function getName(): string
    {
        return 'mercure';
    }
}
