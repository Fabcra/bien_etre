<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

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
     * @ORM\Column(name="names", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="descriptions", type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @var bool
     *
     * @ORM\Column(name="in_front", type="boolean", nullable=true)
     */
    private $inFront;

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
     *
     */
    private $users;

    /**
     *
     *
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\Image", mappedBy="service", cascade={"persist"})
     *
     * @ORM\JoinColumn(name="images", nullable=true, onDelete = "SET NULL")
     *
     */
    private $image;


    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Promotion", mappedBy="service")
     */
    private $promotions;



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
     * Set inFront
     *
     * @param boolean $inFront
     *
     * @return Services
     */
    public function setInFront($inFront)
    {
        $this->inFront = $inFront;

        return $this;
    }

    /**
     * Get inFront
     *
     * @return bool
     */
    public function getInFront()
    {
        return $this->inFront;
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
     * @return string
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * @param string $users
     */
    public function setUsers($users)
    {
        $this->users = $users;
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

}