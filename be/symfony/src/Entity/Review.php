<?php
// api/src/Entity/Review.php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;

/**
 * A review of a book.
 *
 * @ORM\Entity
 *
 * @ApiResource
 */

class Review
{
    /**
     * @var int The id of this review.
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var int The rating of this review (between 0 and 5).
     *
     * @ORM\Column(type="smallint")
     */
    public $rating;

    /**
     * @var string the body of the review.
     *
     * @ORM\Column(type="text")
     */
    public $body;

    /**
     * @var string The author of the review.
     *
     * @ORM\Column
     */
    public $author;

    /**
     * @var \DateTimeInterface The date of publication of this review.
     *
     * @ORM\Column(type="datetime")
     */
    public $publicationDate;

    /**
     * @var Book The book this review is about.
     *
     * @ORM\ManyToOne(targetEntity="Book", inversedBy="reviews")
     */
    public $book;

    public function getId(): ?int
    {
        return $this->id;
    }
}