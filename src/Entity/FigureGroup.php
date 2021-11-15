<?php
namespace App\Entity;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="figureGroup")
 */
class FigureGroup 
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
    protected $name;

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

}