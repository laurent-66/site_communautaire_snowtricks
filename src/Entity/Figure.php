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
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @ORM\Entity(repositoryClass="App\Repository\FigureRepository")
 * @ORM\Table(name="figure")
 * @ORM\HasLifecycleCallbacks
 * @UniqueEntity("name", message = "Le nom de la figure déjà existant", groups="createFigure")
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
     * message = "La valeur ne peut être vide.", groups="base"
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
     *
     * @ORM\Column(type="string", nullable="true")
     */
    private $description;
/**
     * @ORM\Column(type="string", length=255)
     */
    private $coverImage;
/**
     * Undocumented variable
     * @var UploadedFile
     * @Assert\NotNull(
     * message = "La valeur ne peut être vide.", groups="createFigure"
     * )
     *
     * @Assert\Image(
     * maxSize = "1024k",
     * mimeTypes = {"image/jpeg", "image/jpg", "image/png"},
     * mimeTypesMessage = "Veuillez charger un fichier jpeg/jpg ou png", groups="base"
     * )
     *
     */
    private $coverImageFile;
/**
     *
     * @var string
     *
     *
     * @ORM\Column(type="string", length=255)
     *
     */
    private $alternativeAttribute;
/**
     * @var Datetime
     *
     * @ORM\column(type="datetime", nullable="true")
     */
    private $createdAt;
/**
     * @var Datetime
     *
     * @ORM\column(type="datetime")
     */
    private $updatedAt;
/**
     * @var Boolean
     *
     * @ORM\column(type="boolean")
     */
    private $fixture;
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
    private $illustrations;
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
    public function initializeSlug()
    {
        if (empty($this->slug)) {
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
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(?string $name): void
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
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    /**
     *
     * @return string
     */
    public function getCoverImage(): ?string
    {
        return $this->coverImage;
    }

    /**
     *
     * @param string $coverImage
     *
     */
    public function setCoverImage(?string $coverImage): void
    {
        $this->coverImage = $coverImage;
    }

    /**
     *
     * @return UploadedFile
     */
    public function getCoverImageFile(): ?UploadedFile
    {
        return $this->coverImageFile;
    }


    /**
     *
     * @param string $coverImageFile
     *
     */
    public function setCoverImageFile($coverImageFile): void
    {
        $this->coverImageFile = $coverImageFile;
    }


    /**
     *
     * @return string
     */
    public function getAlternativeAttribute(): ?string
    {
        return $this->alternativeAttribute;
    }

    /**
     *
     * @param string $alternativeAttribute
     *
     */
    public function setAlternativeAttribute(?string $alternativeAttribute): void
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
     * Undocumented function
     *
     * @return void
     */
    public function getFixture()
    {
        return $this->fixture;
    }


    /**
     * Undocumented function
     *
     * @param [type] $fixture
     * @return void
     */
    public function setFixture($fixture): void
    {
        $this->fixture = $fixture;
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
     * @return Collection
     */
    public function getIllustrations(): ?Collection
    {
        return $this->illustrations;
    }

    public function addIllustration(Illustration $illustration)
    {
        if (!$this->illustrations->contains($illustration)) {
            $this->illustrations->add($illustration);
        }
    }

    public function removeIllustration(Illustration $illustration): void
    {
        if ($this->illustrations->contains($illustration)) {
            $this->illustrations->removeElement($illustration);
        }
    }

    /**
     * Action sur le tableau des videos
     *
     * @return Collection
     */
    public function getVideos(): ?Collection
    {
        return $this->videos;
    }

    public function addVideo(Video $video)
    {
        if (!$this->videos->contains($video)) {
            $this->videos->add($video);
        }
    }

    public function removeVideo(Video $video): void
    {
        if ($this->videos->contains($video)) {
            $this->videos->removeElement($video);
        }
    }
}
