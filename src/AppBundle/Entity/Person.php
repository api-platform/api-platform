<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Dunglas\ApiBundle\Annotation\Iri;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * A person (alive, dead, undead, or fictional).
 * 
 * @see http://schema.org/Person Documentation on Schema.org
 * 
 * @ORM\Entity
 * @Iri("http://schema.org/Person")
 */
class Person
{
    /**
     * @var int
     * 
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    /**
     * @var \DateTime Date of birth.
     * 
     * @ORM\Column(type="date", nullable=true)
     * @Assert\Date
     * @Iri("https://schema.org/birthDate")
     */
    private $birthDate;
    /**
     * @var string A short description of the item.
     * 
     * @ORM\Column(nullable=true)
     * @Assert\Type(type="string")
     * @Iri("https://schema.org/description")
     */
    private $description;
    /**
     * @var string Gender of the person.
     * 
     * @ORM\Column(nullable=true)
     * @Assert\Type(type="string")
     * @Iri("https://schema.org/gender")
     */
    private $gender;
    /**
     * @var string The name of the item.
     * 
     * @ORM\Column(nullable=true)
     * @Assert\Type(type="string")
     * @Iri("https://schema.org/name")
     */
    private $name;
    /**
     * @var string URL of the item.
     * 
     * @ORM\Column(nullable=true)
     * @Assert\Url
     * @Iri("https://schema.org/url")
     */
    private $url;

    /**
     * Sets id.
     * 
     * @param int $id
     * 
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Gets id.
     * 
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Sets birthDate.
     * 
     * @param \DateTime $birthDate
     * 
     * @return $this
     */
    public function setBirthDate(\DateTime $birthDate = null)
    {
        $this->birthDate = $birthDate;

        return $this;
    }

    /**
     * Gets birthDate.
     * 
     * @return \DateTime
     */
    public function getBirthDate()
    {
        return $this->birthDate;
    }

    /**
     * Sets description.
     * 
     * @param string $description
     * 
     * @return $this
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Gets description.
     * 
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Sets gender.
     * 
     * @param string $gender
     * 
     * @return $this
     */
    public function setGender($gender)
    {
        $this->gender = $gender;

        return $this;
    }

    /**
     * Gets gender.
     * 
     * @return string
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * Sets name.
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
     * Gets name.
     * 
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Sets url.
     * 
     * @param string $url
     * 
     * @return $this
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Gets url.
     * 
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }
}
