<?php

namespace App\Entity;

use DateTime;
use App\Entity\Figure;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\VideoRepository")
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
     * @Assert\NotBlank(
     * message = "La valeur ne peut être vide.", groups="base"
     * )
     *
     * @ORM\Column(type="string")
     */
    protected $urlVideo;
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
     * @ORM\JoinColumn(name="figure_id", referencedColumnName="id")
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
     * @return string
     */
    public function getUrlVideo(): ?string
    {
        return $this->urlVideo;
    }

    /**
     * @param string $url_video
     */
    public function setUrlVideo(?string $urlVideo): void
    {
        $this->urlVideo = $urlVideo;
    }


    /**
     * @return Datetime
     */
    public function getCreatedAt(): Datetime
    {
        return $this->createdAt;
    }

    /**
     * @param Datetime $createdAt
     */
    public function setCreatedAt(Datetime $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return Datetime
     */
    public function getUpdatedAt(): Datetime
    {
        return $this->updatedAt;
    }

    /**
     * @param Datetime $updatedAt
     */
    public function setUpdatedAt(Datetime $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * @return Figure
     */
    public function getFigure()
    {
        return $this->figure;
    }

    /**
     * @param Figure $figure
     */
    public function setFigure(Figure $figure): void
    {
        $this->figure = $figure;
    }
}
