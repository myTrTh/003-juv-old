<?php

namespace App\TournamentBundle\Entity;
use Symfony\Component\Validator\Constraints as Assert;

use Doctrine\ORM\Mapping as ORM;

/**
 * Forecast
 *
 * @ORM\Table(name="forecast")
 * @ORM\Entity(repositoryClass="App\TournamentBundle\Repository\ForecastRepository")
 * @ORM\HasLifecycleCallbacks()  
 */
class Forecast
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
     * @ORM\Column(name="hash", type="string", length=255)
     */
    private $hash;

    /**
     * @var string
     *
     * @ORM\Column(name="team1", type="string", length=255, nullable=true)
     */
    private $team1;

    /**
     * @var string
     *
     * @ORM\Column(name="team2", type="string", length=255, nullable=true)
     */
    private $team2;

    /**
     * @var int
     *
     * @ORM\Column(name="result1", type="integer", nullable=true)
     */
    private $result1;

    /**
     * @var int
     *
     * @ORM\Column(name="result2", type="integer", nullable=true)
     */
    private $result2;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="timer", type="datetime", nullable=true)
     * @Assert\NotBlank()     
     */
    private $timer;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created", type="datetime")
     */
    private $created;


    /**
     * @var int
     *
     * @ORM\Column(name="added", type="integer", nullable=true)
     */
    private $added;

    /**
     * @var string
     *
     * @ORM\Column(name="access", type="string", length=255, nullable=true)
     */
    private $access;

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
     * @return Forecast
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
     * Set team1
     *
     * @param string $team1
     *
     * @return Forecast
     */
    public function setTeam1($team1)
    {
        $this->team1 = $team1;

        return $this;
    }

    /**
     * Get team1
     *
     * @return string
     */
    public function getTeam1()
    {
        return $this->team1;
    }

    /**
     * Set access
     *
     * @param string $access
     *
     * @return Forecast
     */
    public function setAccess($access)
    {
        $this->team1 = $access;

        return $this;
    }

    /**
     * Get access
     *
     * @return string
     */
    public function getAccess()
    {
        return $this->access;
    }    

    /**
     * Set team2
     *
     * @param string $team2
     *
     * @return Forecast
     */
    public function setTeam2($team2)
    {
        $this->team2 = $team2;

        return $this;
    }

    /**
     * Get team2
     *
     * @return string
     */
    public function getTeam2()
    {
        return $this->team2;
    }

    /**
     * Set added
     *
     * @param string $added
     *
     * @return Forecast
     */
    public function setAdded($added)
    {
        $this->added = $added;

        return $this;
    }

    /**
     * Get added
     *
     * @return string
     */
    public function getAdded()
    {
        return $this->added;
    }

    /**
     * Set result1
     *
     * @param integer $result1
     *
     * @return Forecast
     */
    public function setResult1($result1)
    {
        $this->result1 = $result1;

        return $this;
    }

    /**
     * Get result1
     *
     * @return int
     */
    public function getResult1()
    {
        return $this->result1;
    }

    /**
     * Set result2
     *
     * @param integer $result2
     *
     * @return Forecast
     */
    public function setResult2($result2)
    {
        $this->result2 = $result2;

        return $this;
    }

    /**
     * Get result2
     *
     * @return int
     */
    public function getResult2()
    {
        return $this->result2;
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
     * Set timer
     *
     * @param datetime timer
     *
     * @return Forecast
     */
    public function setTimer($timer)
    {
        $this->timer = $timer;

        return $this;
    }

    /**
     * Get timer
     *
     * @return \DateTime
     */
    public function getTimer()
    {
        return $this->timer;
    }    
}