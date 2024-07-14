<?php
namespace App\EventListener;

use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Exception\ValidationFailedException;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Psr\Log\LoggerInterface;

#[AsDoctrineListener('prePersist')]
class ValidateListener {

    private ValidatorInterface $validator;
    private LoggerInterface $logger;

    public function __construct(ValidatorInterface $validator, LoggerInterface $logger)
    {
        $this->validator = $validator;
        $this->logger = $logger;
    }

    public function prePersist(PrePersistEventArgs $args): void {
        try {
            // Je récupère l'entité avant le persist
            $entity = $args->getObject();
            $errors = $this->validator->validate($entity);
            if (count($errors) > 0) {
                $this->handleValidationErrors($errors);
            }
        } catch (\Exception $e) {
            // Log the exception message
            $this->logger->error('An error occurred during prePersist: ' . $e->getMessage());
            // Optionally rethrow or handle the exception
            throw $e;
        }
    }

    private function handleValidationErrors(ConstraintViolationListInterface $errors): void {
        $errorMessage = '';
        foreach ($errors as $error) {
            $errorMessage .= $error->getPropertyPath() . ': ' . $error->getMessage() . "\n";
        }
        throw new ValidationFailedException($errorMessage, $errors);
    }
}
