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

namespace Gilbertsoft\Composer\Util;

use Composer\Pcre\Preg;
use Composer\Util\Filesystem as ComposerFilesystem;
use RuntimeException;
use Throwable;
use UnexpectedValueException;

final class Filesystem
{
    /**
     * @var ComposerFilesystem
     */
    private $composerFilesystem;

    public function __construct(ComposerFilesystem $composerFilesystem = null)
    {
        $this->composerFilesystem = $composerFilesystem ?? new ComposerFilesystem();
    }

    public function isAbsolutePath(string $path): bool
    {
        return $this->composerFilesystem->isAbsolutePath($path);
    }

    /**
     * @throws RuntimeException
     */
    public function fileGetContents(string $filename): string
    {
        try {
            if (($content = file_get_contents($filename)) === false) {
                // @codeCoverageIgnoreStart
                throw new RuntimeException();
                // @codeCoverageIgnoreEnd
            }

            return $content;
        } catch (Throwable $throwable) {
            throw new RuntimeException(
                sprintf('Failed to read file "%s".', $filename),
                1654777708,
                $throwable
            );
        }
    }

    /**
     * @throws RuntimeException
     */
    public function filePutContents(string $filename, string $content): void
    {
        try {
            if (file_put_contents($filename, $content) === false) {
                // @codeCoverageIgnoreStart
                throw new RuntimeException();
                // @codeCoverageIgnoreEnd
            }
        } catch (Throwable $throwable) {
            throw new RuntimeException(
                sprintf('Failed to write file "%s".', $filename),
                1654777709,
                $throwable
            );
        }
    }

    /**
     * @param string $filename File name relative to the root composer.json.
     * @throws RuntimeException
     * @throws UnexpectedValueException
     */
    public function fileReplaceContents(string $filename, string $pattern, string $replacement): void
    {
        $currentContent = $this->fileGetContents($filename);

        try {
            $content = Preg::replace($pattern, $replacement, $currentContent);
        } catch (Throwable $throwable) {
            throw new UnexpectedValueException(
                sprintf(
                    'Failed to replace content of "%s" using pattern "%s" and replacement "%s".',
                    $filename,
                    $pattern,
                    $replacement
                ),
                1654777711,
                $throwable
            );
        }

        if ($currentContent === $content) {
            return;
        }

        $this->filePutContents($filename, $content);
    }
}
