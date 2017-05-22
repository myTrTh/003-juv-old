<?php

namespace App\UserBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * User
 *
 * @ORM\Table(name="users")
 * @ORM\Entity(repositoryClass="App\UserBundle\Repository\UserRepository")
 * @ORM\HasLifecycleCallbacks()  
 */
class User extends BaseUser
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created", type="datetime")
     */
    private $created;

    /**
     * @ORM\Column(name="image", type="string", length=255, nullable=true)
     *
    */    
    private $image;

    /**
     * @var string
     *
     * @ORM\Column(name="options", type="string", nullable=true)
     * @Assert\NotBlank()
     */
    protected $options;

    /**
     * @var string
     *
     * @ORM\Column(name="signature", type="string", length=150, nullable=true)
     */
    protected $signature;    


    public function __construct() {
        parent::__construct();

        # set default role and default ban status
        $this->roles = array('ROLE_VERIFIED_USER', 'ROLE_BANNED_0');
        $this->options = serialize(array('timezone' => 100, 'notification' => array('notification_guestbook' => false, 'notification_vote' => true)));
    }

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
     * Set image
     *
     * @param string $image
     *
     * @return User
     */
    public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image
     *
     * @return string
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Set options
     *
     * @param string $options
     *
     * @return User
     */
    public function setOptions($options)
    {
        $this->options = serialize($options);

        return $this;
    }

    /**
     * Get options
     *
     * @return string
     */
    public function getOptions()
    {
        return unserialize($this->options);
    }

    /**
     * Set options
     *
     * @param string $signature
     *
     * @return User
     */
    public function setSignature($signature)
    {
        $this->signature = $signature;

        return $this;
    }

    /**
     * Get options
     *
     * @return string
     */
    public function getSignature()
    {
        return $this->signature;
    }     
}