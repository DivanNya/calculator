<?php

declare(strict_types=1);

use Phinx\Seed\AbstractSeed;

class PricesSeeder extends AbstractSeed
{
    public function getDependencies(): array
    {
        return [
            'ListsSeeder',
        ];
    }

    public function run(): void
    {
        $prices = require_once __DIR__ . '/../../config/prices.php';

        foreach ($prices as $type => $price) {
            foreach ($price as $month => $tonnages) {
                foreach ($tonnages as $tonnage => $price) {
                    $sql = sprintf('
                        INSERT INTO prices (tonnage_id, month_id, raw_type_id, price)
                        VALUES (
                                (
                                    SELECT id FROM tonnages WHERE value = %s
                                ),
                                (
                                    SELECT id FROM months WHERE name = "%s"
                                ),
                                (
                                    SELECT id FROM raw_types WHERE name = "%s"
                                ),
                                "%s"
                        )
                    ', $tonnage, $month, $type, $price);

                    $this->execute($sql);
                }
            }
        }
    }
}
