<?php

namespace App\EventListener;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

class ExceptionListener
{
    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        $request = $event->getRequest();

        if (!\str_starts_with($request->getPathInfo(), '/api')) {
            return;
        }

        $statusCode = 500;
        $message = 'An error occurred';

        if ($exception instanceof HttpExceptionInterface) {
            $statusCode = $exception->getStatusCode();
            $message = $exception->getMessage();
        } elseif ($exception instanceof AuthenticationException) {
            $statusCode = 401;
            $message = $exception->getMessage();
        }

        $decodedMessage = json_decode($message, true);
        if (json_last_error() === JSON_ERROR_NONE && isset($decodedMessage['errors'])) {
            $response = new JsonResponse($decodedMessage, $statusCode);
        } else {
            $response = new JsonResponse([
                'errors' => [
                    [
                        'message' => $message
                    ]
                ]
            ], $statusCode);
        }

        $event->setResponse($response);
    }
}
