<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;


/**
 * Member
 *
 * @ORM\Table(name="Members")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\MemberRepository")
 */
class Member extends User
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
     * @ORM\Column(name="last_names", type="string", length=255, nullable=true)
     */
    private $lastName;

    /**
     * @var string
     *
     * @ORM\Column(name="first_names", type="string", length=255, nullable=true)
     */
    private $firstName;

    /**
     * @var bool
     *
     * @ORM\Column(name="newsletters", type="boolean")
     */
    private $newsletter;


    /**
     * @var
     *
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\Image")
     *
     * @ORM\JoinColumn(name="avatars")
     */
    private $avatar;


    /**
     * @var
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Comment", mappedBy="member")
     *
     */
    private $comments;

    /**
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Abuse", mappedBy="member")
     *
     */
    private $abuses;



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
     * Set lastName
     *
     * @param string $lastName
     *
     * @return string
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Get lastName
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Set firstName
     *
     * @param string $firstName
     *
     * @return string
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Get firstName
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set newsletter
     *
     * @param boolean $newsletter
     *
     * @return boolean
     */
    public function setNewsletter($newsletter)
    {
        $this->newsletter = $newsletter;

        return $this;
    }

    /**
     * Get newsletter
     *
     * @return bool
     */
    public function getNewsletter()
    {
        return $this->newsletter;
    }

    /**
     * @return mixed
     */
    public function getAvatar()
    {
        return $this->avatar;
    }

    /**
     * @param mixed $avatar
     */
    public function setAvatar($avatar)
    {
        $this->avatar = $avatar;
    }

    /**
     * @return mixed
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * @param mixed $comments
     */
    public function setComments($comments)
    {
        $this->comments = $comments;
    }

    /**
     * @return mixed
     */
    public function getAbuses()
    {
        return $this->abuses;
    }

    /**
     * @param mixed $abuses
     */
    public function setAbuses($abuses)
    {
        $this->abuses = $abuses;
    }


}

