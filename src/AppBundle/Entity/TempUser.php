<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TempUser
 *
 * @ORM\Table(name="temp_user")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TempUserRepository")
 */
class TempUser
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
     * @ORM\Column(name="eMail", type="string", length=255, unique=true)
     */
    private $eMail;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=255)
     */
    private $password;

    /**
     * @var string
     *
     * @ORM\Column(name="token", type="string", length=255)
     */
    private $token;

    /**
     * @var string
     *
     * @ORM\Column(name="userType", type="string", length=255)
     */
    private $userType;


    /**
     * @var datetime
     *
     * @ORM\Column(name="firstRegistrationDate", type="datetime")
     */
    private $firstRegisterDate;


    public function __construct()
    {
        $this->firstRegisterDate = new \DateTime();
    }


    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set eMail.
     *
     * @param string $eMail
     *
     * @return TempUser
     */
    public function setEMail($eMail)
    {
        $this->eMail = $eMail;

        return $this;
    }

    /**
     * Get eMail.
     *
     * @return string
     */
    public function getEMail()
    {
        return $this->eMail;
    }

    /**
     * Set password.
     *
     * @param string $password
     *
     * @return TempUser
     */
    public function setPassword($password)
    {

        $options = [
            'cost' => 12,
        ];

        $this->password = password_hash($password, PASSWORD_BCRYPT, $options);


    }

    /**
     * Get password.
     *
     * @return string
     */
    public function getPassword()
    {

        return $this->password;
    }

    /**
     * Set token.
     *
     *
     * @return TempUser
     */
    public function setToken()
    {

        $date = $this->firstRegisterDate->format('d-m-Y');

        $token = $this->password.$this->eMail.$date;

        $this->token = sha1($token);

    }

    /**
     * Get token.
     *
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Set userType.
     *
     * @param string $userType
     *
     * @return TempUser
     */
    public function setUserType($userType)
    {
        $this->userType = $userType;

        return $this;
    }

    /**
     * Get userType.
     *
     * @return string
     */
    public function getUserType()
    {
        return $this->userType;
    }

    /**
     * @return mixed
     */
    public function getFirstRegisterDate()
    {
        return $this->firstRegisterDate;
    }

    /**
     * @param mixed $firstRegisterDate
     * @return $fi
     */
    public function setFirstRegisterDate($firstRegisterDate)
    {
        $this->firstRegisterDate = $firstRegisterDate;

        return $this;

    }




}
