<?php

declare(strict_types=1);

return array_merge(
    require_once PROJECT_ROOT . '/config/common.php',
    [
        'settings' => require_once PROJECT_ROOT . '/config/settings.php',
    ],
    [
        'prices' => require_once PROJECT_ROOT . '/config/prices.php',
    ],
);
