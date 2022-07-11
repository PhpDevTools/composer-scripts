<?php

declare(strict_types=1);

/*
 * This file is part of the gilbertsoft/composer-scripts package.
 *
 * (c) Gilbertsoft LLC (gilbertsoft.org)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gilbertsoft\Composer;

use Composer\Script\Event;
use Gilbertsoft\Composer\Util\Filesystem;
use UnexpectedValueException;

abstract class AbstractScripts
{
    /**
     * @var \Gilbertsoft\Composer\Util\Filesystem|null
     */
    private static $filesystem;

    protected static function getFilesystem(bool $recreate = false): Filesystem
    {
        if (self::$filesystem === null || $recreate) {
            self::$filesystem = new Filesystem();
        }

        return self::$filesystem;
    }

    /**
     * @param bool $forceConfig Forces to read the path from the config instead of cwd.
     */
    protected static function getRootPath(Event $event, bool $forceConfig = false): string
    {
        if (!$forceConfig) {
            // @todo replace with Platform::getCwd(true) once Composer lower 2.3 is not supported anymore
            // return Platform::getCwd(true);
            if (($cwd = getcwd()) === false) {
                // @codeCoverageIgnoreStart
                return '';
                // @codeCoverageIgnoreEnd
            }

            return $cwd;
        }

        return dirname($event->getComposer()->getConfig()->getConfigSource()->getName());
    }

    /**
     * @throws UnexpectedValueException
     */
    protected static function getAbsoluteFilename(Event $event, string $filename): string
    {
        if (self::getFilesystem()->isAbsolutePath($filename)) {
            throw new UnexpectedValueException(
                sprintf(
                    'The parameter filename should be relative to the root composer.json, "%s" was given.',
                    $filename
                ),
                1654777710
            );
        }

        return self::getRootPath($event) . '/' . $filename;
    }
}
