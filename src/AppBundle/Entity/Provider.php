<?php

namespace AppBundle\Entity;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Provider
 *
 * @ORM\Table(name="providers")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ProviderRepository")
 *
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
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="names", type="string", length=25, nullable=true)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="websites", type="string", length=255, nullable=true)
     */
    private $website;

    /**
     * @var string
     *
     * @ORM\Column(name="e_mails_contact", type="string", length=255, nullable=true)
     */
    private $eMail_contact;


    /**
     * @var string
     *
     * @ORM\Column(name="phone_nos", type="string", length=255, nullable=true)
     */
    private $phoneNo;

    /**
     * @var string
     *
     * @ORM\Column(name="tva_nos", type="string", length=255, nullable=true)
     */
    private $tvaNo;


    /**
     * @var string
     *
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Service", inversedBy="users", cascade={"persist"})
     *
     */
    private $services;


    /**
     * @var string
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Image", mappedBy="provider")
     *
     * @ORM\JoinColumn(name="galeries")
     *
     */
    private $galery;


    /**
     * @var string
     *
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\Image")
     *
     * @ORM\JoinColumn(name="logos", onDelete="SET NULL")
     *
     */
    private $logo;

    /**
     * @var string
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Stage", mappedBy="user")
     *
     */
    private $stages;

    /**
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Promotion", mappedBy="user")
     */
    private $promotions;


    /**
     * Provider constructor.
     */
    public function __construct()
    {
        $this->stages = new ArrayCollection();
        $this->promotions = new ArrayCollection();
        $this->services = new ArrayCollection();
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
    public function getGalery()
    {
        return $this->galery;
    }

    /**
     * @param string $galery
     */
    public function setGalery($galery)
    {
        $this->galery = $galery;
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

}

