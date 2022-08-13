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

use Composer\Composer;
use Composer\Config;
use Composer\Config\ConfigSourceInterface;
use Composer\Config\JsonConfigSource;
use Composer\Script\Event;
use InvalidArgumentException;
use Iterator;
use RuntimeException;
use UnexpectedValueException;

/**
 * @covers \Gilbertsoft\Composer\AbstractReleaseScripts
 * @uses \Gilbertsoft\Composer\AbstractScripts
 * @uses \Gilbertsoft\Composer\Release\FileReplaceVersionItem
 * @uses \Gilbertsoft\Composer\Util\Filesystem
 */
final class ReleaseScriptsTest extends TestCase
{
    private function getFileContents(string $filename): string
    {
        if (($content = file_get_contents($filename)) === false) {
            self::fail(sprintf('File "%s" not found.', $filename));
        }

        return $content;
    }

    public function testSetVersion(): void
    {
        $eventProphecy = $this->prophesize(Event::class);
        $eventProphecy->getArguments()->willReturn(['11.22.33']);

        TestReleaseScripts::setVersion($eventProphecy->reveal());

        self::assertStringContainsString(
            '"test-vendor/test-package-required": "^11.22.33"',
            $this->getFileContents('composer.json')
        );
        self::assertStringContainsString(
            '- COMPOSER_ROOT_VERSION=11.22.33',
            $this->getFileContents('ddev/config.yaml')
        );
        self::assertStringContainsString(
            'COMPOSER_ROOT_VERSION: 11.22.33',
            $this->getFileContents('github/workflows/continuous-integration.yml')
        );
        self::assertStringContainsString(
            '"dev-main": "11.22.x-dev"',
            $this->getFileContents('composer.json')
        );
    }

    public function testSetVersionThrowsOnMissingVersion(): void
    {
        $eventProphecy = $this->prophesize(Event::class);
        $eventProphecy->getArguments()->willReturn([]);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(
            'A valid version number must be provided as an argument, e.g. `composer set-version 1.2.3`.'
        );

        TestReleaseScripts::setVersion($eventProphecy->reveal());
    }

    /**
     * @dataProvider versionProvider
     */
    public function testExtractVersionCore(
        string $semver,
        int $expectedMajor,
        int $expectedMinor,
        int $expectedPatch,
        string $expectedPrerelease,
        string $expectedBuildmetadata
    ): void {
        $major = 0;
        $minor = 0;
        $patch = 0;
        $prerelease = '';
        $buildmetadata = '';

        TestReleaseScripts::testExtractVersionParts($semver, $major, $minor, $patch, $prerelease, $buildmetadata);

        self::assertSame($expectedMajor, $major);
        self::assertSame($expectedMinor, $minor);
        self::assertSame($expectedPatch, $patch);
        self::assertSame($expectedPrerelease, $prerelease);
        self::assertSame($expectedBuildmetadata, $buildmetadata);
    }

    /**
     * @return Iterator<string, array{
     *   rawVersion: string,
     *   expectedMajor: int,
     *   expectedMinor: int,
     *   expectedPatch: int,
     *   expectedPrerelease: string,
     *   expectedBuildmetadata: string
     * }>
     */
    public function versionProvider(): Iterator
    {
        yield 'simple version' => [
            'rawVersion' => '1.2.3',
            'expectedMajor' => 1,
            'expectedMinor' => 2,
            'expectedPatch' => 3,
            'expectedPrerelease' => '',
            'expectedBuildmetadata' => '',
        ];
        yield 'sem ver with prefix' => [
            'rawVersion' => 'v1.2.3-alpha',
            'expectedMajor' => 1,
            'expectedMinor' => 2,
            'expectedPatch' => 3,
            'expectedPrerelease' => 'alpha',
            'expectedBuildmetadata' => '',
        ];
        yield 'high version' => [
            'rawVersion' => '999999.999999.999999',
            'expectedMajor' => 999999,
            'expectedMinor' => 999999,
            'expectedPatch' => 999999,
            'expectedPrerelease' => '',
            'expectedBuildmetadata' => '',
        ];
    }

    public function testExtractVersionCoreThrowsOnMissingVersion(): void
    {
        $major = 0;
        $minor = 0;
        $patch = 0;
        $prerelease = '';
        $buildmetadata = '';

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionCode(1654777706);
        $this->expectExceptionMessage(
            'The parameter semver must be not be empty.'
        );

        TestReleaseScripts::testExtractVersionParts('', $major, $minor, $patch, $prerelease, $buildmetadata);
    }

    public function testExtractVersionCoreThrowsOnInvalidVersion(): void
    {
        $major = 0;
        $minor = 0;
        $patch = 0;
        $prerelease = '';
        $buildmetadata = '';

        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionCode(1654777707);
        $this->expectExceptionMessage(
            '"v20100102" is no valid version number.'
        );

        TestReleaseScripts::testExtractVersionParts('v20100102', $major, $minor, $patch, $prerelease, $buildmetadata);
    }

    public function testFileReplaceVersion(): void
    {
        $jsonConfigSourceProphecy = $this->prophesize(JsonConfigSource::class);
        $jsonConfigSourceProphecy->willImplement(ConfigSourceInterface::class);
        $jsonConfigSourceProphecy->getName()->willReturn('composer.json');

        $configProphecy = $this->prophesize(Config::class);
        $configProphecy->getConfigSource()->willReturn($jsonConfigSourceProphecy->reveal());

        $composerProphecy = $this->prophesize(Composer::class);
        $composerProphecy->getConfig()->willReturn($configProphecy->reveal());

        $eventProphecy = $this->prophesize(Event::class);
        $eventProphecy->getComposer()->willReturn($composerProphecy->reveal());

        TestReleaseScripts::testFileReplaceVersion(
            'composer.json',
            '/("test-vendor\/test-package-required": "\^)\d+.\d+.\d+(")/',
            '999.888.777'
        );

        self::assertStringContainsString(
            '999.888.777',
            $this->getFileContents('composer.json')
        );

        // Test early return
        TestReleaseScripts::testFileReplaceVersion(
            'composer.json',
            '/("test-vendor\/test-package-required": "\^)\d+.\d+.\d+(")/',
            '999.888.777'
        );
    }

    public function testFileReplaceVersionThrowsOnAbsolutePath(): void
    {
        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionCode(1654777710);
        $this->expectExceptionMessage(
            'The parameter filename should be relative to the root composer.json, "/composer.json" was given.'
        );

        TestReleaseScripts::testFileReplaceVersion(
            '/composer.json',
            '',
            ''
        );
    }
}
