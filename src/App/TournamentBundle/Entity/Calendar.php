<?php

namespace App\TournamentBundle\Entity;
use Symfony\Component\Validator\Constraints as Assert;

use Doctrine\ORM\Mapping as ORM;

/**
 * Calendar
 *
 * @ORM\Table(name="calendar")
 * @ORM\Entity(repositoryClass="App\TournamentBundle\Repository\CalendarRepository")
 * @ORM\HasLifecycleCallbacks()   
 */
class Calendar
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
     * @ORM\Column(name="endtour", type="integer", nullable=true)
     */
    private $endtour;

    /**
     * @var int
     *
     * @ORM\Column(name="user1", type="integer", nullable=true)
     */
    private $user1;

    /**
     * @var int
     *
     * @ORM\Column(name="user2", type="integer", nullable=true)
     */
    private $user2;

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
     * @return Calendar
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
     * @return Calendar
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
     * @return Calendar
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
     * @return Calendar
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
     * Set endtour
     *
     * @param integer $endtour
     *
     * @return Calendar
     */
    public function setEndtour($endtour)
    {
        $this->endtour = $endtour;

        return $this;
    }

    /**
     * Get endtour
     *
     * @return int
     */
    public function getEndtour()
    {
        return $this->endtour;
    }

    /**
     * Set user1
     *
     * @param integer $user1
     *
     * @return Calendar
     */
    public function setUser1($user1)
    {
        $this->user1 = $user1;

        return $this;
    }

    /**
     * Get user1
     *
     * @return int
     */
    public function getUser1()
    {
        return $this->user1;
    }

    /**
     * Set user2
     *
     * @param integer $user2
     *
     * @return Calendar
     */
    public function setUser2($user2)
    {
        $this->user2 = $user2;

        return $this;
    }

    /**
     * Get user2
     *
     * @return int
     */
    public function getUser2()
    {
        return $this->user2;
    }

    /**
     * Set result1
     *
     * @param integer $result1
     *
     * @return Calendar
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
     * @return Calendar
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

