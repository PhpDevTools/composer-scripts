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

namespace Gilbertsoft\Composer\Tests\Unit;

use Composer\Script\Event;
use Gilbertsoft\Composer\AbstractScripts;
use Gilbertsoft\Composer\Util\Filesystem;

/**
 * @internal
 */
final class TestScripts extends AbstractScripts
{
    public static function testGetFilesystem(bool $recreate = false): Filesystem
    {
        return self::getFilesystem($recreate);
    }

    public static function testGetRootPath(Event $event, bool $forceConfig): string
    {
        return self::getRootPath($event, $forceConfig);
    }

    public static function testGetAbsoluteFilename(Event $event, string $filename): string
    {
        return self::getAbsoluteFilename($event, $filename);
    }
}
