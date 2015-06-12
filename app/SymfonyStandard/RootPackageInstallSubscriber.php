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

use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\Script\ScriptEvents;
use Composer\Script\CommandEvent;
use Sensio\Bundle\DistributionBundle\Composer\ScriptHandler;

class RootPackageInstallSubscriber implements EventSubscriberInterface
{
    public static function installAcmeDemoBundle(CommandEvent $event)
    {
        ScriptHandler::installAcmeDemoBundle($event);
    }

    public static function setupNewDirectoryStructure(CommandEvent $event)
    {
        ScriptHandler::defineDirectoryStructure($event);
    }

    public static function getSubscribedEvents()
    {
        return array(
            ScriptEvents::POST_INSTALL_CMD => array(
                array('setupNewDirectoryStructure', 512),
                array('installAcmeDemoBundle', 0)
            ),
        );
    }
}
