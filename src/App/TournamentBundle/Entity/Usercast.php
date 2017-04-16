<?php

namespace App\TournamentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Usercast
 *
 * @ORM\Table(name="usercast")
 * @ORM\Entity(repositoryClass="App\TournamentBundle\Repository\UsercastRepository")
 * @ORM\HasLifecycleCallbacks()   
 */
class Usercast
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
     * @ORM\Column(name="user", type="integer")
     */
    private $user;

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
     * @var int
     *
     * @ORM\Column(name="ball", type="integer", nullable=true)
     */
    private $ball;

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
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
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

    /**
     * Set user
     *
     * @param integer $user
     *
     * @return Usercast
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
     * Set tr
     *
     * @param integer $tr
     *
     * @return Usercast
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
     * @return Usercast
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
     * Set result1
     *
     * @param integer $result1
     *
     * @return Usercast
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
     * @return Usercast
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
     * Set ball
     *
     * @param integer $ball
     *
     * @return Usercast
     */
    public function setBall($ball)
    {
        $this->ball = $ball;

        return $this;
    }

    /**
     * Get ball
     *
     * @return int
     */
    public function getBall()
    {
        return $this->ball;
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
}

