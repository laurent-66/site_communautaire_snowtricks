<?php
namespace App\Entity;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Validator\Constraints as SecurityAssert;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @ORM\Table(name="user")
 */
class User implements UserInterface, PasswordAuthenticatedUserInterface
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
     * @Assert\NotBlank( 
     * message = "La valeur ne peut être vide."
     * )
     * @ORM\Column(type="string")
     */
    protected $pseudo;

    /**
     * @var string
     * @Assert\NotBlank(
     * message = "La valeur ne peut être vide."
     * )
     * @Assert\Email(
     * message = "l'email '{{ value }}' n'est pas un email valide."
     * )
     * 
     * @ORM\Column(type="string")
     */
    protected $email;

    /**
     * @var string
     * @Assert\NotBlank(
     * message = "La valeur ne peut être vide."
     * )
     * 
     * @Assert\Length(min=8, minMessage="Votre mot de passe doit faire au moins 8 caractères !")
     * 
     * 
     * @ORM\Column(type="string")
     */
    protected $password;

    /**
     * @var string
     * 
     * @Assert\Url
     * @ORM\Column(type="string",nullable=true)
     */
    protected $urlPhoto;


    public function __construct()
    {
        $this->figures = new ArrayCollection();
        $this->createdAt = new DateTime();
        $this->updatedAt = new DateTime();
    }

    /**
     * Instance d'objet ArrayCollection déclarer dans constructeur
     *
     * @var Collection|Figure[]
     * 
     * @ORM\OneToMany(targetEntity="App\Entity\Figure", mappedBy="author")
     */
    protected $figures;


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
     * Undocumented variable
     *
     * @var string
     * 
     * @ORM\column(type="string", nullable=true)
     */
    protected $lastPasswordToken;


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
    public function getPseudo(): ?string
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
    public function getEmail(): ?string
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
    public function getPassword(): ?string
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
    public function getUrlPhoto(): ?string
    {
        return $this->urlPhoto;
    }

    /**
     * @param string $url_photo
     */
    public function setUrlPhoto(string $urlPhoto): void
    {
        $this->urlPhoto = $urlPhoto;
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

    /**Uniquement le getter pour récupérer la liste des figures */

    /**
     * @return Figure[]|Collection 
     */
    public function getFigures(): ArrayCollection
    {
        return $this->figures;
    }

    /** method d'ajout ou de modification reconnu par doctrine permettant de manipuler les collection */

    public function addFigure(Figure $figure)
    {
        /** si notre collection ne contient pas la figure alors enregistre la */
        /** method add, contains...etc sont issues de l'objet Collection */
        if(!$this->figures->contains($figure))
        {
            $this->figures->add($figure);
            $figure->setAuthor($this);
        }

    }

    public function removeFigure(Figure $figure)
    {
        /** si notre collection bien la figure alors on la supprime */
        /** method add, contains...etc sont issues de l'objet Collection */
        if($this->figures->contains($figure))
        {
            $this->figures->removeElement($figure);
        }
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    public function getLastPasswordToken() {

        return $this->lastPasswordToken;
        
    }

    /**
     * Undocumented function
     *
     * @param string $lastPasswordToken
     * @return void
     */
    public function setLastPasswordToken(string $lastPasswordToken) {

        $this->lastPasswordToken = $lastPasswordToken;
    }


    public function getRoles()
    {
        return ['ROLE_USER'];
    }
    public function getSalt()
    {
        return '';
    }
    
    public function eraseCredentials()
    {
        return;
    }
    public function getUsername()
    {
        return $this->email;
    }
    public function getUserIdentifier()
    {
        return $this->email;
    }

}