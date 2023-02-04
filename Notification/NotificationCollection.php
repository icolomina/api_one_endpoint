<?php

namespace Ict\ApiOneEndpoint\Notification;

use Ict\ApiOneEndpoint\Contract\Notification\NotificationHandlerInterface;

class NotificationCollection
{
    /**
     * @var array<string, NotificationHandlerInterface>
     */
    private array $notificationHandlers;

    public function __construct(iterable $notificationHandlers)
    {
        foreach ($notificationHandlers as $notificationHandler){
            $this->notificationHandlers[$notificationHandler->getName()] = $notificationHandler;
        }
    }

    public function getNotificationHandler(string $handlerName): ?NotificationHandlerInterface
    {
        return $this->notificationHandlers[$handlerName] ?? null;
    }

    public function hasNotificationHandler(string $handlerName): bool
    {
        return isset($this->notificationHandlers[$handlerName]);
    }
}
