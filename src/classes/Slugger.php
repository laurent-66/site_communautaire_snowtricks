<?php 

use Symfony\Component\String\Slugger\SluggerInterface;

class Slugger 
{

    public static function slugify(string $filename, SluggerInterface $sluggerInterface )
    {

        return $sluggerInterface->slug($filename);

    }

    public static function generateSlug(string $originalFilename, $slugger)
    {

        return self::slugify($originalFilename, $slugger); 

    }

}