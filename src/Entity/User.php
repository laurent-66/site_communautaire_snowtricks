<?php
namespace App\Entity;
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
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
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

}