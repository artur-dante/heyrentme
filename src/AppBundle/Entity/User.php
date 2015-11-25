<?php
namespace AppBundle\Entity;
// src/AppBundle/Entity/User.php
#whole class added by Seba


use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="fos_user")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\UserRepository")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
      /**
     * @var string
     *
     * @ORM\Column(name="facebookID", type="string", nullable=true)
     */
    protected $facebookID;
    
    public function getFacebookID()
    {
        return $this->facebookID;
    }

    public function setEmail($email)
    {
        $this->email = $email;
        $this->setUsername($email);
    }
    
    public function setFacebookID($facebookID)
    {
        $this->facebookID = $facebookID;
    }
    
    /**
     * @var string
     *
     * @ORM\Column(name="Name", type="string", nullable=true)
     * 
     * 
     *  * @Assert\NotBlank(message="Please enter your name.", groups={"Registration", "Profile"})
     * @Assert\Length(
     *     min=3,
     *     max=128,
     *     minMessage="The name is too short.",
     *     maxMessage="The name is too long.",
     *     groups={"Registration", "Profile"}
     * )
     * 
     */
    protected $name;
    
    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }
    
    /**
     * @var string
     *
     * @ORM\Column(name="Surname", type="string", nullable=true)
     * 
     *  *  * @Assert\NotBlank(message="Please enter your surname.", groups={"Registration", "Profile"})
     * @Assert\Length(
     *     min=3,
     *     max=128,
     *     minMessage="The name is too short.",
     *     maxMessage="The name is too long.",
     *     groups={"Registration", "Profile"}
     * )
     * 
     */
    protected $surname;
    
    public function getSurname()
    {
        return $this->surname;
    }

    public function setSurname($surname)
    {
        $this->surname = $surname;
    }
    
    
    public function __construct()
    {
        parent::__construct();
        // your own logic
    }
}
