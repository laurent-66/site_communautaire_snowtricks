<?php 

use Slugger;

class UniqueIdImage {

    public static function generateUniqIdFileName($coverImage) {

        $originalFilename = pathinfo($coverImage->getClientOriginalName(), PATHINFO_FILENAME);

        $safeFilename = Slugger::generateSlug($originalFilename);

        $newFilename = $safeFilename.'-'.uniqid().'.'.$coverImage->guessExtension();

        return $newFilename;
    } 

}