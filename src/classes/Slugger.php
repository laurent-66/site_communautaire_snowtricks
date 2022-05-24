<?php 

use Symfony\Component\String\Slugger\SluggerInterface;

class Slugger 
{

    // public function __construct(SluggerInterface $slugger)
    // {
    //     $this->slugger = $slugger;
    // }


    public static function slugify(string $filename, SluggerInterface $sluggerInterface )
    {

        // return $this->slugger->slug($filename);
        return $sluggerInterface->slug($filename);

    }

    public static function generateSlug(string $originalFilename, $slugger)
    {

        return self::slugify($originalFilename, $slugger); 

    }

}