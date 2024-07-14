<?php

namespace App\Controller;

use App\Entity\MediaObject;
use App\Service\FileUploader;
use DateTimeImmutable;
use DateTimeZone;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapUploadedFile;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Exception\ValidationFailedException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class MediaController extends AbstractController
{   
    public function __construct(private EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    #[Route('/media', name: 'app_create_media' , methods:['POST'])]
    public function create(#[MapUploadedFile(
        constraints:new File(
                extensions: ['jpg' , 'jpeg' , 'png' , 'webp' , 'gif' , 'svg'] , 
                extensionsMessage: "l'extension du fichier est invalide {{ extension }}. Les extensions autorisé sont {{ extensions }}.",
                maxSize: "1M",
                maxSizeMessage: "le fichier est trop grand {{ size }} {{ suffix }}. La taille maximum par fichier est de {{ limit }} {{ suffix }}.",
                notFoundMessage: "Aucun fichier trouver"
        ),
    validationFailedStatusCode: Response::HTTP_UNSUPPORTED_MEDIA_TYPE
    )] UploadedFile $file, Request $request, FileUploader $fileUploader, ValidatorInterface $validator): MediaObject
    {   

        $now = new DateTimeImmutable('now' , new DateTimeZone('Europe/Paris'));
        $media = new MediaObject();
        $media
        ->setFilename($fileUploader->upload($file))
        ->setLink($request->isSecure() ? 'https' : 'http' . '://' . $request->getHttpHost() . "/uploads/medias/" . $media->getFilename())
        ->setFile($file)
        ->setCreatedAt($now)
        ;
        $this->em->persist($media);
        $this->em->flush();
        return $media;
    }



    #[Route("/media" , name: 'app_ressource_media' , methods:['GET'])]
    public function ressource(){

        return $this->em->getRepository(MediaObject::class)->findAll();
    }
}
