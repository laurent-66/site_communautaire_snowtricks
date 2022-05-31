<?php 

use Symfony\Component\String\Slugger\SluggerInterface;

class UniqueIdImage {


    public static function uniqIdCoverImage($coverImage, $originalFilename, SluggerInterface $slugger ) {

        $safeFilename = $slugger->slug($originalFilename);
        $newFilename = $safeFilename.'-'.uniqid().'.'.$coverImage->guessExtension();

        return $newFilename;
    } 


    public static function uniqIdIllustration($illustration, $originalFilename, SluggerInterface $slugger ) {

        $safeFilename = $slugger->slug($originalFilename);
        $newFilename = $safeFilename.'-'.uniqid().'.'.$illustration->getFileIllustration()->guessExtension();

        return $newFilename;
    } 











}