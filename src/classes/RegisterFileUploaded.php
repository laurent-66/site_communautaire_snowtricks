<?php 

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class RegisterFileUploaded extends AbstractController
{

    public static function registerFile($coverImage, $newFilename) {
        
        try {
            $coverImage->move(
                AbstractController::getParameter('images_directory'),
                $newFilename
            );

        } catch (FileException $e) {
            dump($e);
        }

    } 

}