<?php

namespace AppBundle\Entity;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * Provider
 *
 * @ORM\Table(name="providers")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ProviderRepository")
 *
 */
class Provider extends User
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     * @Assert\NotBlank(groups={"providers"})
     * @ORM\Column(name="name", type="string", length=255)
     *
     *
     */
    private $name;

    /**
     * @var string
     * @Assert\NotBlank(groups={"providers"})
     * @ORM\Column(name="website", type="string", length=255)
     *
     */
    private $website;

    /**
     * @var string
     * @Assert\NotBlank(groups={"providers"})
     * @ORM\Column(name="e_mail_contact", type="string", length=255)
     */
    private $eMail_contact;


    /**
     * @var string
     *
     * @Assert\NotBlank(groups={"providers"})
     * @ORM\Column(name="phone_no", type="string", length=255)
     */
    private $phoneNo;

    /**
     * @var string
     * @Assert\NotBlank(groups={"providers"})
     * @ORM\Column(name="tva_no", type="string", length=255)
     */
    private $tvaNo;


    /**
     * @var string
     * @Assert\NotBlank(groups={"providers"})
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Service", inversedBy="providers", cascade={"persist"})
     *
     * @ORM\JoinColumn(nullable=false)
     */
    private $services;


    /**
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Image", mappedBy="provider", cascade={"remove"})
     *
     */
    private $gallery;


    /**
     * @var string
     *
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\Image", cascade={"remove"})
     *
     * @ORM\JoinColumn(onDelete="SET NULL")
     */
    private $logo;

    /**
     * @var string
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Stage", mappedBy="provider", cascade={"remove"})
     *
     */
    private $stages;

    /**
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Promotion", mappedBy="provider", cascade={"remove"})
     *
     */
    private $promotions;

    /**
     * @Gedmo\Slug(fields={"name"})
     * @ORM\column(length=128)
     */
    private $slug;


    /**
     * Provider constructor.
     */
    public function __construct()
    {
        $this->services = new ArrayCollection();
        $this->gallery = new ArrayCollection();
    }

    /**
     * @return ArrayCollection
     */
    public function getStages()
    {
        return $this->stages;
    }

    /**
     * @param string $stages
     */
    public function setStages($stages)
    {
        $this->stages = $stages;
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
     * @return string
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
     * Set website
     *
     * @param string $website
     *
     * @return string
     *
     */
    public function setWebsite($website)
    {
        $this->website = $website;

        return $this;
    }

    /**
     * Get website
     *
     * @return string
     */
    public function getWebsite()
    {
        return $this->website;
    }


    /**
     * Set phoneNo
     *
     * @param string $phoneNo
     *
     * @return Prestataire
     */
    public function setPhoneNo($phoneNo)
    {
        $this->phoneNo = $phoneNo;

        return $this;
    }

    /**
     * Get phoneNo
     *
     * @return string
     */
    public function getPhoneNo()
    {
        return $this->phoneNo;
    }

    /**
     * Set tvaNo
     *
     * @param string $tvaNo
     *
     * @return Prestataire
     */
    public function setTvaNo($tvaNo)
    {
        $this->tvaNo = $tvaNo;

        return $this;
    }

    /**
     * Get tvaNo
     *
     * @return string
     */
    public function getTvaNo()
    {
        return $this->tvaNo;
    }


    /**
     * @return string
     */
    public function getGallery()
    {
        return $this->gallery;
    }

    /**
     * @param string $gallery
     */
    public function setGallery($gallery)
    {
        $this->gallery = $gallery;
    }


    /**
     * @return string
     */
    public function getLogo()
    {
        return $this->logo;
    }

    /**
     * @param string $logo
     */
    public function setLogo($logo)
    {
        $this->logo = $logo;
    }

    /**
     * @return string
     */
    public function getServices()
    {
        return $this->services;
    }

    /**
     * @param string $services
     */
    public function setServices($services)
    {
        $this->services = $services;
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
     * @return string
     */
    public function getEMailContact()
    {
        return $this->eMail_contact;
    }

    /**
     * @param string $eMail_contact
     */
    public function setEMailContact($eMail_contact)
    {
        $this->eMail_contact = $eMail_contact;
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


}

