<?php

namespace App\TournamentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Userscored
 *
 * @ORM\Table(name="userscored")
 * @ORM\Entity(repositoryClass="App\TournamentBundle\Repository\UserscoredRepository")
 * @ORM\HasLifecycleCallbacks()  
 */
class Userscored
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
     * @ORM\Column(name="idfore", type="integer")
     */
    private $idfore;

    /**
     * @var int
     *
     * @ORM\Column(name="tr", type="integer")
     */
    private $tr;

    /**
     * @var int
     *
     * @ORM\Column(name="tour", type="integer")
     */
    private $tour;

    /**
     * @var int
     *
     * @ORM\Column(name="user", type="integer")
     */
    private $user;

    /**
     * @var int
     *
     * @ORM\Column(name="player", type="integer")
     */
    private $player;

    /**
     * @var string
     *
     * @ORM\Column(name="first", type="string", length=255, nullable=true)
     */
    private $first;

    /**
     * @var string
     *
     * @ORM\Column(name="second", type="string", length=255, nullable=true)
     */
    private $second;

    /**
     * @var string
     *
     * @ORM\Column(name="three", type="string", length=255, nullable=true)
     */
    private $three;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created", type="datetime")
     */
    private $created;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated", type="datetime", nullable=true)
     */
    private $updated;

    /**
     * @var int
     *
     * @ORM\Column(name="score", type="integer", nullable=true)
     */
    private $score;


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
     * Set tr
     *
     * @param integer $tr
     *
     * @return Userscored
     */
    public function setTr($tr)
    {
        $this->tr = $tr;

        return $this;
    }

    /**
     * Get tr
     *
     * @return int
     */
    public function getTr()
    {
        return $this->tr;
    }

    /**
     * Set tour
     *
     * @param integer $tour
     *
     * @return Userscored
     */
    public function setTour($tour)
    {
        $this->tour = $tour;

        return $this;
    }

    /**
     * Get tour
     *
     * @return int
     */
    public function getTour()
    {
        return $this->tour;
    }

    /**
     * Set user
     *
     * @param integer $user
     *
     * @return Userscored
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return int
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set player
     *
     * @param integer $player
     *
     * @return Userscored
     */
    public function setPlayer($player)
    {
        $this->player = $player;

        return $this;
    }

    /**
     * Get player
     *
     * @return int
     */
    public function getPlayer()
    {
        return $this->player;
    }

    /**
     * Set score
     *
     * @param integer $score
     *
     * @return Userscored
     */
    public function setScore($score)
    {
        $this->score = $score;

        return $this;
    }

    /**
     * Get score
     *
     * @return int
     */
    public function getScore()
    {
        return $this->score;
    }    

    /**
     * Set first
     *
     * @param string $first
     *
     * @return Userscored
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
     * @return Userscored
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
     * Set three
     *
     * @param string $three
     *
     * @return Userscored
     */
    public function setThree($three)
    {
        $this->three = $three;

        return $this;
    }

    /**
     * Get three
     *
     * @return string
     */
    public function getThree()
    {
        return $this->three;
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

     /**
     * @ORM\PreUpdate
     */
    public function setUpdated()
    {
        $this->updated = new \DateTime();
    } 

    /**
     * Get updated
     *
     * @return \DateTime
     */
    public function getUpdated()
    {
        return $this->updated;
    }        

    /**
     * Set idfore
     *
     * @param integer $idfore
     *
     * @return Usercast
     */
    public function setIdfore($idfore)
    {
        $this->idfore = $idfore;

        return $this;
    }

    /**
     * Get idfore
     *
     * @return int
     */
    public function getIdfore()
    {
        return $this->idfore;
    }    

}

