<?php 

namespace App\Services;

//Autocomplete image attribute when this field is not completed

class DeleteImageStored
{

    public static function deleteImage($fileName, $pathFile) {
        
        $filePath = $pathFile.'\\'.$fileName;
        unlink($filePath);

    } 

}