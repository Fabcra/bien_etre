<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Abuse
 *
 * @ORM\Table(name="abuses")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\abuseRepository")
 */
class Abuse
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
     * @ORM\Column(name="descriptions", type="string", length=255)
     */
    private $description;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="insert_dates", type="datetime")
     */
    private $insertDate;

    /**
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Member", inversedBy="abuses")
     *
     * @ORM\JoinColumn(name="members")
     *
     */
    private $member;


    /**
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Comment", inversedBy="abuses")
     *
     * @ORM\JoinColumn(name="comments")
     *
     */
    private $comment;


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
     * Set description
     *
     * @param string $description
     *
     * @return abuse
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
     * Set insertDate
     *
     * @param \DateTime $insertDate
     *
     * @return abuse
     */
    public function setInsertDate($insertDate)
    {
        $this->insertDate = $insertDate;

        return $this;
    }

    /**
     * Get insertDate
     *
     * @return \DateTime
     */
    public function getInsertDate()
    {
        return $this->insertDate;
    }


    /**
     * @return mixed
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * @param mixed $comment
     */
    public function setComment($comment)
    {
        $this->comment = $comment;
    }

    /**
     * @return mixed
     */
    public function getMember()
    {
        return $this->member;
    }

    /**
     * @param mixed $member
     */
    public function setMember($member)
    {
        $this->member = $member;
    }
}

