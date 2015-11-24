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
     * @ORM\Column(type="decimal", scale=10, precision=2)
     */
    protected $price;
    /**
     * @ORM\Column(type="decimal", scale=10, precision=0)
     */
    protected $discount;
    /**
     * @ORM\Column(name="TestBuy", type="integer")
     */
    protected $testBuy;
            
    /**
     * @ORM\ManyToOne(targetEntity="Subcategory", inversedBy="equipments")
     * @ORM\JoinColumn(name="subcategoryID", referencedColumnName="id")
     */
    protected $subcategory;
    
    
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
     * Set testBuy
     *
     * @param integer $testBuy
     *
     * @return Equipment
     */
    public function setTestBuy($testBuy)
    {
        $this->testBuy = $testBuy;

        return $this;
    }

    /**
     * Get testBuy
     *
     * @return integer
     */
    public function getTestBuy()
    {
        return $this->testBuy;
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
     * Constructor
     */
    public function __construct()
    {
        $this->images = new \Doctrine\Common\Collections\ArrayCollection();
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
}
