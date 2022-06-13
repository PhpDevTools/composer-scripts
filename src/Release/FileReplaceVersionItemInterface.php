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

namespace Gilbertsoft\Composer\Release;

interface FileReplaceVersionItemInterface extends FileReplaceItemInterface
{
    /**
     * @var int
     */
    public const VERSION_PART_MAJOR = 1;

    /**
     * @var int
     */
    public const VERSION_PART_MINOR = 2;

    /**
     * @var int
     */
    public const VERSION_PART_PATCH = 4;

    /**
     * @var int
     */
    public const VERSION_PART_PRERELEASE = 8;

    /**
     * @var int
     */
    public const VERSION_PART_BUILDMETADATA = 16;

    /**
     * @var int
     */
    public const VERSIONS_UP_TO_MAJOR =
        self::VERSION_PART_MAJOR;

    /**
     * @var int
     */
    public const VERSIONS_UP_TO_MINOR =
        self::VERSIONS_UP_TO_MAJOR |
        self::VERSION_PART_MINOR;

    /**
     * @var int
     */
    public const VERSIONS_UP_TO_PATCH =
        self::VERSIONS_UP_TO_MINOR |
        self::VERSION_PART_PATCH;

    /**
     * @var int
     */
    public const VERSIONS_UP_TO_PRERELEASE =
        self::VERSIONS_UP_TO_PATCH |
        self::VERSION_PART_PRERELEASE;

    /**
     * @var int
     */
    public const VERSIONS_UP_TO_BUILDMETADATA =
        self::VERSIONS_UP_TO_PRERELEASE |
        self::VERSION_PART_BUILDMETADATA;

    /**
     * @var int
     */
    public const VERSIONS_ALL = self::VERSIONS_UP_TO_BUILDMETADATA;

    public function setVersions(int $major, int $minor, int $patch, string $prerelease, string $buildmetadata): void;
}
