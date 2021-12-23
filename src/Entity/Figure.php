<?php
namespace App\Entity;

use DateTime;
use Cocur\Slugify\Slugify;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\String\Slugger\AsciiSlugger;

/**
 * @ORM\Entity(repositoryClass="App\Repository\FigureRepository")
 * @ORM\Table(name="figure")
 * @ORM\HasLifecycleCallbacks
 */
class Figure 
{
    public const LIMIT_PER_PAGE = 8;

    /**
     * @var int
     * 
     * @ORM\Id()
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     * 
     * @ORM\Column(type="string")
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $slug;

    /**
     * @var string
     * 
     * @ORM\Column(type="string")
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $coverImage;

    /**
     * @var Datetime 
     * 
     * @ORM\column(type="datetime")
     */
    private $createdAt;

    /**
     * @var Datetime 
     * 
     * @ORM\column(type="datetime")
     */
    private $updatedAt;

    public function __construct()
    {
        $this->createdAt = new DateTime();
        $this->updatedAt = new DateTime();
    }

    /**
     * Relation bidirectionnelle Figure ManyToOne User et User ManyToOne Figure
     * @var User
     * 
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="figure")
     * @ORM\JoinColumn(name="pseudo_id", referencedColumnName="id")
     */
    private $author;

    /**
     * @var FigureGroup
     * 
     * @ORM\ManyToOne(targetEntity="App\Entity\FigureGroup")
     * @ORM\JoinColumn(name="figure_group_id", referencedColumnName="id")
     */
    private $figureGroup;


    /** Method Entity lifecycle */

    /**
     * Permet d'initialiser le slug ! (annotation cycle de vie orm doctrine)
     * 
     * @ORM\PrePersist
     * @ORM\PreUpdate
     *
     * @return void
     */
    public function initializeSlug() {
        if(empty($this->slug)) {
            $slugify = new Slugify();
            $this->slug = $slugify->slugify($this->name);
        }
    }

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
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     *
     * @return string
     */
    public function getSlug(): string
    {
        return $this->slug;
    }

    /**
     *
     * @param string $slug
     * 
     */
    public function setSlug(string $slug): void
    {
        $this->slug = $slug;

    }

    /**
     * @return string 
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    /**
     *
     * @return string
     */
    public function getCoverImage(): string
    {
        return $this->coverImage;
    }

    /**
     *
     * @param string $coverImage
     * 
     */
    public function setCoverImage(string $coverImage): void
    {
        $this->coverImage = $coverImage;

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
     * @return User 
     */
    public function getAuthor(): ?User
    {
        return $this->author;
    }

    /**
     * @param User $author
     */
    public function setAuthor(User $author): void
    {
        $this->author = $author;
    }

    /**
     * @return FigureGroup 
     */
    public function getFigureGroup(): ?FigureGroup
    {
        return $this->figureGroup;
    }

    /**
     * @param FigureGroup $figureGroup
     */
    public function setFigureGroup(FigureGroup $figureGroup): void
    {
        $this->figureGroup = $figureGroup;
    }

}