<?php

declare(strict_types=1);

use app\modules\cost_calculator\Module;

return [
    'aliases' => [
        '@app' => PROJECT_ROOT,
        '@framework' => PROJECT_ROOT . '/vendor/monoelf/framework',
        '@modules' => PROJECT_ROOT . '/modules',
        '@runtime' => PROJECT_ROOT . '/runtime',
    ],
    'modules' => [
        Module::class,
    ]
];
