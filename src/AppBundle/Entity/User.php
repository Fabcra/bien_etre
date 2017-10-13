<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * User
 *
 * @ORM\Table(name="users")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserRepository")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="user_type", type="string")
 * @ORM\DiscriminatorMap({"users" = "User", "members" = "Member", "providers" = "Provider"})
 */
class User
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
     * @ORM\Column(name="e_mails", type="string", length=255, unique=true, nullable=true)
     */
    private $eMail;

    /**
     * @var string
     *
     * @ORM\Column(name="passwords", type="string", length=255, nullable=true)
     */
    private $password;

    /**
     * @var string
     *
     * @ORM\Column(name="addresses_no", type="string", length=5, nullable=true)
     */
    private $addressNo;

    /**
     * @var string
     *
     * @ORM\Column(name="street_names", type="string", length=255, nullable=true)
     */
    private $streetName;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="registration_dates", type="datetime", nullable=true)
     */
    private $registrationDate;


    /**
     * @var int
     *
     * @ORM\Column(name="tests_no", type="integer", nullable=true)
     */
    private $testNo;

    /**
     * @var bool
     *
     * @ORM\Column(name="banned", type="boolean", nullable=true)
     */
    private $banned;

    /**
     * @var bool
     *
     * @ORM\Column(name="confirmed", type="boolean", nullable=true)
     */
    private $confirmed;

    /**
     * @var string
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\PostalCode")
     *
     * @ORM\JoinColumn(name="postal_codes", nullable=true)
     */
    private $postalCode;

    /**
     * @var string
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Locality")
     *
     * @ORM\JoinColumn(name="localities", nullable=true)
     */
    private $locality;

    /**
     * @var string
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\City")
     *
     * @ORM\JoinColumn(name="cities", nullable=true, onDelete="SET NULL")
     *
     */
    private $city;



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
     * Set eMail
     *
     * @param string $eMail
     *
     * @return User
     */
    public function setEMail($eMail)
    {
        $this->eMail = $eMail;

        return $this;
    }

    /**
     * Get eMail
     *
     * @return string
     */
    public function getEMail()
    {
        return $this->eMail;
    }

    /**
     * Set password
     *
     * @param string $password
     *
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set addressNo
     *
     * @param string $addressNo
     *
     * @return User
     */
    public function setAddressNo($addressNo)
    {
        $this->addressNo = $addressNo;

        return $this;
    }

    /**
     * Get addressNo
     *
     * @return string
     */
    public function getAddressNo()
    {
        return $this->addressNo;
    }

    /**
     * Set streetName
     *
     * @param string $streetName
     *
     * @return User
     */
    public function setStreetName($streetName)
    {
        $this->streetName = $streetName;

        return $this;
    }

    /**
     * Get streetName
     *
     * @return string
     */
    public function getStreetName()
    {
        return $this->streetName;
    }

    /**
     * Set registrationDate
     *
     * @param \DateTime $registrationDate
     *
     * @return User
     */
    public function setRegistrationDate($registrationDate)
    {
        $this->registrationDate = $registrationDate;

        return $this;
    }

    /**
     * Get registrationDate
     *
     * @return \DateTime
     */
    public function getRegistrationDate()
    {
        return $this->registrationDate;
    }


    /**
     * Set testNo
     *
     * @param integer $testNo
     *
     * @return User
     */
    public function setTestNo($testNo)
    {
        $this->testNo = $testNo;

        return $this;
    }

    /**
     * Get testNo
     *
     * @return int
     */
    public function getTestNo()
    {
        return $this->testNo;
    }

    /**
     * Set banned
     *
     * @param boolean $banned
     *
     * @return User
     */
    public function setBanned($banned)
    {
        $this->banned = $banned;

        return $this;
    }

    /**
     * Get banned
     *
     * @return bool
     */
    public function getBanned()
    {
        return $this->banned;
    }

    /**
     * Set confirmed
     *
     * @param boolean $confirmed
     *
     * @return User
     */
    public function setConfirmed($confirmed)
    {
        $this->confirmed = $confirmed;

        return $this;
    }

    /**
     * Get confirmed
     *
     * @return bool
     */
    public function getConfirmed()
    {
        return $this->confirmed;
    }

    /**
     * @return string
     */
    public function getPostalCode()
    {
        return $this->postalCode;
    }


    /**
     * @param string $postalCode
     */
    public function setPostalCode($postalCode)
    {
        $this->postalCode = $postalCode;
    }


    /**
     * @return string
     */
    public function getLocality()
    {
        return $this->locality;
    }

    /**
     * @param string $locality
     */
    public function setLocality($locality)
    {
        $this->locality = $locality;
    }

    /**
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param string $city
     */
    public function setCity($city)
    {
        $this->city = $city;
    }


}

