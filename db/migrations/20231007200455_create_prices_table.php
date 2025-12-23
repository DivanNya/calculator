<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreatePricesTable extends AbstractMigration
{
    public function change(): void
    {
        $this->execute('
            CREATE TABLE prices (
                id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                tonnage_id INT(11) UNSIGNED,
                month_id INT(11) UNSIGNED,
                raw_type_id INT(11) UNSIGNED,
                price INT UNSIGNED,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP(),
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP() ON UPDATE CURRENT_TIMESTAMP(),
                FOREIGN KEY (tonnage_id) REFERENCES tonnages(id) ON UPDATE NO ACTION ON DELETE CASCADE,
                FOREIGN KEY (month_id) REFERENCES months(id) ON UPDATE NO ACTION ON DELETE CASCADE,
                FOREIGN KEY (raw_type_id) REFERENCES raw_types(id) ON UPDATE NO ACTION ON DELETE CASCADE,
                UNIQUE KEY unique_price_combination (tonnage_id, month_id, raw_type_id)
            )
        ');
    }
}
