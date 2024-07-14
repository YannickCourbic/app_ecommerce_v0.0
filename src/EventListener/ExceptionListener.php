<?php

namespace App\EventListener;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\KernelEvents;

final class ExceptionListener
{    

    #[AsEventListener(event: KernelEvents::EXCEPTION)]
    public function onKernelException(ExceptionEvent $event): void
    {   
        $exception = [];
        $error = $event->getThrowable();
        dump($error);
        if ($event->getThrowable() instanceof HttpExceptionInterface) {
            $statusCode = $error->getStatusCode();
            $exception['status'] = $statusCode;
        }
        switch (get_class($event->getThrowable())) {
            case 'Symfony\Component\HttpKernel\Exception\UnsupportedMediaTypeHttpException':
                $exception['message'] = $error->getMessage();
                $exception['exception'] = get_class($event->getThrowable());
                break;
            
            case 'Symfony\Component\Validator\Exception\ValidationFailedException': 
                $exception['messages'] = $error->getMessage();
                $exception['exception'] = get_class($event->getThrowable());
                break;
            case "Symfony\Component\HttpKernel\Exception\BadRequestHttpException":
                $exception['messages'] = $error->getMessage();
                $exception['exception'] = get_class($event->getThrowable());
                break;

            case "TypeError":
                if($error->getFile() === "C:\Users\Yanni\OneDrive\Bureau\projet symfony api 2024\app_ecommerce_v0.0\src\Controller\MediaController.php"){
                    throw new FileNotFoundException("Aucun fichier trouver");
                }
            case "Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException":
                $exception['status'] = 415;
                $exception['messages'] = $error->getMessage();
                $exception['exception'] = get_class($event->getThrowable());
                break;
            default:
                // throw new BadRequestHttpException('Aucun fichier envoyé dans la requête');
                break;
        }
        $event->setResponse( new JsonResponse($exception , count($exception) > 0 ? $exception['status'] : Response::HTTP_BAD_REQUEST));
    }
}
