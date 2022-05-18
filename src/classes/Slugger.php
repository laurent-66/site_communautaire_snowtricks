<?php 

use Symfony\Component\String\Slugger\SluggerInterface;

class Slugger 
{

    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }


    public static function slugify(string $filename)
    {

        // return $this->slugger->slug($filename);

    }

    public static function generateSlug(string $originalFilename)
    {

        return self::slugify($originalFilename);

    }

}