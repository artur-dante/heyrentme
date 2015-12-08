<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Entity\BlogRepository")
 * @ORM\Table(name="blog")
 */
class Blog
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /**
     * @ORM\Column(type="string", length=256)
     */
    protected $title;    
    /**
     * @ORM\Column(type="integer")
     */
    protected $position;   
        
    /**
     * @ORM\Column(type="string")
     */
    protected $content;    
    
    /**
     * @ORM\Column(type="datetime")
     */
    protected $creation_date;    
    /**
     * @ORM\Column(type="datetime")
     */
    protected $modification_date;    

    /**
     * @ORM\OneToOne(targetEntity="Image")
     * @ORM\JoinColumn(name="image_id", referencedColumnName="id")
     */
    protected $image;
    
    public function getImageUrl() {
        return $this->image != null ? $this->image->getUrlPath() : "";
    }
    
    
    public function setId($id)
    {
        return $this;
    }

    
    public function getId()
    {
        return $this->id;
    }

    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    public function getTitle()
    {
        return $this->title;
    }
    
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }
    
    public function getContent()
    {
        return $this->content;
    }

    public function getCreationDate()
    {
        return $this->creation_date;
    }
    
    public function setCreationDate($creation_date)
    {
        $this->creation_date = $creation_date;

        return $this;
    }
    
    public function getModificationDate()
    {
        return $this->modification_date;
    }
    
    public function setModificationDate($modification_date)
    {
        $this->modification_date = $modification_date;

        return $this;
    }


   
    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }

    public function getPosition()
    {
        return $this->position;
    }
    
    
    public function setImage(\AppBundle\Entity\Image $image = null)
    {
        $this->image = $image;

        return $this;
    }
    
    public function getImage()
    {
        return $this->image;
    }
    
    public function __construct()
    {
        
    }

}
