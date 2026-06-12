<?php

declare(strict_types=1);

namespace ApiPlatform\Installer;

final class Templates
{
    public static function path(string $name): string
    {
        return \dirname(__DIR__).'/templates/'.$name;
    }
}
