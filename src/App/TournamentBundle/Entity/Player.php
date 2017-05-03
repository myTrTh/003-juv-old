<?php

namespace App\TournamentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Player
 *
 * @ORM\Table(name="player")
 * @ORM\Entity(repositoryClass="App\TournamentBundle\Repository\PlayerRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Player
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="team", type="integer")
     */
    private $team;

    /**
     * @var string
     *
     * @ORM\Column(name="first", type="string", length=255, nullable=true)
     */
    private $first;

    /**
     * @var string
     *
     * @ORM\Column(name="second", type="string", length=255)
     */
    private $second;

    /**
     * @var int
     *
     * @ORM\Column(name="position", type="integer")
     */
    private $position;

    /**
     * @ORM\Column(name="image", type="string", length=255, nullable=true)
     * @Assert\File(mimeTypes={ "image/png", "image/jpeg", "image/gif" })
     * @Assert\File(maxSize="50k")
     *
    */    
    private $image;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created", type="datetime")
     */
    private $created;

    /**
     * @var int
     *
     * @ORM\Column(name="status", type="integer")
     */
    private $status;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set team
     *
     * @param integer $team
     *
     * @return Player
     */
    public function setTeam($team)
    {
        $this->team = $team;

        return $this;
    }

    /**
     * Get team
     *
     * @return int
     */
    public function getTeam()
    {
        return $this->team;
    }

    /**
     * Set first
     *
     * @param string $first
     *
     * @return Player
     */
    public function setFirst($first)
    {
        $this->first = $first;

        return $this;
    }

    /**
     * Get first
     *
     * @return string
     */
    public function getFirst()
    {
        return $this->first;
    }

    /**
     * Set second
     *
     * @param string $second
     *
     * @return Player
     */
    public function setSecond($second)
    {
        $this->second = $second;

        return $this;
    }

    /**
     * Get second
     *
     * @return string
     */
    public function getSecond()
    {
        return $this->second;
    }

    /**
     * Set position
     *
     * @param integer $position
     *
     * @return Player
     */
    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Get position
     *
     * @return int
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Set image
     *
     * @param string $image
     *
     * @return Player
     */
    public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image
     *
     * @return string
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Set status
     *
     * @param integer $status
     *
     * @return Player
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

     /**
     * @ORM\PrePersist
     */
    public function setCreated()
    {
        $this->created = new \DateTime();
    } 

    /**
     * Get created
     *
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }    
}

