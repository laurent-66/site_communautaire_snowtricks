<?php
namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
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

    /**
     * @var User
     * 
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\Joincolumn(name="author_id", referencedColumnName="id")
     */
    protected $author;

    /**
     * @var FigureGroup
     * 
     * @ORM\ManyToOne(targetEntity="App\Entity\FigureGroup")
     * @ORM\Joincolumn(name="figure_group_id", referencedColumnName="id")
     */
    protected $figureGroup;

    public function __construct()
    {
        $this->createdAt = new DateTime();
        $this->updatedAt = new DateTime();
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

}