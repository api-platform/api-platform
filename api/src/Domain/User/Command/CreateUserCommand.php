<?php

declare(strict_types=1);

namespace App\Domain\User\Command;

use App\Model\Command;
use Assert\Assertion;

final class CreateUserCommand extends Command
{
    private $username;
    private $password;
    private $firstname;
    private $lastname;
    private $roles;

    private function __construct()
    {
    }

    public function username(): string
    {
        return $this->username;
    }

    public function password(): string
    {
        return $this->password;
    }

    public function firstname(): string
    {
        return $this->firstname;
    }

    public function lastname(): string
    {
        return $this->lastname;
    }

    public function roles(): array
    {
        return $this->roles;
    }

    public static function fromPayload(array $payload)
    {
        static::assertIsValidPayload($payload);
        $self = new self();
        $self->username = $payload['username'];
        $self->password = $payload['password'];
        $self->firstname = $payload['firstname'];
        $self->lastname = $payload['lastname'];
        $self->roles = $payload['roles'];

        return $self;
    }

    /**
     * {@inheritdoc}
     */
    public static function assertIsValidPayload(array $payload)
    {
        Assertion::string($payload['username']);
        Assertion::string($payload['password']);
        Assertion::string($payload['firstname']);
        Assertion::string($payload['lastname']);
        Assertion::isArray($payload['roles']);
    }

    /**
     * @param string $username
     * @param string $password
     * @param string $firstname
     * @param string $lastname
     * @param array  $roles
     *
     * @return CreateUserCommand
     */
    public static function fromParams(string $username, string $password, string $firstname, string $lastname, array $roles): self
    {
        $self = new self();
        $self->username = $username;
        $self->password = $password;
        $self->firstname = $firstname;
        $self->lastname = $lastname;
        $self->roles = $roles;

        return $self;
    }
}
