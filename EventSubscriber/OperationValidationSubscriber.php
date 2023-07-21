<?php

namespace Ict\ApiOneEndpoint\EventSubscriber;

use Ict\ApiOneEndpoint\Exception\ContextOperationNotMatchException;
use Ict\ApiOneEndpoint\Exception\ContextOperationRequiredException;
use Ict\ApiOneEndpoint\Exception\OperationValidationException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class OperationValidationSubscriber implements EventSubscriberInterface
{

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => 'onKernelException'
        ];
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        if($exception instanceof OperationValidationException){
            $event->setResponse(new JsonResponse(
                [
                    'errors' => $exception->getErrors()
                ],
                Response::HTTP_BAD_REQUEST
            ));
        }

        if($exception instanceof ContextOperationRequiredException || $exception instanceof ContextOperationNotMatchException) {
            $event->setResponse(new JsonResponse(
                [
                    'errors' => [
                        'operation' => $exception->getMessage()
                    ]
                ],
                Response::HTTP_BAD_REQUEST
            ));
        }
    }
}
