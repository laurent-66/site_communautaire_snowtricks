<?php
namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\FigureRepository")
 * @ORM\Table(name="figure")
 */
class Figure 
{
    /**
     * @var int
     * 
     * @ORM\Id()
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     * 
     * @ORM\Column(type="string")
     */
    protected $name;

    /**
     * @var string
     * 
     * @ORM\Column(type="string")
     */
    protected $description;

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
     * Relation bidirectionnelle Figure ManyToOne User et User ManyToOne Figure
     * @var User
     * 
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="figures")
     * @ORM\JoinColumn(name="author_id", referencedColumnName="id")
     */
    protected $author;

    /**
     * @var FigureGroup
     * 
     * @ORM\ManyToOne(targetEntity="App\Entity\FigureGroup")
     * @ORM\JoinColumn(name="figure_group_id", referencedColumnName="id")
     */
    protected $figureGroup;




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
    public function getAuthor(): User
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
    public function getFigureGroup(): FigureGroup
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