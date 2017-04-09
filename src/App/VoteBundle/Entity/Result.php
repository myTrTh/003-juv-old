<?php

namespace App\VoteBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Result
 *
 * @ORM\Table(name="votes_result")
 * @ORM\Entity(repositoryClass="App\VoteBundle\Repository\ResultRepository")
 * @ORM\HasLifecycleCallbacks() 
 */
class Result
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
     * @var string
     *
     * @ORM\Column(name="session", type="string")
     */
    private $session;

    /**
     * @var string
     *
     * @ORM\Column(name="ip", type="string", nullable=true)
     */
    private $ip;    

    /**
     * @var int
     *
     * @ORM\Column(name="vote", type="integer")
     */
    private $vote;

    /**
     * @var int
     *
     * @ORM\Column(name="options", type="integer")
     * @Assert\NotBlank(message="Пожалуйста, выберите вариант ответа")     
     */
    private $options;

    /**
     * @var bool
     *
     * @ORM\Column(name="anon", type="boolean", nullable=true)
     */
    private $anon;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created", type="datetime")
     */
    private $created;

    /**
     * @var bool
     *
     * @ORM\Column(name="status", type="boolean")
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
     * Set user
     *
     * @param integer $user
     *
     * @return Result
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
     * Set session
     *
     * @param string $session
     *
     * @return Result
     */
    public function setSession($session)
    {
        $this->session = $session;

        return $this;
    }

    /**
     * Get session
     *
     * @return string
     */
    public function getSession()
    {
        return $this->session;
    }    

    /**
     * Set ip
     *
     * @param string $ip
     *
     * @return Result
     */
    public function setIp($ip)
    {
        $this->ip = $ip;

        return $this;
    }

    /**
     * Get ip
     *
     * @return string
     */
    public function getIp()
    {
        return $this->ip;
    }        

    /**
     * Set vote
     *
     * @param integer $vote
     *
     * @return Result
     */
    public function setVote($vote)
    {
        $this->vote = $vote;

        return $this;
    }

    /**
     * Get vote
     *
     * @return int
     */
    public function getVote()
    {
        return $this->vote;
    }

    /**
     * Set options
     *
     * @param integer $options
     *
     * @return Result
     */
    public function setOptions($options)
    {
        $this->options = $options;

        return $this;
    }

    /**
     * Get option
     *
     * @return int
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Set anon
     *
     * @param boolean $anon
     *
     * @return Result
     */
    public function setAnon($anon)
    {
        $this->anon = $anon;

        return $this;
    }

    /**
     * Get anon
     *
     * @return bool
     */
    public function getAnon()
    {
        return $this->anon;
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
     * Set status
     *
     * @param boolean $status
     *
     * @return Result
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return bool
     */
    public function getStatus()
    {
        return $this->status;
    }
}

