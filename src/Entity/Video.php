<?php
namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\BooleanType;

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

        /**
     * @var Datetime 
     * 
     * @ORM\column(type="datetime")
     */
    protected $createdAt;

    /**
     * @var Datetime 
     * 
     * @ORM\column(type="datetime")
     */
    protected $updatedAt;

    public function __construct()
    {
        $this->createdAt = new DateTime();
        $this->updatedAt = new DateTime();
    }


    /**
     * @var Figure
     * 
     * @ORM\ManyToOne(targetEntity="App\Entity\Figure")
     * @ORM\Joincolumn(name="figure_id", referencedColumnName="id")
     */
    protected $figure;


    /* getter and setter */

    /**
     * @return int 
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string 
     */
    public function getUrl_video(): string
    {
        return $this->url_video;
    }

    /**
     * @param string $url_video
     */
    public function setUrl_video(string $url_video): void
    {
        $this->url_video = $url_video;
    }

    /**
     * @return boolean 
     */
    public function getEmbed(): bool
    {
        return $this->url_video;
    }

    /**
     * @param boolean $embed
     */
    public function setEmbed(bool $embed): void
    {
        $this->embed = $embed;
    }
}