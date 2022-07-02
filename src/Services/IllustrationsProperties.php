<?php

namespace App\Services;

// Generates filtering properties for each illustration in the table

class IllustrationsProperties
{
    public static function generateProperties(array $arrayIllustration)
    {

        $arrayImages = [];

        $arrayIllustrationLength = count($arrayIllustration);

        for ($i = 0; $i < (int)$arrayIllustrationLength; $i++) {
            $id = $arrayIllustration[$i]->getId();
            $tag = "img";

            $uri_Illustration = $arrayIllustration[$i]->getUrlIllustration();
            $alternativeAttribute = $arrayIllustration[$i]->getAlternativeAttribute();


            $urlFixtureIllustration = stristr($uri_Illustration, "https");

            if ($urlFixtureIllustration) {
                $objectImage = ["path" => $uri_Illustration, "alternativeAttribute" => $alternativeAttribute, "type" => $tag, "fixture" => "true", "id" => $id ];
            } else {
                $url_illustration = "/uploads/illustrationsCollection/" . $uri_Illustration;

                $objectImage = ["path" => $url_illustration, "alternativeAttribute" => $alternativeAttribute, "type" => $tag, "fixture" => "false", "id" => $id ];
            }
            array_push($arrayImages, $objectImage);
        }

        return $arrayImages;
    }
}
