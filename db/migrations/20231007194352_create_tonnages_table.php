<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateTonnagesTable extends AbstractMigration
{
    public function change(): void
    {
        $this->execute('
            CREATE TABLE tonnages (
                id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                value TINYINT UNSIGNED UNIQUE,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP(),
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP() ON UPDATE CURRENT_TIMESTAMP()
            )
        ');
    }
}
