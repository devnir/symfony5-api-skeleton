<?php

declare(strict_types=1);

namespace Parfums\Api\EventListener;

use Doctrine\Common\Annotations\Reader;
use Parfums\Api\Annotations\RestController;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\KernelEvents;

class RequestSubscriber implements EventSubscriberInterface
{
    private const SUPPORTED_TYPES = ['json', 'application/json'];

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['onKernelRequest', 254],
        ];
    }


    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $request = $event->getRequest();
        if (!in_array($request->getContentType(), self::SUPPORTED_TYPES, true)) {
            return;
        }

        $content = $request->headers->has('data') ? $request->headers->get('data') : $request->getContent();
        $data = json_decode($content, true);
        if (false === $data) {
            throw new BadRequestHttpException('Request data is invalid');
        }

        $request->request->replace(is_array($data) ? $data : []);
    }
}
