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
     * @var int
     */
    public const VERSION_PATCH = 0;

    /**
     * @var int
     */
    public const VERSION_MINOR = 1;

    /**
     * @var int
     */
    public const VERSION_MAJOR = 2;

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
        if ($this->major === null || $this->minor === null || $this->patch === null) {
            throw new BadMethodCallException('Call to previous setVersions() is missing.', 1654889886);
        }

        $concatVersion = function (string $version, int $part): string {
            if ($version === '') {
                return (string)$part;
            }

            return $part . '.' . $version;
        };

        $version = '';

        switch ($this->versionFormat) {
            case self::VERSION_PATCH:
                $version = $concatVersion($version, $this->patch);
                // continue with next part
                // no break
            case self::VERSION_MINOR:
                $version = $concatVersion($version, $this->minor);
                // continue with next part
                // no break
            case self::VERSION_MAJOR:
                $version = $concatVersion($version, $this->major);
        }

        return $version;
    }

    public function setVersions(int $major, int $minor, int $patch): void
    {
        $this->major = $major;
        $this->minor = $minor;
        $this->patch = $patch;
    }
}
