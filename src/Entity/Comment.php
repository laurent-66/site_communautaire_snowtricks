<?php
namespace App\Entity;
use DateTime;
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
     * @var Datetime
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
     * @return Datetime 
     */
    public function getDate(): Datetime
    {
        return $this->date;
    }

    /**
     * @param Datetime $date
     */
    public function setDate(Datetime $date): void
    {
        $this->date = $date;
    }


    /**
     * @return text 
     */
    public function getContent(): text
    {
        return $this->content;
    }

    /**
     * @param text $content
     */
    public function setContent(text $content): void
    {
        $this->content = $content;
    }
}