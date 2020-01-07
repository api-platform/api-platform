<?php

declare(strict_types=1);

namespace App\Model;

abstract class Command
{
    /**
     * @var array
     */
    protected $metadata = [];

    /**
     * @var array
     */
    protected $payload = [];

    abstract public static function fromPayload(array $payload);

    /**
     * @param array $payload
     * @throws \Exception
     */
    abstract public static function assertIsValidPayload(array $payload);

    public function withAddedMetadata(string $key, $value): void
    {
        $this->metadata[$key] = $value;
    }

    public function metadata(): array
    {
        return $this->metadata;
    }
}
