<?php 

namespace App\Services;

use Symfony\Component\HttpFoundation\File\Exception\FileException;

//Save the image file on the server

class RegisterFileUploaded 
{

    public function __construct()
    {
        
    }
    public function registerFile($imageUploaded, string $newFilename, string $imagesDirectory) {
        
        try {
            $imageUploaded->move(
                $imagesDirectory,
                $newFilename
            );

        } catch (FileException $e) {
            dump($e);
        }

    } 

} 