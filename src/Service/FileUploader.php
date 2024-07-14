<?php
namespace App\Service;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class FileUploader{

    public function __construct(
        private string $targetDirectory,
        private SluggerInterface $slugger
    )
    {}

    public function upload(UploadedFile $file){
        // dd($file, $this->getTargetDirectory() );
        $originalFilename = pathinfo($file->getClientOriginalName() , PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFilename, '-');
        $newFilename = $safeFilename .'-'. uniqid() . '.'. $file->guessExtension();
        try {
            $file->move($this->getTargetDirectory(), $newFilename);
        } catch (FileException $e) {
            throw new FileException("Une erreur est survenue lors de l'upload de l'image." . $e->getMessage());
        }

        return $newFilename;
    }

    private function getTargetDirectory():string{
        return $this->targetDirectory;
    }


}