<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Cocur\Slugify\Slugify;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Entity\EquipmentRepository")
 * @ORM\Table(name="equipment")
 */
class Equipment
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    /**
     * @ORM\Column(type="string", length=256)
     */
    protected $name;
    /**
     * @ORM\Column(type="string", length=256)
     */
    protected $description;
    /**
     * @ORM\Column(type="decimal", scale=2, precision=10)
     */
    protected $price;
    /**
     * @ORM\Column(type="decimal", scale=10, precision=2)
     */
    protected $discount;
    /**
     * @ORM\Column(type="decimal", scale=10, precision=2)
     */
    protected $value;
    /**
     * @ORM\Column(type="decimal", scale=10, precision=2)
     */
    protected $deposit;
    /**
     * @ORM\Column(name="PriceBuy", type="decimal", scale=10, precision=2)
     */
    protected $priceBuy;
    /**
     * @ORM\Column(type="integer")
     */
    protected $invoice;
    /**
     * @ORM\Column(type="integer")
     */
    protected $industrial;
    
    /**
     * @ORM\Column(name="AddrStreet", type="string", length=128)
     */
    protected $addrStreet;
    /**
     * @ORM\Column(name="AddrNumber", type="string", length=16)
     */
    protected $addrNumber;
    /**
     * @ORM\Column(name="AddrPostcode", type="string", length=4)
     */
    protected $addrPostcode;
    /**
     * @ORM\Column(name="AddrPlace", type="string", length=128)
     */
    protected $addrPlace;
            
    /**
     * @ORM\ManyToOne(targetEntity="Subcategory", inversedBy="equipments")
     * @ORM\JoinColumn(name="subcategoryID", referencedColumnName="id")
     */
    protected $subcategory;
    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="equipments")
     * @ORM\JoinColumn(name="userID", referencedColumnName="id")
     */
    protected $user;
    
    
    /**
     * 
     * @ORM\ManyToMany(targetEntity="Image")
     * @ORM\JoinTable(name="equipment_image",
     *      joinColumns={ @ORM\JoinColumn(name="equipmentID", referencedColumnName="id") },
     *      inverseJoinColumns={ @ORM\JoinColumn(name="imageID", referencedColumnName="id") }
     *  )
     */
    protected $images;
    
    public function getUrlPath() {
       $sg = new Slugify(); // TODO: make slugify a helper (static)
       $s = $sg->slugify($this->getName());
       return "{$this->id}/{$s}";//   ;
    }
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->images = new \Doctrine\Common\Collections\ArrayCollection();
    }

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
     * @return Equipment
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
     * Set description
     *
     * @param string $description
     *
     * @return Equipment
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set price
     *
     * @param string $price
     *
     * @return Equipment
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
     * Set discount
     *
     * @param string $discount
     *
     * @return Equipment
     */
    public function setDiscount($discount)
    {
        $this->discount = $discount;

        return $this;
    }

    /**
     * Get discount
     *
     * @return string
     */
    public function getDiscount()
    {
        return $this->discount;
    }

    /**
     * Set value
     *
     * @param string $value
     *
     * @return Equipment
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set deposit
     *
     * @param string $deposit
     *
     * @return Equipment
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
     * @return Equipment
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
     * Set invoice
     *
     * @param integer $invoice
     *
     * @return Equipment
     */
    public function setInvoice($invoice)
    {
        $this->invoice = $invoice;

        return $this;
    }

    /**
     * Get invoice
     *
     * @return integer
     */
    public function getInvoice()
    {
        return $this->invoice;
    }

    /**
     * Set industrial
     *
     * @param integer $industrial
     *
     * @return Equipment
     */
    public function setIndustrial($industrial)
    {
        $this->industrial = $industrial;

        return $this;
    }

    /**
     * Get industrial
     *
     * @return integer
     */
    public function getIndustrial()
    {
        return $this->industrial;
    }

    /**
     * Set subcategory
     *
     * @param \AppBundle\Entity\Subcategory $subcategory
     *
     * @return Equipment
     */
    public function setSubcategory(\AppBundle\Entity\Subcategory $subcategory = null)
    {
        $this->subcategory = $subcategory;

        return $this;
    }

    /**
     * Get subcategory
     *
     * @return \AppBundle\Entity\Subcategory
     */
    public function getSubcategory()
    {
        return $this->subcategory;
    }

    /**
     * Add image
     *
     * @param \AppBundle\Entity\Image $image
     *
     * @return Equipment
     */
    public function addImage(\AppBundle\Entity\Image $image)
    {
        $this->images[] = $image;

        return $this;
    }

    /**
     * Remove image
     *
     * @param \AppBundle\Entity\Image $image
     */
    public function removeImage(\AppBundle\Entity\Image $image)
    {
        $this->images->removeElement($image);
    }

    /**
     * Get images
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getImages()
    {
        return $this->images;
    }

    /**
     * Set user
     *
     * @param \AppBundle\Entity\User $user
     *
     * @return Equipment
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
     * Set addrStreet
     *
     * @param string $addrStreet
     *
     * @return Equipment
     */
    public function setAddrStreet($addrStreet)
    {
        $this->addrStreet = $addrStreet;

        return $this;
    }

    /**
     * Get addrStreet
     *
     * @return string
     */
    public function getAddrStreet()
    {
        return $this->addrStreet;
    }

    /**
     * Set addrNumber
     *
     * @param string $addrNumber
     *
     * @return Equipment
     */
    public function setAddrNumber($addrNumber)
    {
        $this->addrNumber = $addrNumber;

        return $this;
    }

    /**
     * Get addrNumber
     *
     * @return string
     */
    public function getAddrNumber()
    {
        return $this->addrNumber;
    }

    /**
     * Set addrPostcode
     *
     * @param string $addrPostcode
     *
     * @return Equipment
     */
    public function setAddrPostcode($addrPostcode)
    {
        $this->addrPostcode = $addrPostcode;

        return $this;
    }

    /**
     * Get addrPostcode
     *
     * @return string
     */
    public function getAddrPostcode()
    {
        return $this->addrPostcode;
    }

    /**
     * Set addrPlace
     *
     * @param string $addrPlace
     *
     * @return Equipment
     */
    public function setAddrPlace($addrPlace)
    {
        $this->addrPlace = $addrPlace;

        return $this;
    }

    /**
     * Get addrPlace
     *
     * @return string
     */
    public function getAddrPlace()
    {
        return $this->addrPlace;
    }
}
