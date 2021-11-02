<?php
namespace App\Entity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="video")
 */
class Video 
{
    /**
     * @var int
     * 
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    protected $id;


    /**
     * @var string
     * 
     * @ORM\Column(type="string")
     */
    protected $url_video;

    /**
     * @var boolean
     * 
     * @ORM\Column(type="boolean")
     */
    protected $embed;
}