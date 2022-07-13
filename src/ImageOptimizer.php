<?php

namespace App;

use Imagine\Image\Box;
use Imagine\Gd\Imagine;
use Imagine\Image\Point;

class ImageOptimizer
{
    private const MAX_WIDTH = 900;
    private const MAX_HEIGHT = 700;


    private $imagine;

    public function __construct()
    {
        $this->imagine = new Imagine();
    }

    public function resize(string $filename): void
    {
        list($iwidth, $iheight) = getimagesize($filename);
        $ratio = $iwidth / $iheight;
        $width = self::MAX_WIDTH;
        $height = self::MAX_HEIGHT;
        if ($width / $height > $ratio) {
            $width = $height * $ratio;
        } else {
            $height = $width / $ratio; 
        }

        $this->imagine->open($filename)
                        ->resize(new Box($width , $height))
                        ->crop(new Point( 50, 0), new Box(800, 600))
                        ->save($filename);

    }
}

