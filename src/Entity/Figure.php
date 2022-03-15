<?php
namespace App\Entity;

use DateTime;
use App\Entity\Video;
use Cocur\Slugify\Slugify;
use App\Entity\Illustration;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\FigureRepository")
 * @ORM\Table(name="figure")
 * @ORM\HasLifecycleCallbacks
 * @UniqueEntity("name", message = "Le nom de la figure déjà existant")
 * 
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
     * @Assert\NotBlank( 
     * message = "La valeur ne peut être vide.",
     * )
     * 
     * 
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
     * @Assert\NotBlank( 
     * message = "La valeur ne peut être vide.",
     * )
     * 
     * 
     * @ORM\Column(type="string")
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $coverImage;

    /**
     *
     * @var string
     * 
     * @Assert\NotBlank( 
     * message = "La valeur ne peut être vide.",
     * )
     * 
     * @ORM\Column(type="string", length=255)
     * 
     */
    private $alternativeAttribute;

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
        $this->illustrations = new ArrayCollection();
        $this->videos = new ArrayCollection();
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
     * Relation Figure OneToMany Comment
     * 
     * @var Comment
     * 
     * @ORM\OneToMany(targetEntity="App\Entity\Comment", mappedBy="figure", cascade={"ALL"})
     * @ORM\JoinColumn(name="comment_id", referencedColumnName="id")
     */
    private $comment;


    /**
     * @var FigureGroup
     * 
     * @ORM\ManyToOne(targetEntity="App\Entity\FigureGroup")
     * @ORM\JoinColumn(name="figure_group_id", referencedColumnName="id")
     */
    private $figureGroup;


    /**
     * @var Collection
     * 
     * 
     * @Assert\Valid()
     * @ORM\OneToMany(targetEntity="App\Entity\Illustration", mappedBy="figure", cascade={"ALL"})
     * 
     */
    protected $illustrations;


    /**
     * @var Collection
     * 
     * @Assert\Valid()
     * @ORM\OneToMany(targetEntity="App\Entity\Video", mappedBy="figure", cascade={"ALL"})
     * 
     */
    private $videos;


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
     *
     * @return string
     */
    public function getAlternativeAttribute(): string
    {
        return $this->alternativeAttribute;
    }

    /**
     *
     * @param string $alternativeAttribute
     * 
     */
    public function setAlternativeAttribute(string $alternativeAttribute): void
    {
        $this->alternativeAttribute = $alternativeAttribute;

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
     * @return Comment 
     */
    public function getComment(): ?Comment
    {
        return $this->comment;
    }

    /**
     * @param Comment $comment
     */
    public function setComment(Comment $comment): void
    {
        $this->comment = $comment;
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

    /**
     * Action sur le tableau des illustrations
     *
     * @return ArrayCollection
     */
    public function getIllustrations(): ArrayCollection
    {
        return $this->illustrations;
    }

    public function addIllustration(Illustration $illustration)
    {
        if(!$this->illustrations->contains($illustration))
        {
            $this->illustrations->add($illustration);
        }
    }

    public function removeIllustration(Illustration $illustration)
    {
        if($this->illustrations->contains($illustration))
        {
            $this->illustrations->remove($illustration);
        }
    }

    /**
     * Action sur le tableau des videos
     *
     * @return ArrayCollection
     */
    public function getVideos(): ArrayCollection
    {
        return $this->videos;
    }

    public function addVideo(Video $video)
    {
        if(!$this->videos->contains($video))
        {
            $this->videos->add($video);
        }
    }
  
}