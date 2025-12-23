<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateMonthsTable extends AbstractMigration
{
    public function change(): void
    {
        $this->execute('
            CREATE TABLE months (
                id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(10) UNIQUE,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP(),
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP() ON UPDATE CURRENT_TIMESTAMP()
            )
        ');
    }
}
