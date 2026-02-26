<?php

namespace App\EventListener;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Psr\Log\LoggerInterface;

class ExceptionListener
{
    public function __construct(
        private string $environment = 'dev',
        private ?LoggerInterface $logger = null
    ) {}

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        $request = $event->getRequest();

        if (!\str_starts_with($request->getPathInfo(), '/api')) {
            return;
        }

        $statusCode = 500;
        $message = 'An error occurred';
        $trace = null;

        if ($this->logger) {
            $this->logger->error('API Exception: ' . $exception::class, [
                'message' => $exception->getMessage(),
                'trace' => $exception->getTraceAsString()
            ]);
        }

        if ($exception instanceof HttpExceptionInterface) {
            $statusCode = $exception->getStatusCode();
            $message = $exception->getMessage();
        } elseif ($exception instanceof AuthenticationException) {
            $statusCode = 401;
            $message = $exception->getMessage();
        } elseif ('dev' === $this->environment) {
            $message = $exception->getMessage() ?: 'An error occurred';
            $trace = $exception->getTraceAsString();
        }

        $decodedMessage = json_decode($message, true);
        if (json_last_error() === JSON_ERROR_NONE && isset($decodedMessage['errors'])) {
            $response = new JsonResponse($decodedMessage, $statusCode);
        } else {
            $responseData = [
                'errors' => [
                    [
                        'message' => $message,
                        'exception' => 'dev' === $this->environment ? $exception::class : null
                    ]
                ]
            ];

            if ('dev' === $this->environment && $trace) {
                $responseData['trace'] = $trace;
            }

            $response = new JsonResponse($responseData, $statusCode);
        }

        $event->setResponse($response);
    }
}
