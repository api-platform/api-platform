<?php

namespace App\ApiResource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use Symfony\Component\Serializer\Attribute\Groups;

#[ApiResource(
    operations: [
        new Get(),
        new GetCollection(
            provider: Greetings::class . '::provide',
        ),
    ],
    normalizationContext: ['groups' => ['greetings:read']],
)]
class Greetings
{
    #[Groups(['greetings:read'])]
    public string $hello = '';

    /**
     * @return array<Greetings>
     */
    public static function provide(): array
    {
        $greeting = new self();
        $greeting->hello = 'World!';

        return [$greeting];
    }
}
