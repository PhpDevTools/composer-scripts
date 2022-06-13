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

namespace Gilbertsoft\Composer\Tests\Unit\Release;

use BadMethodCallException;
use Gilbertsoft\Composer\Release\FileReplaceVersionItem;
use Gilbertsoft\Composer\Tests\Unit\TestCase;
use Iterator;

/**
 * @covers \Gilbertsoft\Composer\Release\FileReplaceVersionItem
 */
final class FileReplaceVersionItemTest extends TestCase
{
    /**
     * @dataProvider fileProvider
     */
    public function testCreationAndGetters(
        string $filename,
        string $pattern,
        int $versionFormat,
        int $major,
        int $minor,
        int $patch,
        string $prerelease,
        string $buildmetadata,
        string $expectedReplacement
    ): void {
        $fileReplaceVersionItem = new FileReplaceVersionItem($filename, $pattern, $versionFormat);
        $fileReplaceVersionItem->setVersions($major, $minor, $patch, $prerelease, $buildmetadata);

        self::assertSame($filename, $fileReplaceVersionItem->getName());
        self::assertSame($pattern, $fileReplaceVersionItem->getPattern());
        self::assertSame($expectedReplacement, $fileReplaceVersionItem->getReplacement());
    }

    /**
     * @return Iterator<string, array{
     *   filename: string,
     *   pattern: string,
     *   versionFormat: int,
     *   major: int,
     *   minor: int,
     *   patch: int,
     *   prerelease: string,
     *   buildmetadata: string,
     *   expectedReplacement: string,
     * }>
     */
    public function fileProvider(): Iterator
    {
        yield 'major part' => [
            'filename' => 'filename1',
            'pattern' => '/\d+\.\d+\.\d+/',
            'versionFormat' => FileReplaceVersionItem::VERSION_PART_MAJOR,
            'major' => 1,
            'minor' => 2,
            'patch' => 3,
            'prerelease' => '4',
            'buildmetadata' => '5',
            'expectedReplacement' => '1',
        ];
        yield 'minor part' => [
            'filename' => 'filename2',
            'pattern' => '/\d+\.\d+\.\d+/',
            'versionFormat' => FileReplaceVersionItem::VERSION_PART_MINOR,
            'major' => 1,
            'minor' => 2,
            'patch' => 3,
            'prerelease' => '4',
            'buildmetadata' => '5',
            'expectedReplacement' => '2',
        ];
        yield 'patch part' => [
            'filename' => 'filename3',
            'pattern' => '/\d+\.\d+\.\d+/',
            'versionFormat' => FileReplaceVersionItem::VERSION_PART_PATCH,
            'major' => 1,
            'minor' => 2,
            'patch' => 3,
            'prerelease' => '4',
            'buildmetadata' => '5',
            'expectedReplacement' => '3',
        ];
        yield 'pre-release part' => [
            'filename' => 'filename4',
            'pattern' => '/\d+\.\d+\.\d+/',
            'versionFormat' => FileReplaceVersionItem::VERSION_PART_PRERELEASE,
            'major' => 1,
            'minor' => 2,
            'patch' => 3,
            'prerelease' => '4',
            'buildmetadata' => '5',
            'expectedReplacement' => '4',
        ];
        yield 'build part' => [
            'filename' => 'filename5',
            'pattern' => '/\d+\.\d+\.\d+/',
            'versionFormat' => FileReplaceVersionItem::VERSION_PART_BUILDMETADATA,
            'major' => 1,
            'minor' => 2,
            'patch' => 3,
            'prerelease' => '4',
            'buildmetadata' => '5',
            'expectedReplacement' => '5',
        ];
        yield 'major version' => [
            'filename' => 'filename1',
            'pattern' => '/\d+\.\d+\.\d+/',
            'versionFormat' => FileReplaceVersionItem::VERSIONS_UP_TO_MAJOR,
            'major' => 1,
            'minor' => 2,
            'patch' => 3,
            'prerelease' => '4',
            'buildmetadata' => '5',
            'expectedReplacement' => '1',
        ];
        yield 'minor version' => [
            'filename' => 'filename2',
            'pattern' => '/\d+\.\d+\.\d+/',
            'versionFormat' => FileReplaceVersionItem::VERSIONS_UP_TO_MINOR,
            'major' => 1,
            'minor' => 2,
            'patch' => 3,
            'prerelease' => '4',
            'buildmetadata' => '5',
            'expectedReplacement' => '1.2',
        ];
        yield 'patch version' => [
            'filename' => 'filename3',
            'pattern' => '/\d+\.\d+\.\d+/',
            'versionFormat' => FileReplaceVersionItem::VERSIONS_UP_TO_PATCH,
            'major' => 1,
            'minor' => 2,
            'patch' => 3,
            'prerelease' => '4',
            'buildmetadata' => '5',
            'expectedReplacement' => '1.2.3',
        ];
        yield 'pre-release version' => [
            'filename' => 'filename4',
            'pattern' => '/\d+\.\d+\.\d+/',
            'versionFormat' => FileReplaceVersionItem::VERSIONS_UP_TO_PRERELEASE,
            'major' => 1,
            'minor' => 2,
            'patch' => 3,
            'prerelease' => '4',
            'buildmetadata' => '5',
            'expectedReplacement' => '1.2.3-4',
        ];
        yield 'build version' => [
            'filename' => 'filename5',
            'pattern' => '/\d+\.\d+\.\d+/',
            'versionFormat' => FileReplaceVersionItem::VERSIONS_UP_TO_BUILDMETADATA,
            'major' => 1,
            'minor' => 2,
            'patch' => 3,
            'prerelease' => '4',
            'buildmetadata' => '5',
            'expectedReplacement' => '1.2.3-4+5',
        ];
    }

    public function testGetReplacementThrowsOnMissingSetVersionsCall(): void
    {
        $fileReplaceVersionItem = new FileReplaceVersionItem('', '', 0);

        $this->expectException(BadMethodCallException::class);
        $this->expectExceptionCode(1654889886);
        $this->expectExceptionMessage('Call to previous setVersions() is missing.');

        $fileReplaceVersionItem->getReplacement();
    }
}
