<?php

namespace App;

use Imagine\Image\Box;
use Imagine\Gd\Imagine;
use Imagine\Image\Point;

class ImageOptimizer
{
    private const MAX_WIDTH = 800;
    private const MAX_HEIGHT = 800;


    private $imagine;

    public function __construct()
    {
        $this->imagine = new Imagine();
    }

    public function resize(string $filename): void
    {
        list($iwidth, $iheight) = getimagesize($filename);
        $coordCenterX = $iwidth/2;
        $coordCenterY = $iheight/2;

        $ratio = $iwidth / $iheight;
        $width = self::MAX_WIDTH;
        $height = self::MAX_HEIGHT;
        if ($width / $height > $ratio) {
            $width = $height * $ratio;
        } else {
            $height = $width / $ratio; 
        }

        // $photo = $this->imagine->open($filename);
        // $photo->resize(new Box($width, $height))->save($filename);

        $this->imagine->open($filename)
                        ->resize(new Box(self::MAX_WIDTH, self::MAX_HEIGHT))
                        ->crop(new Point( 0, 100), new Box(800, 600))
                        ->save($filename);

    }
}

