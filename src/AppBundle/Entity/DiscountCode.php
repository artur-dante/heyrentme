<?php

namespace AppBundle\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="DiscountCodeRepository")
 * @ORM\Table(name="discount_code")
 */
class DiscountCode
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /**
     * @ORM\Column(type="integer")
     */
    protected $status;  
    
    /**
     * @ORM\Column(type="string", length=8)
     */
    protected $code;
    
    /**
     * @ORM\Column(type="datetime")
     */
    protected $createdAt;    
    /**
     * @ORM\Column(type="datetime")
     */
    protected $modifiedAt;    

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="discountCodes")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;

    public function setId($id)
    {
        return $this;
    }

    
    public function getId()
    {
        return $this->id;
    }

    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    public function getStatus()
    {
        return $this->status;
    }
    
    const status_new = 1;
    const status_assignet = 2;
    const status_used = 3;
    const status_cancelled = 4;        
    
    public function getStatusStr()
    {
        if ($this->status == DiscountCode::status_new){
            return "new";
        } else if ($this->status == DiscountCode::status_assignet){
            return "assignet";
        } else if ($this->status == DiscountCode::status_used){
            return "used";
        } else if ($this->status == DiscountCode::status_cancelled){
            return "cancelled";
        } else {
            return 'unknown';
        }
    }
    
    const dicount_code_length = 8;
    
    public static function GenerateCode($chars){
        
        $maxRange = count($chars) - 1;
        $code = "";        
        while (strlen($code) < DiscountCode::dicount_code_length) {
            $i = mt_rand(0, $maxRange);
            $code .= $chars[$i];
        }
        return $code;
    }
    
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }
    
    public function getCode()
    {
        return $this->code;
    }
  
    public function setUser(User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    
    public function getUser()
    {
        return $this->user;
    }
    

    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

  
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

  
    public function setModifiedAt($modifiedAt)
    {
        $this->modifiedAt = $modifiedAt;

        return $this;
    }

  
    public function getModifiedAt()
    {
        return $this->modifiedAt;
    }

    public function __construct()
    {
        
    }

   
}
