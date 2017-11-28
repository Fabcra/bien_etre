<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Promotion
 *
 * @ORM\Table(name="promotions")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PromotionRepository")
 */
class Promotion
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
     * @ORM\Column(name="description", type="string", length=255)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="pdf_doc", type="string", length=255)
     */
    private $pdfDoc;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="from_date", type="datetime")
     */
    private $fromDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="to_date", type="datetime")
     */
    private $toDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="display_from", type="datetime")
     */
    private $displayFrom;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="display_to", type="datetime")
     */
    private $displayTo;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Service", inversedBy="promotions")
     */
    private $service;


    /**
     * @var string
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Provider", inversedBy="promotions")
     *
     * @ORM\JoinColumn(onDelete="SET NULL")
     */
    private $provider;


    /**
     * @Gedmo\Slug(fields={"name"})
     * @ORM\column(length=128, nullable=true)
     */
    private $slug;


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
     * @return Promotion
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
     * @return Promotion
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
     * Set pdfDoc
     *
     * @param string $pdfDoc
     *
     * @return Promotion
     */
    public function setPdfDoc($pdfDoc)
    {
        $this->pdfDoc = $pdfDoc;

        return $this;
    }

    /**
     * Get pdfDoc
     *
     * @return string
     */
    public function getPdfDoc()
    {
        return $this->pdfDoc;
    }

    /**
     * Set fromDate
     *
     * @param \DateTime $fromDate
     *
     * @return Promotion
     */
    public function setFromDate($fromDate)
    {
        $this->fromDate = $fromDate;

        return $this;
    }

    /**
     * Get fromDate
     *
     * @return \DateTime
     */
    public function getFromDate()
    {
        return $this->fromDate;
    }

    /**
     * Set toDate
     *
     * @param \DateTime $toDate
     *
     * @return Promotion
     */
    public function setToDate($toDate)
    {
        $this->toDate = $toDate;

        return $this;
    }

    /**
     * Get toDate
     *
     * @return \DateTime
     */
    public function getToDate()
    {
        return $this->toDate;
    }

    /**
     * Set displayFrom
     *
     * @param \DateTime $displayFrom
     *
     * @return Promotion
     */
    public function setDisplayFrom($displayFrom)
    {
        $this->displayFrom = $displayFrom;

        return $this;
    }

    /**
     * Get displayFrom
     *
     * @return \DateTime
     */
    public function getDisplayFrom()
    {
        return $this->displayFrom;
    }

    /**
     * Set displayTo
     *
     * @param \DateTime $displayTo
     *
     * @return Promotion
     */
    public function setDisplayTo($displayTo)
    {
        $this->displayTo = $displayTo;

        return $this;
    }

    /**
     * Get displayTo
     *
     * @return \DateTime
     */
    public function getDisplayTo()
    {
        return $this->displayTo;
    }

    /**
     * @return string
     */
    public function getProvider()
    {
        return $this->provider;
    }

    /**
     * @param string $provider
     */
    public function setProvider($provider)
    {
        $this->provider = $provider;
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

    /**
     * @return mixed
     */
    public function getService()
    {
        return $this->service;
    }

    /**
     * @param mixed $service
     */
    public function setService($service)
    {
        $this->service = $service;
    }




}

