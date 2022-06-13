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
use UnexpectedValueException;

/**
 * @covers \Gilbertsoft\Composer\AbstractScripts
 * @uses \Gilbertsoft\Composer\Util\Filesystem
 */
final class ScriptsTest extends TestCase
{
    public function testGetFilesystem(): void
    {
        self::assertSame(TestScripts::testGetFilesystem(true), TestScripts::testGetFilesystem());
    }

    public function testGetRootPath(): void
    {
        $jsonConfigSourceProphecy = $this->prophesize(JsonConfigSource::class);
        $jsonConfigSourceProphecy->willImplement(ConfigSourceInterface::class);
        $jsonConfigSourceProphecy->getName()->willReturn(self::getTestPath() . '/composer.json');

        $configProphecy = $this->prophesize(Config::class);
        $configProphecy->getConfigSource()->willReturn($jsonConfigSourceProphecy->reveal());

        $composerProphecy = $this->prophesize(Composer::class);
        $composerProphecy->getConfig()->willReturn($configProphecy->reveal());

        $eventProphecy = $this->prophesize(Event::class);
        $eventProphecy->getComposer()->willReturn($composerProphecy->reveal());

        self::assertSame(
            self::getTestPath(),
            TestScripts::testGetRootPath($eventProphecy->reveal(), false)
        );

        self::assertSame(
            self::getTestPath(),
            TestScripts::testGetRootPath($eventProphecy->reveal(), true)
        );
    }

    public function testGetAbsoluteFilename(): void
    {
        $eventProphecy = $this->prophesize(Event::class);

        self::assertSame(
            self::getTestPath() . '/test',
            TestScripts::testGetAbsoluteFilename($eventProphecy->reveal(), 'test')
        );
    }

    public function testGetAbsoluteFilenameThrowsOnAbsoluteFilename(): void
    {
        $jsonConfigSourceProphecy = $this->prophesize(JsonConfigSource::class);
        $jsonConfigSourceProphecy->willImplement(ConfigSourceInterface::class);
        $jsonConfigSourceProphecy->getName()->willReturn(self::getTestPath() . '/composer.json');

        $configProphecy = $this->prophesize(Config::class);
        $configProphecy->getConfigSource()->willReturn($jsonConfigSourceProphecy->reveal());

        $composerProphecy = $this->prophesize(Composer::class);
        $composerProphecy->getConfig()->willReturn($configProphecy->reveal());

        $eventProphecy = $this->prophesize(Event::class);
        $eventProphecy->getComposer()->willReturn($composerProphecy->reveal());

        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionCode(1654777710);
        $this->expectExceptionMessage(
            'The parameter filename should be relative to the root composer.json, "/tmp/test" was given.'
        );

        TestScripts::testGetAbsoluteFilename($eventProphecy->reveal(), '/tmp/test');
    }
}
