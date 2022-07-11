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
    public function setVersions(int $major, int $minor, int $patch): void;
}
