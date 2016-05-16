<?php

namespace AppBundle\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * A book.
 * 
 * @see http://schema.org/Book Documentation on Schema.org
 * 
 * @ORM\Entity
 * @ApiResource(iri="http://schema.org/Book")
 */
class Book
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
     * @var ArrayCollection<Person> The author of this content. Please note that author is special in that HTML 5 provides a special mechanism for indicating authorship via the rel tag. That is equivalent to this and may be used interchangeably.
     * 
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Person")
     * @ORM\JoinTable(name="book_author")
     * @ApiProperty(iri="http://schema.org/author")
     */
    private $author;
    /**
     * @var \DateTime Date of first broadcast/publication.
     * 
     * @ORM\Column(type="date", nullable=true)
     * @Assert\Date
     * @ApiProperty(iri="http://schema.org/datePublished")
     */
    private $datePublished;
    /**
     * @var string A description of the item.
     * 
     * @ORM\Column(nullable=true)
     * @Assert\Type(type="string")
     * @ApiProperty(iri="http://schema.org/description")
     */
    private $description;
    /**
     * @var string Genre of the creative work or group.
     * 
     * @ORM\Column(nullable=true)
     * @Assert\Type(type="string")
     * @ApiProperty(iri="http://schema.org/genre")
     */
    private $genre;
    /**
     * @var ArrayCollection<Person> The illustrator of the book.
     * 
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Person")
     * @ORM\JoinTable(name="book_illustrator")
     * @ApiProperty(iri="http://schema.org/illustrator")
     */
    private $illustrator;
    /**
     * @var string The ISBN of the book.
     * 
     * @ORM\Column(nullable=true)
     * @Assert\Type(type="string")
     * @ApiProperty(iri="http://schema.org/isbn")
     */
    private $isbn;
    /**
     * @var string The name of the item.
     * 
     * @ORM\Column(nullable=true)
     * @Assert\Type(type="string")
     * @ApiProperty(iri="http://schema.org/name")
     */
    private $name;
    /**
     * @var int The number of pages in the book.
     * 
     * @ORM\Column(type="integer", nullable=true)
     * @Assert\Type(type="integer")
     * @ApiProperty(iri="http://schema.org/numberOfPages")
     */
    private $numberOfPages;
    /**
     * @var Organization The publisher of the creative work.
     * 
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Organization")
     * @ORM\JoinColumn(nullable=false)
     * @ApiProperty(iri="http://schema.org/publisher")
     */
    private $publisher;

    public function __construct()
    {
        $this->author = new ArrayCollection();
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
     * Adds author.
     * 
     * @param Person $author
     * 
     * @return $this
     */
    public function addAuthor(Person $author)
    {
        $this->author[] = $author;

        return $this;
    }

    /**
     * Removes author.
     * 
     * @param Person $author
     * 
     * @return $this
     */
    public function removeAuthor(Person $author)
    {
        $this->author->removeElement($author);

        return $this;
    }

    /**
     * Gets author.
     * 
     * @return ArrayCollection<Person>
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * Sets datePublished.
     * 
     * @param \DateTime $datePublished
     * 
     * @return $this
     */
    public function setDatePublished(\DateTime $datePublished = null)
    {
        $this->datePublished = $datePublished;

        return $this;
    }

    /**
     * Gets datePublished.
     * 
     * @return \DateTime
     */
    public function getDatePublished()
    {
        return $this->datePublished;
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
     * Sets genre.
     * 
     * @param string $genre
     * 
     * @return $this
     */
    public function setGenre($genre)
    {
        $this->genre = $genre;

        return $this;
    }

    /**
     * Gets genre.
     * 
     * @return string
     */
    public function getGenre()
    {
        return $this->genre;
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
        $this->illustrator->removeElement($illustrator);

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

    /**
     * Sets publisher.
     * 
     * @param Organization $publisher
     * 
     * @return $this
     */
    public function setPublisher(Organization $publisher = null)
    {
        $this->publisher = $publisher;

        return $this;
    }

    /**
     * Gets publisher.
     * 
     * @return Organization
     */
    public function getPublisher()
    {
        return $this->publisher;
    }
}
