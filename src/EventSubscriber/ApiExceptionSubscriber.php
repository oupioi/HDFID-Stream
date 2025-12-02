<?php

namespace App\EventSubscriber;

use App\Exception\MoviesApiException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Twig\Environment;

class ApiExceptionSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private Environment $twig,
    ) {}

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => 'onKernelException',
        ];
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if (!$exception instanceof MoviesApiException) {
            return;
        }

        $request = $event->getRequest();
        if ($request->getPreferredFormat() === 'json' || str_starts_with($request->getPathInfo(), '/api')) {
            $event->setResponse(new JsonResponse(['error' => $exception->getMessage()], 500));
            return;
        }

        $content = $this->twig->render('error/api_error.html.twig', [
            'message' => $exception->getMessage(),
        ]);

        $event->setResponse(new Response($content));
    }
}
