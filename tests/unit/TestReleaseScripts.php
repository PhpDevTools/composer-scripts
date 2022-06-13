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
use Gilbertsoft\Composer\Release\FileReplaceVersionItem;
use Gilbertsoft\Composer\Release\FileReplaceVersionItemInterface;
use Gilbertsoft\Composer\AbstractReleaseScripts;
use Iterator;

/**
 * @internal
 */
final class TestReleaseScripts extends AbstractReleaseScripts
{
    public static function testExtractVersionParts(
        string $semver,
        int &$major,
        int &$minor,
        int &$patch,
        string &$prerelease,
        string &$buildmetadata
    ): void {
        self::extractVersionParts($semver, $major, $minor, $patch, $prerelease, $buildmetadata);
    }

    public static function testFileReplaceVersion(string $filename, string $pattern, string $version): void
    {
        self::fileReplaceVersion($filename, $pattern, $version);
    }

    public static function testGetAbsoluteFilename(Event $event, string $filename): string
    {
        return self::getAbsoluteFilename($event, $filename);
    }

    /**
     * @return Iterator<FileReplaceVersionItemInterface>
     */
    public static function testGetFiles(): Iterator
    {
        return self::getFiles();
    }

    /**
     * @inheritDoc
     */
    protected static function getFiles(): Iterator
    {
        yield new FileReplaceVersionItem(
            'composer.json',
            '/("test-vendor\/test-package-required": "\^)\d+.\d+.\d+(")/',
            FileReplaceVersionItem::VERSIONS_UP_TO_PATCH
        );
        yield new FileReplaceVersionItem(
            'ddev/config.yaml',
            '/(- COMPOSER_ROOT_VERSION=)\d+.\d+.\d+()/',
            FileReplaceVersionItem::VERSIONS_UP_TO_PATCH
        );
        yield new FileReplaceVersionItem(
            'github/workflows/continuous-integration.yml',
            '/(COMPOSER_ROOT_VERSION: )\d+.\d+.\d+()/',
            FileReplaceVersionItem::VERSIONS_UP_TO_PATCH
        );
        yield new FileReplaceVersionItem(
            'composer.json',
            '/("dev-main": ")\d+.\d+(.x-dev")/',
            FileReplaceVersionItem::VERSIONS_UP_TO_MINOR
        );
    }
}
