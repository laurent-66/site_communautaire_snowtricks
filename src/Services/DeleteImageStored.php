<?php 

namespace App\Services;

//Deleting the image file stored on the server

class DeleteImageStored
{

    public static function deleteImage($fileName, $pathFile) {
        
        $filePath = $pathFile.'\\'.$fileName;
        unlink($filePath);

    } 

}