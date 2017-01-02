<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\Common\Collections\ArrayCollection;


/**
 * @ORM\Entity
 * @UniqueEntity(fields="email", message="Email already taken")
 */

class User implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message = "The firstname field is required")
     */
    private $firstname;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message = "The lastname field is required")
     */
    private $lastname;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Assert\NotBlank(message = "The email field is required")
     * @Assert\Email(message = "Please enter valid email address")
     */
    private $email;

    /**
     * @Assert\Length(max=4096)
     * *@Assert\NotBlank(groups={"registration"}, message = "Please enter your password")
     */
    private $plainPassword;

    /**
     *
     * the password, but this works well with bcrypt.
     *
     * @ORM\Column(type="string", length=64, nullable = TRUE)
     */
    private $password;

    /**
     *
     * the password, but this works well with bcrypt.
     *
     * @ORM\Column(type="string", length=100, nullable = TRUE)
     */
    private $activation_code;

    /**
     *
     * the password, but this works well with bcrypt.
     *
     * @ORM\Column(type="boolean")
     */
    private $is_active;

    /**
     * One User has Many News.
     * @ORM\OneToMany(targetEntity="News", mappedBy="user")
     */
    private $news;
    // ...

    public function __construct() {
        $this->news = new ArrayCollection();
    }

    // other properties and methods
    public function getId()
    {
        return $this->id;
    }

    public function getFirstname()
    {
        return $this->firstname;
    }

    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;
    }

    public function getLastname()
    {
        return $this->lastname;
    }

    public function setLastname($lastname)
    {
        $this->lastname = $lastname;
    }
    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function getUsername()
    {
        return $this->email;
    }


    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    public function setPlainPassword($password)
    {
        $this->plainPassword = $password;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function setPassword($password)
    {
        $this->password = $password;
    }

    public function getActivationCode()
    {
        return $this->activation_code;
    }

    public function setActivationCode($activation_code)
    {
        $this->activation_code = $activation_code;
    }

    public function getIsActive()
    {
        return $this->is_active;
    }

    public function setIsActive($is_active)
    {
        $this->is_active = $is_active;
    }



    public function getSalt()
    {
        // The bcrypt algorithm doesn't require a separate salt.
        // You *may* need a real salt if you choose a different encoder.
        return null;
    }

    public function getRoles()
    {
        return array('ROLE_USER');
    }
    public function eraseCredentials()
    {
        return null;
    }

    /** @see \Serializable::serialize() */
    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->username,
            $this->password,
            // see section on salt below
            // $this->salt,
        ));
    }

    /** @see \Serializable::unserialize() */
    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->username,
            $this->password,
            // see section on salt below
            // $this->salt
            ) = unserialize($serialized);
    }

    // other methods, including security methods like getRoles()
}