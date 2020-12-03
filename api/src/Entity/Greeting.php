<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * This is a dummy entity. Remove it!
 *
 * @ORM\Entity
 */
#[ApiResource(mercure: true)]
class Greeting
{
    /**
     * The entity ID
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;

    /**
     * A nice person
     *
     * @ORM\Column
     * @Assert\NotBlank
     */
    public string $name = '';

    public function getId(): ?int
    {
        return $this->id;
    }
}
