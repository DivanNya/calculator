<?php

declare(strict_types=1);

use Phinx\Seed\AbstractSeed;

class ListsSeeder extends AbstractSeed
{
    public function run(): void
    {
        $settings = require_once __DIR__ . '/../../config/settings.php';

        $setUpKeys = ['months', 'tonnages', 'raw_types'];

        $settingsKeys = array_keys($settings);

        if (count(array_intersect($settingsKeys, $setUpKeys)) !== 3) {
            throw new \LogicException('Некорректный файл конфигурации');
        }

        foreach ($setUpKeys as $key) {
            $query = 'INSERT INTO %s (name) VALUES ("%s")';

            if ($key === 'tonnages') {
                $query = 'INSERT INTO %s (value) VALUES (%s)';
            }

            foreach ($settings[$key] as $value) {
                $sql = sprintf($query, $key, $value);

                $this->execute($sql);
            }
        }
    }
}
