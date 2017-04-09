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
     * groups={"Registration", "Profile"}
     * @Assert\File(mimeTypes={ "image/png", "image/jpeg", "image/gif" })
     * @Assert\File(maxSize="50k")
     *
    */    
    private $image;

    /**
     * @var int
     *
     * @ORM\Column(name="timezone", type="integer", nullable=true)
     * @Assert\NotBlank()
     */
    protected $timezone;


    public function __construct() {
        parent::__construct();

        # set default role and default ban status
        $this->roles = array('ROLE_VERIFIED_USER', 'ROLE_BANNED_0');
        $this->timezone = 100;
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
     * Set timezone
     *
     * @param string $timezone
     *
     * @return User
     */
    public function setTimezone($timezone)
    {
        $this->timezone = $timezone;

        return $this;
    }

    /**
     * Get timezone
     *
     * @return string
     */
    public function getTimezone()
    {
        return $this->timezone;
    }    
}