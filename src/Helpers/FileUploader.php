<?php

namespace Application\Helpers;

class FileUploader
{
    public const DEFAULT_UPLOAD_DIR = __DIR__ . 'public/uploads/';

    public static function uploadFile($file, string $pathDirectory = self::DEFAULT_UPLOAD_DIR)
    {
        $newFilename = md5(uniqid()) . '_' . $file['name'];
        $fullPath = $pathDirectory . $newFilename;

        return [

            'isSuccess' => move_uploaded_file($file['tmp_name'], $fullPath),
            'filename' => $newFilename
        ];
    }
}