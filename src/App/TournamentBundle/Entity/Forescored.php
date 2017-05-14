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
     * @var string
     *
     * @ORM\Column(name="hash", type="string")
     */
    private $hash;

    /**
     * @var int
     *
     * @ORM\Column(name="player", type="integer")
     */
    private $player;

    /**
     * @var string
     *
     * @ORM\Column(name="first", type="string", nullable=true)
     */
    private $first;

    /**
     * @var string
     *
     * @ORM\Column(name="second", type="string", nullable=true)
     */
    private $second;

    /**
     * @var string
     *
     * @ORM\Column(name="three", type="string", nullable=true)
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
     * Set hash
     *
     * @param string $hash
     *
     * @return Forescored
     */
    public function setHash($hash)
    {
        $this->hash = $hash;

        return $this;
    }

    /**
     * Get hash
     *
     * @return string
     */
    public function getHash()
    {
        return $this->hash;
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
     * @param string $first
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

}

