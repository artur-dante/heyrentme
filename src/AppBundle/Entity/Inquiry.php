<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Entity\InquiryRepository")
 * @ORM\Table(name="inquiry")
 */
class Inquiry {
    
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    /**
     * @ORM\Column(type="string", length=128)
     */
    private $name;
    /**
     * @ORM\Column(type="string", length=128)
     */
    private $email;
    /**
     * @ORM\Column(type="string")
     */
    private $message;
    /**
     * @ORM\Column(type="datetime")
     */
    private $fromAt;
    /**
     * @ORM\Column(type="datetime")
     */
    private $toAt;
    /**
     * @ORM\Column(type="decimal")
     */
    private $price;
    /**
     * @ORM\Column(type="decimal")
     */
    private $deposit;
    /**
     * @ORM\Column(type="decimal")
     */
    private $priceBuy;
    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;
    
    /**
     * @ORM\Column(type="integer")
     */
    private $accepted;
    /**
     * @ORM\Column(type="string")
     */
    private $response;
    /**
     * @ORM\Column(type="datetime")
     */
    private $modifiedAt;
    /**
     * @ORM\Column(type="string")
     */
    private $uuid;
    
    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="inquiries")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;
    
    /**
     * @ORM\ManyToOne(targetEntity="Equipment")
     * @ORM\JoinColumn(name="equipment_id", referencedColumnName="id")
     */
    private $equipment;
    
    /**
     * @ORM\OneToOne(targetEntity="Booking", mappedBy="inquiry")
     */
    private $booking;    
    /**
     * @ORM\OneToOne(targetEntity="DiscountCode", mappedBy="inquiry")
     */
    private $discountCode;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Inquiry
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return Inquiry
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set message
     *
     * @param string $message
     *
     * @return Inquiry
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get message
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set fromAt
     *
     * @param \DateTime $fromAt
     *
     * @return Inquiry
     */
    public function setFromAt($fromAt)
    {
        $this->fromAt = $fromAt;

        return $this;
    }

    /**
     * Get fromAt
     *
     * @return \DateTime
     */
    public function getFromAt()
    {
        return $this->fromAt;
    }

    /**
     * Set toAt
     *
     * @param \DateTime $toAt
     *
     * @return Inquiry
     */
    public function setToAt($toAt)
    {
        $this->toAt = $toAt;

        return $this;
    }

    /**
     * Get toAt
     *
     * @return \DateTime
     */
    public function getToAt()
    {
        return $this->toAt;
    }

    /**
     * Set price
     *
     * @param string $price
     *
     * @return Inquiry
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price
     *
     * @return string
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set deposit
     *
     * @param string $deposit
     *
     * @return Inquiry
     */
    public function setDeposit($deposit)
    {
        $this->deposit = $deposit;

        return $this;
    }

    /**
     * Get deposit
     *
     * @return string
     */
    public function getDeposit()
    {
        return $this->deposit;
    }

    /**
     * Set priceBuy
     *
     * @param string $priceBuy
     *
     * @return Inquiry
     */
    public function setPriceBuy($priceBuy)
    {
        $this->priceBuy = $priceBuy;

        return $this;
    }

    /**
     * Get priceBuy
     *
     * @return string
     */
    public function getPriceBuy()
    {
        return $this->priceBuy;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Inquiry
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set user
     *
     * @param \AppBundle\Entity\User $user
     *
     * @return Inquiry
     */
    public function setUser(\AppBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \AppBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set equipment
     *
     * @param \AppBundle\Entity\Equipment $equipment
     *
     * @return Inquiry
     */
    public function setEquipment(\AppBundle\Entity\Equipment $equipment = null)
    {
        $this->equipment = $equipment;

        return $this;
    }

    /**
     * Get equipment
     *
     * @return \AppBundle\Entity\Equipment
     */
    public function getEquipment()
    {
        return $this->equipment;
    }

    /**
     * Set accepted
     *
     * @param integer $accepted
     *
     * @return Inquiry
     */
    public function setAccepted($accepted)
    {
        $this->accepted = $accepted;

        return $this;
    }

    /**
     * Get accepted
     *
     * @return integer
     */
    public function getAccepted()
    {
        return $this->accepted;
    }

    /**
     * Set response
     *
     * @param string $response
     *
     * @return Inquiry
     */
    public function setResponse($response)
    {
        $this->response = $response;

        return $this;
    }

    /**
     * Get response
     *
     * @return string
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * Set modifiedAt
     *
     * @param \DateTime $modifiedAt
     *
     * @return Inquiry
     */
    public function setModifiedAt($modifiedAt)
    {
        $this->modifiedAt = $modifiedAt;

        return $this;
    }

    /**
     * Get modifiedAt
     *
     * @return \DateTime
     */
    public function getModifiedAt()
    {
        return $this->modifiedAt;
    }

    /**
     * Set uuid
     *
     * @param string $uuid
     *
     * @return Inquiry
     */
    public function setUuid($uuid)
    {
        $this->uuid = $uuid;

        return $this;
    }

    /**
     * Get uuid
     *
     * @return string
     */
    public function getUuid()
    {
        return $this->uuid;
    }

    /**
     * Set inquiry
     *
     * @param \AppBundle\Entity\Booking $inquiry
     *
     * @return Inquiry
     */
    public function setBooking(\AppBundle\Entity\Booking $booking = null)
    {
        $this->inquiry = $booking;

        return $this;
    }

    /**
     * Get inquiry
     *
     * @return \AppBundle\Entity\Booking
     */
    public function getBooking()
    {
        return $this->booking;
    }

    /**
     * Set discountCode
     *
     * @param \AppBundle\Entity\DiscountCode $discountCode
     *
     * @return Inquiry
     */
    public function setDiscountCode(\AppBundle\Entity\DiscountCode $discountCode = null)
    {
        $this->discountCode = $discountCode;

        return $this;
    }

    /**
     * Get discountCode
     *
     * @return \AppBundle\Entity\DiscountCode
     */
    public function getDiscountCode()
    {
        return $this->discountCode;
    }
}
