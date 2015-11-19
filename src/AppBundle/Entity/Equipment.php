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
    public function getUrlPath() {
       $sg = new Slugify(); // TODO: make slugify a helper (static)
       $s = $sg->slugify($this->getName());
       return "{$this->ID}/{$s}";//   ;
    }
    
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $ID;
    
    /**
     * @ORM\Column(name="SubcategoryID", type="integer")
     */
    protected $subcategoryID;

    /**
     * @ORM\Column(type="string", length=256)
     */
    protected $name;

    /**
     * @ORM\Column(type="string", length=256)
     */
    protected $description;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
     */
    protected $price;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=0)
     */
    protected $discount;

    /**
     * @ORM\Column(name="TestBuy", type="boolean")
     */
    protected $testBuy;

    public function getImageUrl() {
        return "/db-img/Equipment/{$this->ID}.jpg";
        // TODO: unhardcode the path
    }

    /**
     * Get iD
     *
     * @return integer
     */
    public function getID()
    {
        return $this->ID;
    }

    /**
     * Set subcategoryID
     *
     * @param integer $subcategoryID
     *
     * @return Equipment
     */
    public function setSubcategoryID($subcategoryID)
    {
        $this->SubcategoryID = $subcategoryID;

        return $this;
    }

    /**
     * Get subcategoryID
     *
     * @return integer
     */
    public function getSubcategoryID()
    {
        return $this->SubcategoryID;
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
     * @param boolean $testBuy
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
     * @return boolean
     */
    public function getTestBuy()
    {
        return $this->testBuy;
    }
}
