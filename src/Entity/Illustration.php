<?php
namespace App\Entity;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
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
    protected $url_illustration;

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
    public function getUrl_illustration(): string
    {
        return $this->url_illustration;
    }

    /**
     * @param string $url_illustration
     */
    public function setUrl_illustration(string $url_illustration): void
    {
        $this->url_illustration = $url_illustration;
    }

}