<?php
namespace App\Entity;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="user")
 */
class User 
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
    protected $pseudo;

    /**
     * @var string
     * 
     * @ORM\Column(type="string")
     */
    protected $email;

    /**
     * @var string
     * 
     * @ORM\Column(type="string")
     */
    protected $password;

    /**
     * @var string
     * 
     * @ORM\Column(type="string")
     */
    protected $url_photo;

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
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string 
     */
    public function getPseudo(): string
    {
        return $this->pseudo;
    }

    /**
     * @param string $pseudo
     */
    public function setPseudo(string $pseudo): void
    {
        $this->pseudo = $pseudo;
    }

    /**
     * @return string 
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }


    /**
     * @return string 
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param string $password
     */
    public function setPassword(string $password): void
    {
        $this->password = $password;
    }


    /**
     * @return string 
     */
    public function getUrl_photo(): string
    {
        return $this->url_photo;
    }

    /**
     * @param string $url_photo
     */
    public function setUrl_photo(string $url_photo): void
    {
        $this->url_photo = $url_photo;
    }
}