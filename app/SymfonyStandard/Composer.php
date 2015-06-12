<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymfonyStandard;

use Composer\Script\CommandEvent;

class Composer
{
    public static function hookRootPackageInstall(CommandEvent $event)
    {
        $event->getComposer()->getEventDispatcher()->addSubscriber(new RootPackageInstallSubscriber());
    }
}
