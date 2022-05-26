<?php 

use Slugger;

class UniqueIdImage {

    public static function generateUniqIdFileName($coverImage, $slugger) {

        $originalFilename = pathinfo($coverImage->getClientOriginalName(), PATHINFO_FILENAME);

        $safeFilename = Slugger::generateSlug($originalFilename, $slugger);

        $newFilename = $safeFilename.'-'.uniqid().'.'.$coverImage->guessExtension();

        return $newFilename;
    } 

} 