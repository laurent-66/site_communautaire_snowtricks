<?php 

namespace App\Services;

// This class allows to recognize the type of Youtube url and to select only its identification code

class Youtube
{

    public static function typeUrl(string $urlVideo) {
        
        if ( stristr($urlVideo,"embed") ) {

            $attrSrc = stristr($urlVideo, 'embed/');
            $codeYoutube = substr($attrSrc, 6, 11);

        }else{

            $codeYoutube = substr($urlVideo, -11);

        }

        return $codeYoutube;

    } 

}