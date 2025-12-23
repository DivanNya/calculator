<?php

declare(strict_types=1);

namespace app\modules\cost_calculator;

use Monoelf\Framework\common\AliasManager;
use Monoelf\Framework\common\ModuleInterface;

final class Module implements ModuleInterface
{
    public function __construct(
        private readonly AliasManager $aliasManager
    ) {}

    public function init(): void
    {
        $this->aliasManager->addAlias('@views-web', '@modules/cost_calculator/views/http');
    }
}
