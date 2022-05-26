<?php 

//Autocomplete image attribute when this field is not completed

class AlternativeAttribute 
{

    public static function autoCompleteAttribute($entityImageUploaded, $originalFilename, $alternativeAttribute ) {
        
        if ($alternativeAttribute) {
            $entityImageUploaded->setAlternativeAttribute($alternativeAttribute);
        } else {
            $entityImageUploaded->setAlternativeAttribute($originalFilename);
        }

    } 

}