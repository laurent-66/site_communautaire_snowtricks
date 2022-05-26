<?php 

// Generates filtering properties for each illustration in the table 

class IllustrationsProperties
{

    public static function generateProperties(array $arrayIllustration) {

        $arrayImages= [];
        
        $arrayIllustrationLength = count($arrayIllustration);
                
        for ($i = 0 ; $i < (int)$arrayIllustrationLength ; $i++) {

            $id = $arrayIllustration[$i]->getId();
            $tag = "img";

            $uri_Illustration = $arrayIllustration[$i]->getUrlIllustration();
            $urlFixtureIllustration = stristr($uri_Illustration,"https");

            if ( $urlFixtureIllustration ) {

                $objectImage = ["path"=> $uri_Illustration, "type" => $tag, "fixture" => "true", "id" => $id ];

            } else {

                $url_illustration = "/uploads/illustrationsCollection/".$uri_Illustration;

                $objectImage = ["path"=>$url_illustration, "type" => $tag, "fixture" => "false", "id" => $id ];
            }
            array_push($arrayImages, $objectImage);
        
        }  
        
        return $arrayImages;

    } 

}