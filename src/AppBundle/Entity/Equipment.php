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
     * @ORM\OneToOne(targetEntity="Image")
     * @ORM\JoinColumn(name="imageID", referencedColumnName="id")
     */
    protected $image;
    
    public function getImageUrl() {
        $url = '';
        if ($this->image != null) {
            $url = $this->image->getUrlPath();
        }
        return $url;
    }
    
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
     * Set image
     *
     * @param \AppBundle\Entity\Image $image
     *
     * @return Equipment
     */
    public function setImage(\AppBundle\Entity\Image $image = null)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image
     *
     * @return \AppBundle\Entity\Image
     */
    public function getImage()
    {
        return $this->image;
    }
}
