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

use BadMethodCallException;

final class FileReplaceVersionItem implements FileReplaceVersionItemInterface
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $pattern;

    /**
     * @var int
     */
    private $versionFormat;

    /**
     * @var int|null
     */
    private $major;

    /**
     * @var int|null
     */
    private $minor;

    /**
     * @var int|null
     */
    private $patch;

    /**
     * @var string|null
     */
    private $prerelease;

    /**
     * @var string|null
     */
    private $buildmetadata;

    public function __construct(string $filename, string $pattern, int $versionFormat)
    {
        $this->name = $filename;
        $this->pattern = $pattern;
        $this->versionFormat = $versionFormat;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPattern(): string
    {
        return $this->pattern;
    }

    public function getReplacement(): string
    {
        if (
            $this->major === null
            || $this->minor === null
            || $this->patch === null
            || $this->prerelease === null
            || $this->buildmetadata === null
        ) {
            throw new BadMethodCallException('Call to previous setVersions() is missing.', 1654889886);
        }

        $concatVersion = static function (string $version, string $part, string $separator): string {
            if ($version === '') {
                return $part;
            }

            return $version . $separator . $part;
        };

        $version = '';

        if (($this->versionFormat & self::VERSION_PART_MAJOR) === self::VERSION_PART_MAJOR) {
            $version = $concatVersion($version, (string)$this->major, '');
        }

        if (($this->versionFormat & self::VERSION_PART_MINOR) === self::VERSION_PART_MINOR) {
            $version = $concatVersion($version, (string)$this->minor, '.');
        }

        if (($this->versionFormat & self::VERSION_PART_PATCH) === self::VERSION_PART_PATCH) {
            $version = $concatVersion($version, (string)$this->patch, '.');
        }

        if (
            ($this->versionFormat & self::VERSION_PART_PRERELEASE) === self::VERSION_PART_PRERELEASE
            && $this->prerelease !== ''
        ) {
            $version = $concatVersion($version, $this->prerelease, '-');
        }

        if (
            ($this->versionFormat & self::VERSION_PART_BUILDMETADATA) === self::VERSION_PART_BUILDMETADATA
            && $this->buildmetadata !== ''
        ) {
            return $concatVersion($version, $this->buildmetadata, '+');
        }

        return $version;
    }

    public function setVersions(int $major, int $minor, int $patch, string $prerelease, string $buildmetadata): void
    {
        $this->major = $major;
        $this->minor = $minor;
        $this->patch = $patch;
        $this->prerelease = $prerelease;
        $this->buildmetadata = $buildmetadata;
    }
}
