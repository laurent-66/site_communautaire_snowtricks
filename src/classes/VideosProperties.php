<?php 

// Generates filter properties for each video in the table 

class VideosProperties
{

    public static function generateProperties(array $arrayVideo) {

        $arrayVideos = [];
        
        $arrayVideoLength = count($arrayVideo);
                
        for ($i = 0 ; $i < (int)$arrayVideoLength ; $i++) {
            $id = $arrayVideo[$i]->getId();
            $url_video = $arrayVideo[$i]->getUrlVideo();
            $tag = "iframe";
            $objectVideo = ["path"=>$url_video, "type"=> $tag , "id"=>$id];
            array_push($arrayVideos, $objectVideo);     
        }  

        return $arrayVideos;

    } 

}