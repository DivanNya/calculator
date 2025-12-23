<?php

declare(strict_types=1);

namespace app\modules\cost_calculator\commands;

use Monoelf\Framework\common\AliasManager;
use Monoelf\Framework\config_storage\ConfigurationStorage;
use Monoelf\Framework\console\command\ConsoleCommandInterface;
use Monoelf\Framework\console\ConsoleInputInterface;
use Monoelf\Framework\console\ConsoleOutputInterface;

final class PricesInitCommand implements ConsoleCommandInterface
{
    private array $setUpKeys;
    private ConsoleOutputInterface $output;

    public function __construct(
        private readonly ConfigurationStorage $configurationStorage,
        private readonly AliasManager $aliasManager,
    ) {
        $this->setUpKeys = ['months', 'tonnages', 'raw_types'];
    }

    public static function getSignature(): string
    {
        return 'prices:init';
    }

    public static function getDescription(): string
    {
        return 'Инициализация данных по прайсам в формате json';
    }

    public function execute(ConsoleInputInterface $input, ConsoleOutputInterface $output): void
    {
        $this->output = $output;

        $this->validateConfiguration();

        $this->initSetUpFiles($this->configurationStorage->get('settings'));
        $this->initPricesFile($this->configurationStorage->get('prices'));
    }

    /**
     * @return void
     */
    private function validateConfiguration(): void
    {
        if ($this->configurationStorage->has('settings') === false) {
            throw new \LogicException('Не задан файл настроек');
        }

        if ($this->configurationStorage->has('prices') === false) {
            throw new \LogicException('Не задан файл цен');
        }

        $settings = $this->configurationStorage->get('settings');

        $settingsKeys = array_keys($settings);

        if (count(array_intersect($settingsKeys, $this->setUpKeys)) !== 3) {
            throw new \LogicException('Некорректный файл конфигурации');
        }
    }

    /**
     * @param array $settings
     * @return void
     */
    private function initSetUpFiles(array $settings): void
    {
        foreach ($this->setUpKeys as $key) {

            $setUpFile = $this->aliasManager->buildPath('@runtime/') . $key . '.json';

            if (file_exists($setUpFile) === true) {
                $this->output->warning("Файл $setUpFile уже существует");
                $this->output->writeLn();

                continue;
            }

            $this->output->success("Создан файл $setUpFile");
            $this->output->writeLn();

            $content = json_encode($settings[$key], JSON_UNESCAPED_UNICODE);
            file_put_contents($setUpFile, $content);
        }
    }

    /**
     * @param array $prices
     * @return void
     */
    private function initPricesFile(array $prices): void
    {
        $pricesOutput = $this->aliasManager->buildPath('@runtime/') . 'prices.json';

        if (file_exists($pricesOutput) === true) {
            $this->output->warning("Файл $pricesOutput уже существует");
            $this->output->writeLn();

            return;
        }

        $content = json_encode($prices, JSON_UNESCAPED_UNICODE);

        file_put_contents($pricesOutput, $content);

        $this->output->success("Создан файл $pricesOutput");
        $this->output->writeLn();
    }
}
