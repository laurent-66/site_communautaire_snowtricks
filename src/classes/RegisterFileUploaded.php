<?php 

use Symfony\Component\HttpFoundation\File\Exception\FileException;

class RegisterFileUploaded 
{

    public static function registerFile($coverImage, $newFilename, $abstractController) {
        
        try {
            $coverImage->move(
                $abstractController,
                $newFilename
            );

        } catch (FileException $e) {
            dump($e);
        }

    } 

}