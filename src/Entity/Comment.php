<?php
namespace App\Entity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="comment")
 */
class Comment 
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
     * @var datetime
     * 
     * @ORM\Column(type="datetime")
     */
    protected $date;

    /**
     * @var string
     * 
     * @ORM\Column(type="string")
     */
    protected $content;

}