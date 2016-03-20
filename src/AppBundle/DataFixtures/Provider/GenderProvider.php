<?php

namespace AppBundle\DataFixtures\Provider;

use Faker\Provider\Base as BaseProvider;

/**
 * @author Théo FIDRY <theo.fidry@gmail.com>
 */
final class GenderProvider
{
    const GENDERS = ['male', 'female'];

    public static function gender()
    {
        return BaseProvider::randomElement(self::GENDERS);
    }
}
