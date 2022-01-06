<?php
namespace App\Entity;
use DateTime;
use App\Entity\Figure;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\IllustrationRepository")
 * @ORM\Table(name="illustration")
 */
class Illustration 
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
    protected $urlIllustration;


    protected $file;

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
    public function getUrlIllustration(): string
    {
        return $this->urlIllustration;
    }

    /**
     * @param string $urlIllustration
     */
    public function setUrlIllustration(string $urlIllustration): void
    {
        $this->urlIllustration = $urlIllustration;
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


    /**
     * @return 
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @param 
     */
    public function setFile($file): void
    {
        $this->file = $file;
    }





}