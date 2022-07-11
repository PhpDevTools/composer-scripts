<?php

$header = <<<EOM
This file is part of the gilbertsoft/composer-scripts package.

(c) Gilbertsoft LLC (gilbertsoft.org)

For the full copyright and license information, please view the LICENSE
file that was distributed with this source code.
EOM;

$config = \TYPO3\CodingStandards\CsFixerConfig::create();
$config
    ->setHeader($header, true)
    ->addRules([
        '@PSR12' => true,
        'fully_qualified_strict_types' => true,
        'global_namespace_import' => [
            'import_classes' => true,
            'import_constants' => true,
            'import_functions' => true
        ],
        'no_unneeded_import_alias' => true,
    ])
    ->getFinder()
    ->in(__DIR__ . '/src')
    ->in(__DIR__ . '/tests')
;

return $config;
