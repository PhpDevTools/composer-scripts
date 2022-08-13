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

use Gilbertsoft\PHPUnit\Tools\ComposerFilesystemTrait;
use Gilbertsoft\PHPUnit\Tools\DedicatedTestPathTrait;
use Gilbertsoft\PHPUnit\Tools\FileFixturesTrait;
use PHPUnit\Framework\TestCase as BaseTestCase;
use Prophecy\PhpUnit\ProphecyTrait;

abstract class TestCase extends BaseTestCase
{
    use ProphecyTrait;
    use ComposerFilesystemTrait;
    use DedicatedTestPathTrait;
    use FileFixturesTrait;

    public static function setUpBeforeClass(): void
    {
        self::registerFixturesPath(__DIR__ . '/Fixtures');
    }

    protected function setUp(): void
    {
        self::createFixtures(self::getTestPath(), [
            'composer.json' => 'composer.json',
            'config.yaml' => 'ddev/config.yaml',
            'continuous-integration.yml' => 'github/workflows/continuous-integration.yml',
        ]);
    }
}
