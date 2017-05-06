<?php

namespace App\TournamentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Forescored
 *
 * @ORM\Table(name="forescored")
 * @ORM\Entity(repositoryClass="App\TournamentBundle\Repository\ForescoredRepository")
 * @ORM\HasLifecycleCallbacks()  
 */
class Forescored
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
     * @ORM\Column(name="player", type="integer")
     */
    private $player;

    /**
     * @var int
     *
     * @ORM\Column(name="first", type="integer", nullable=true)
     */
    private $first;

    /**
     * @var int
     *
     * @ORM\Column(name="second", type="integer", nullable=true)
     */
    private $second;

    /**
     * @var int
     *
     * @ORM\Column(name="three", type="integer", nullable=true)
     */
    private $three;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created", type="datetime")
     */
    private $created;

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
     * @return Forescored
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
     * @return Forescored
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
     * Set player
     *
     * @param integer $player
     *
     * @return Forescored
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
     * Set first
     *
     * @param integer $first
     *
     * @return Forescored
     */
    public function setFirst($first)
    {
        $this->first = $first;

        return $this;
    }

    /**
     * Get first
     *
     * @return int
     */
    public function getFirst()
    {
        return $this->first;
    }

    /**
     * Set second
     *
     * @param integer $second
     *
     * @return Forescored
     */
    public function setSecond($second)
    {
        $this->second = $second;

        return $this;
    }

    /**
     * Get second
     *
     * @return int
     */
    public function getSecond()
    {
        return $this->second;
    }

    /**
     * Set three
     *
     * @param integer $three
     *
     * @return Forescored
     */
    public function setThree($three)
    {
        $this->three = $three;

        return $this;
    }

    /**
     * Get three
     *
     * @return int
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

}

