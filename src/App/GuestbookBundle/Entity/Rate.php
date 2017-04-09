<?php

namespace App\GuestbookBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Rank
 *
 * @ORM\Table(name="rate")
 * @ORM\Entity(repositoryClass="App\GuestbookBundle\Repository\RateRepository")
 * @ORM\HasLifecycleCallbacks() 
 */
class Rate
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
     * @ORM\Column(name="message", type="integer")
     */
    private $message;

    /**
     * @var int
     *
     * @ORM\Column(name="author", type="integer")
     */
    private $author;

    /**
     * @var int
     *
     * @ORM\Column(name="user", type="integer")
     */
    private $user;

    /**
     * @var int
     *
     * @ORM\Column(name="sign", type="integer")
     */
    private $sign;

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
     * Set message
     *
     * @param integer $message
     *
     * @return Rank
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get message
     *
     * @return int
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set author
     *
     * @param integer $author
     *
     * @return Rank
     */
    public function setAuthor($author)
    {
        $this->author = $author;

        return $this;
    }

    /**
     * Get author
     *
     * @return int
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * Set user
     *
     * @param integer $user
     *
     * @return Rank
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
     * Set sign
     *
     * @param integer $sign
     *
     * @return Rank
     */
    public function setSign($sign)
    {
        $this->sign = $sign;

        return $this;
    }

    /**
     * Get sign
     *
     * @return int
     */
    public function getSign()
    {
        return $this->sign;
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

