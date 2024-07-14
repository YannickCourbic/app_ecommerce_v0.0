<?php

namespace App\Entity;

use App\Repository\MediaObjectRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Serializer\Attribute\Context;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: MediaObjectRepository::class)]
class MediaObject
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'SEQUENCE')]
    #[ORM\Column]
    #[Groups(['create' , 'modify' , 'detail' , 'list'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['create' , 'modify' , 'detail' , 'list'])]
    private ?string $filename = null;

    #[ORM\Column(length: 255)]
    #[Groups(['create' , 'modify' , 'detail' , 'list'])]
    private ?string $link = null;

    #[ORM\Column]    
    #[Groups(['create' , 'modify' , 'detail' , 'list'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    #[Groups([ 'modify' , 'detail' , 'list'])]
    private ?\DateTimeImmutable $updatedAt = null;

    private UploadedFile $file;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFilename(): ?string
    {
        return $this->filename;
    }

    public function setFilename(string $filename): static
    {
        $this->filename = $filename;

        return $this;
    }

    public function getLink(): ?string
    {
        return $this->link;
    }

    public function setLink(string $link): static
    {
        $this->link = $link;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getFile(): ?UploadedFile{
        return $this->file;
    }

    public function setFile(UploadedFile $file): static{
        $this->file = $file;
        return $this;
    }
}
