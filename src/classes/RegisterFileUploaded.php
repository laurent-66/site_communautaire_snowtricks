<?php 

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;


class RegisterFileUploaded extends AbstractController
{

    public function __construct()
    {
        
    }
    
    public function registerFile($coverImage, $newFilename) {

        $fileobject = new RegisterFileUploaded ;

        try {
            $coverImage->move(
                $this->getParameter('images_directory'),
                $newFilename
            );

        } catch (FileException $e) {
            dump($e);
        }

    } 

}