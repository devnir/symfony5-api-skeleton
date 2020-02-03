<?php

declare(strict_types=1);

namespace Parfums\Api\EventListener;

use Api\Exception\ValidationException;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\KernelEvents;

class ExceptionSubscriber implements EventSubscriberInterface
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => ['onKernelException', -10],
        ];
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $e = $event->getException();

        $statusCode = ($e instanceof HttpExceptionInterface)
            ? $e->getStatusCode()
            : Response::HTTP_INTERNAL_SERVER_ERROR;

        $headers = ($e instanceof HttpExceptionInterface)
            ? $e->getHeaders()
            : [];

        $body = [
            'code'    => $statusCode,
            'message' => (string) $e->getMessage(),
        ];

        if ($e instanceof ValidationException) {
            $body['errors'] = $this->violation($e->getErrors());
        }

        $event->setResponse(
            new JsonResponse($body, $statusCode, $headers)
        );

        if (false === $e instanceof HttpExceptionInterface) {
            $this->logger->critical(
                sprintf(
                    'Exception thrown when handling an exception (%s: %s at %s line %s)',
                    \get_class($e),
                    $e->getMessage(),
                    $e->getFile(),
                    $e->getLine()
                ),
                [
                    'exception' => $e,
                ]
            );
        }
    }

    /**
     * @param array $errors
     * @return array
     */
    public function violation(array $errors): array
    {
        $data = [];

        foreach ($errors as $error) {
            $field = strtolower(preg_replace('/[A-Z]/', '_\\0', $error->getPropertyPath()));
            if (!array_key_exists($field, $data)) {
                $data[$field] = [];
            }
            $data[$field][] = $error->getMessage();
        }

        return $data;
    }
}
