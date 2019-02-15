<?php

/*
 * This file is part of the Symfony MakerBundle package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Bundle\MakerBundle\Util;

use Composer\Autoload\ClassLoader;
use Symfony\Component\Debug\DebugClassLoader;

/**
 * @internal
 */
class ComposerAutoloaderFinder
{
    /**
     * @var ClassLoader|null
     */
    private $classLoader = null;

    public function getClassLoader(): ClassLoader
    {
        if (null === $this->classLoader) {
            $this->classLoader = $this->findComposerClassLoader();
        }

        if (null === $this->classLoader) {
            throw new \Exception('Composer ClassLoader not found!');
        }

        return $this->classLoader;
    }

    /**
     * @return ClassLoader|null
     */
    private function findComposerClassLoader()
    {
        $autoloadFunctions = spl_autoload_functions();

        foreach ($autoloadFunctions as $autoloader) {
            if (\is_array($autoloader) && isset($autoloader[0]) && \is_object($autoloader[0])) {
                if ($autoloader[0] instanceof ClassLoader) {
                    return $autoloader[0];
                }

                if ($autoloader[0] instanceof DebugClassLoader
                    && \is_array($autoloader[0]->getClassLoader())
                    && $autoloader[0]->getClassLoader()[0] instanceof ClassLoader) {
                    return $autoloader[0]->getClassLoader()[0];
                }
            }
        }

        return null;
    }
}
