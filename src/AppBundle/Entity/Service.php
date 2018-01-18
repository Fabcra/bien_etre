<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;


/**
 * Service
 *
 * @ORM\Table(name="services")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ServicesRepository")
 */
class Service
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @var bool
     *
     * @ORM\Column(name="highlight", type="boolean", nullable=true)
     */
    private $highlight;

    /**
     * @var bool
     *
     * @ORM\Column(name="valid", type="boolean")
     */
    private $valid;

    /**
     *
     *
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Provider", mappedBy="services",cascade={"persist"})
     *
     */
    private $providers;

    /**
     *
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\Image", cascade={"persist"})
     *
     * @ORM\JoinColumn(onDelete="SET NULL")
     *
     */
    private $image;


    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Promotion", mappedBy="service")
     */
    private $promotions;

    /**
     * @Gedmo\Slug(fields={"name"})
     * @ORM\column(length=128, nullable=true)
     */
    private $slug;

    public function __construct()
    {
        $this->providers=new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return int
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
     * @return $this
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
     * @return Services
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
     * @return bool
     */
    public function isHighlight()
    {
        return $this->highlight;
    }

    /**
     * @param bool $highlight
     */
    public function setHighlight($highlight)
    {
        $this->highlight = $highlight;
    }


    /**
     * Set valid
     *
     * @param boolean $valid
     *
     * @return Services
     */
    public function setValid($valid)
    {
        $this->valid = $valid;

        return $this;
    }

    /**
     * Get valid
     *
     * @return bool
     */
    public function getValid()
    {
        return $this->valid;
    }

    /**
     * @return mixed
     */
    public function getProviders()
    {
        return $this->providers;
    }

    /**
     * @param mixed $providers
     */
    public function setProviders($providers)
    {
        $this->providers = $providers;
    }



    /**
     * @return mixed
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @param mixed $image
     */
    public function setImage($image)
    {
        $this->image = $image;
    }

    /**
     * @return mixed
     */
    public function getPromotions()
    {
        return $this->promotions;
    }

    /**
     * @param mixed $promotions
     */
    public function setPromotions($promotions)
    {
        $this->promotions = $promotions;
    }

    /**
     * @return mixed
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * @param mixed $slug
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;
    }

    public function __toString()
    {
        return $this->getName();
    }

}