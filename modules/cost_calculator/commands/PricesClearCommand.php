<?php

declare(strict_types=1);

namespace app\modules\cost_calculator\commands;

use Monoelf\Framework\common\AliasManager;
use Monoelf\Framework\console\command\ConsoleCommandInterface;
use Monoelf\Framework\console\ConsoleInputInterface;
use Monoelf\Framework\console\ConsoleOutputInterface;

final class PricesClearCommand implements ConsoleCommandInterface
{
    public function __construct(
        private readonly AliasManager $aliasManager,
    ) {}

    public static function getSignature(): string
    {
        return 'prices:clear';
    }

    public static function getDescription(): string
    {
        return 'Очистка данных по прайсам';
    }

    public function execute(ConsoleInputInterface $input, ConsoleOutputInterface $output): void
    {
        $priceDir = $this->aliasManager->buildPath('@runtime');

        $content = scandir($priceDir);

        $deletedCount = 0;

        foreach ($content as $file) {
            if ((bool)preg_match('/\.json$/', $file) === false) {
                continue;
            }

            $deletedCount++;

            $filePath = $priceDir . DIRECTORY_SEPARATOR . $file;
            unlink($filePath);

            $output->warning("Удален файл {$filePath}");
            $output->writeLn();
        }

        if ($deletedCount === 0) {
            $output->warning('Отсутствуют файлы для удаления');
            $output->writeLn();

            return;
        }

        $output->success("Удалено файлов: {$deletedCount}");
        $output->writeLn();
    }
}
