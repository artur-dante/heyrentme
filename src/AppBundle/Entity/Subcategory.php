<?php

namespace AppBundle\Entity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Entity\SubcategoryRepository")
 * @ORM\Table(name="subcategory")
 */
class Subcategory
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $ID;
    
    /**
     * @ORM\Column(name="CategoryID", type="integer")
     */
    protected $categoryID;    
    
    /**
     * @ORM\Column(type="string", length=256)
     */
    protected $name;

    /**
     * @ORM\Column(type="string", length=64)
     */
    protected $slug;

    /**
     * @ORM\Column(type="integer")
     */
    protected $position;

    /**
     * Get iD
     *
     * @return integer
     */
    public function getImageUrl() {
        return "/db-img/Subcategory/{$this->ID}.jpg";
        // TODO: unhardcode the path
    }

    public function getID()
    {
        return $this->ID;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Category
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
     * Set urlPart
     *
     * @param string $urlPart
     *
     * @return Category
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get urlPart
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set position
     *
     * @param integer $position
     *
     * @return Category
     */
    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Get position
     *
     * @return integer
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Set categoryID
     *
     * @param integer $categoryID
     *
     * @return Subcategory
     */
    public function setCategoryID($categoryID)
    {
        $this->categoryID = $categoryID;

        return $this;
    }

    /**
     * Get categoryID
     *
     * @return integer
     */
    public function getCategoryID()
    {
        return $this->categoryID;
    }
}
