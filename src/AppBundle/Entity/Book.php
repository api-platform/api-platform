<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Dunglas\ApiBundle\Annotation\Iri;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * A book.
 * 
 * @see http://schema.org/Book Documentation on Schema.org
 * 
 * @ORM\Entity
 * @Iri("http://schema.org/Book")
 */
class Book extends CreativeWork
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
     * @var ArrayCollection<Person> The illustrator of the book.
     * 
     * @ORM\ManyToMany(targetEntity="Person")
     * @Iri("https://schema.org/illustrator")
     */
    private $illustrator;
    /**
     * @var string The ISBN of the book.
     * 
     * @ORM\Column(nullable=true)
     * @Assert\Type(type="string")
     * @Iri("https://schema.org/isbn")
     */
    private $isbn;
    /**
     * @var int The number of pages in the book.
     * 
     * @ORM\Column(type="integer", nullable=true)
     * @Assert\Type(type="integer")
     * @Iri("https://schema.org/numberOfPages")
     */
    private $numberOfPages;

    public function __construct()
    {
        parent::__construct();

        $this->illustrator = new ArrayCollection();
    }

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
     * Adds illustrator.
     * 
     * @param Person $illustrator
     * 
     * @return $this
     */
    public function addIllustrator(Person $illustrator)
    {
        $this->illustrator[] = $illustrator;

        return $this;
    }

    /**
     * Removes illustrator.
     * 
     * @param Person $illustrator
     * 
     * @return $this
     */
    public function removeIllustrator(Person $illustrator)
    {
        $key = array_search($illustrator, $this->illustrator, true);
        if (false !== $key) {
            unset($this->illustrator[$key]);
        }

        return $this;
    }

    /**
     * Gets illustrator.
     * 
     * @return ArrayCollection<Person>
     */
    public function getIllustrator()
    {
        return $this->illustrator;
    }

    /**
     * Sets isbn.
     * 
     * @param string $isbn
     * 
     * @return $this
     */
    public function setIsbn($isbn)
    {
        $this->isbn = $isbn;

        return $this;
    }

    /**
     * Gets isbn.
     * 
     * @return string
     */
    public function getIsbn()
    {
        return $this->isbn;
    }

    /**
     * Sets numberOfPages.
     * 
     * @param int $numberOfPages
     * 
     * @return $this
     */
    public function setNumberOfPages($numberOfPages)
    {
        $this->numberOfPages = $numberOfPages;

        return $this;
    }

    /**
     * Gets numberOfPages.
     * 
     * @return int
     */
    public function getNumberOfPages()
    {
        return $this->numberOfPages;
    }
}
