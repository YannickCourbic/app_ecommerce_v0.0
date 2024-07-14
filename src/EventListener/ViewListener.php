<?php

namespace App\EventListener;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class ViewListener
{   

    public function __construct(private SerializerInterface $serializer, private ValidatorInterface $validator)
    {
        $this->serializer = $serializer;
        $this->validator = $validator;
    }

    #[AsEventListener(event: KernelEvents::VIEW)]
    public function onKernelView(ViewEvent $event): void
    {   
        $response = [];
        switch ($event->getRequest()->getMethod()) {
            case 'POST':
                $response['status'] = Response::HTTP_CREATED;
                $response['message'] = "vous avez créer avec succès un/une " . str_replace('App\\Entity\\' , '' ,  get_class($event->getControllerResult()));
                $response['result'] =   json_decode($this->serializer->serialize($event->getControllerResult() , 'json' , ['groups' => ['create']]));
                break;
            case 'GET':
                $response['status'] = Response::HTTP_OK;
                $response['message'] = "Vous avez récupérer les ressources avec succès.";
                $response['results'] = json_decode($this->serializer->serialize($event->getControllerResult() , 'json' , ['groups' => ['list', 'detail']]));
                break;
            default:
               
                break;
        }

        $event->setResponse(new JsonResponse($response , $response['status']));
    }

}
