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

interface FileReplaceItemInterface
{
    /**
     * The filename of the file to process.
     */
    public function getName(): string;

    /**
     * The pattern to search for.
     * @see \preg_replace
     */
    public function getPattern(): string;

    /**
     * The string to replace.
     * @see \preg_replace
     */
    public function getReplacement(): string;
}
