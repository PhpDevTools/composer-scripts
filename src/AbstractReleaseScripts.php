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
use Composer\Semver\VersionParser;
use Gilbertsoft\Composer\Release\FileReplaceVersionItemInterface;
use InvalidArgumentException;
use Iterator;
use RuntimeException;
use UnexpectedValueException;

abstract class AbstractReleaseScripts extends AbstractScripts
{
    /**
     * @throws InvalidArgumentException
     * @throws UnexpectedValueException
     * @see https://semver.org/
     */
    protected static function extractVersionCore(string $semver, int &$major, int &$minor, int &$patch): void
    {
        if ($semver === '') {
            throw new InvalidArgumentException(
                'The parameter semver must be not be empty.',
                1654777706
            );
        }

        $normalizedVersion = (new VersionParser())->normalize($semver);

        if (preg_match('#^(\d+)\.(\d+)\.(\d+)#', $normalizedVersion, $matches) === false || count($matches) !== 4) {
            throw new UnexpectedValueException(sprintf('"%s" is no valid version number.', $semver), 1654777707);
        }

        $major = (int)$matches[1];
        $minor = (int)$matches[2];
        $patch = (int)$matches[3];

        /*
        $version = sprintf('%d.%d.%d', $matches[1], $matches[2], $matches[3]);
        $branchVersion = sprintf('%d.%d', $matches[1], $matches[2]);
        */
    }

    /**
     * @param string $filename File name relative to the root composer.json.
     * @throws RuntimeException
     * @throws UnexpectedValueException
     */
    protected static function fileReplaceVersion(string $filename, string $pattern, string $version): void
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

        self::getFilesystem()->fileReplaceContents($filename, $pattern, '${1}' . $version . '${2}');
    }

    /**
     * @return Iterator<FileReplaceVersionItemInterface>
     */
    abstract protected static function getFiles(): Iterator;

    /**
     * @throws RuntimeException
     * @throws UnexpectedValueException
     */
    public static function setVersion(Event $event): void
    {
        $major = 0;
        $minor = 0;
        $patch = 0;

        try {
            self::extractVersionCore($event->getArguments()[0] ?? '', $major, $minor, $patch);
        } catch (InvalidArgumentException $invalidArgumentException) {
            throw new UnexpectedValueException(
                'A valid version number must be provided as an argument, e.g. `composer set-version 1.2.3`.',
                1654777706,
                $invalidArgumentException
            );
        }

        foreach (static::getFiles() as $file) {
            $file->setVersions($major, $minor, $patch);
            self::fileReplaceVersion(
                $file->getName(),
                $file->getPattern(),
                $file->getReplacement()
            );
        }
    }
}
