<?php

namespace App\TournamentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Bonus
 *
 * @ORM\Table(name="bonus")
 * @ORM\Entity(repositoryClass="App\TournamentBundle\Repository\BonusRepository")
 */
class Bonus
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
     * @ORM\Column(name="user", type="integer")
     */
    private $user;

    /**
     * @var int
     *
     * @ORM\Column(name="foreid", type="integer")
     */
    private $foreid;


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
     * @return Bonus
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
     * @return Bonus
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
     * @return Bonus
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
     * Set foreid
     *
     * @param integer $foreid
     *
     * @return Bonus
     */
    public function setForeid($foreid)
    {
        $this->foreid = $foreid;

        return $this;
    }

    /**
     * Get foreid
     *
     * @return int
     */
    public function getForeid()
    {
        return $this->foreid;
    }
}

