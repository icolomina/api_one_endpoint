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

    public function pushNotification(string $topic, array $data): void
    {
        $this->hub->publish(
            new Update(
                $topic,
                json_encode($data)
            )
        );
    }

    public function getName(): string
    {
        return 'mercure';
    }
}
