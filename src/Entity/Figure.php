<?php
namespace App\Entity;
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
     * @var string
     * 
     * @ORM\Column(type="string")
     */
    protected $description;

}