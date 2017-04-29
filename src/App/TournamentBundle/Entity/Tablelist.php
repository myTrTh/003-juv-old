<?php

namespace App\TournamentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Tablelist
 *
 * @ORM\Table(name="tablelist")
 * @ORM\Entity(repositoryClass="App\TournamentBundle\Repository\TablelistRepository")
 * @ORM\HasLifecycleCallbacks()   
 */
class Tablelist
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
     * @ORM\Column(name="user", type="integer")
     */
    private $user;

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
     * @ORM\Column(name="groups", type="integer", nullable=true)
     */
    private $groups;

    /**
     * @var int
     *
     * @ORM\Column(name="off", type="integer", nullable=true)
     */
    private $off;

    /**
     * @var int
     *
     * @ORM\Column(name="game", type="integer", nullable=true)
     */
    private $game;

    /**
     * @var int
     *
     * @ORM\Column(name="howgame", type="integer", nullable=true)
     */
    private $howgame;    

    /**
     * @var int
     *
     * @ORM\Column(name="w", type="integer", nullable=true)
     */
    private $w;

    /**
     * @var int
     *
     * @ORM\Column(name="n", type="integer", nullable=true)
     */
    private $n;

    /**
     * @var int
     *
     * @ORM\Column(name="l", type="integer", nullable=true)
     */
    private $l;

    /**
     * @var int
     *
     * @ORM\Column(name="bw", type="integer", nullable=true)
     */
    private $bw;

    /**
     * @var int
     *
     * @ORM\Column(name="bl", type="integer", nullable=true)
     */
    private $bl;

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
     * Set user
     *
     * @param integer $user
     *
     * @return Tablelist
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
     * Set tr
     *
     * @param integer $tr
     *
     * @return Tablelist
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
     * @return Tablelist
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
     * Set groups
     *
     * @param integer $groups
     *
     * @return Tablelist
     */
    public function setGroups($groups)
    {
        $this->groups = $groups;

        return $this;
    }

    /**
     * Get groups
     *
     * @return int
     */
    public function getGroups()
    {
        return $this->groups;
    }

/**
     * Set off
     *
     * @param integer $off
     *
     * @return Tablelist
     */
    public function setOff($off)
    {
        $this->off = $off;

        return $this;
    }

    /**
     * Get off
     *
     * @return int
     */
    public function getOff()
    {
        return $this->off;
    }    

    /**
     * Set game
     *
     * @param integer $game
     *
     * @return Tablelist
     */
    public function setGame($game)
    {
        $this->game = $game;

        return $this;
    }

    /**
     * Get game
     *
     * @return int
     */
    public function getGame()
    {
        return $this->game;
    }

    /**
     * Set howgame
     *
     * @param integer $howgame
     *
     * @return Tablelist
     */
    public function setHowgame($howgame)
    {
        $this->howgame = $howgame;

        return $this;
    }

    /**
     * Get howgame
     *
     * @return int
     */
    public function getHowgame()
    {
        return $this->howgame;
    }    

    /**
     * Set w
     *
     * @param integer $w
     *
     * @return Tablelist
     */
    public function setW($w)
    {
        $this->w = $w;

        return $this;
    }

    /**
     * Get w
     *
     * @return integer
     */
    public function getW()
    {
        return $this->w;
    }

    /**
     * Set n
     *
     * @param integer $n
     *
     * @return Tablelist
     */
    public function setN($n)
    {
        $this->n = $n;

        return $this;
    }

    /**
     * Get n
     *
     * @return int
     */
    public function getN()
    {
        return $this->n;
    }

    /**
     * Set l
     *
     * @param integer $l
     *
     * @return Tablelist
     */
    public function setL($l)
    {
        $this->l = $l;

        return $this;
    }

    /**
     * Get l
     *
     * @return int
     */
    public function getL()
    {
        return $this->l;
    }

    /**
     * Set bw
     *
     * @param integer $bw
     *
     * @return Tablelist
     */
    public function setBw($bw)
    {
        $this->bw = $bw;

        return $this;
    }

    /**
     * Get bw
     *
     * @return int
     */
    public function getBw()
    {
        return $this->bw;
    }

    /**
     * Set bl
     *
     * @param integer $bl
     *
     * @return Tablelist
     */
    public function setBl($bl)
    {
        $this->bl = $bl;

        return $this;
    }

    /**
     * Get bl
     *
     * @return int
     */
    public function getBl()
    {
        return $this->bl;
    }

    /**
     * Set score
     *
     * @param integer $score
     *
     * @return Tablelist
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
}

