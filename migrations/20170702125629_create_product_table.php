<?php

use Phinx\Migration\AbstractMigration;
class CreateProductTable extends AbstractMigration
{
    private $tableName = 'products';

    public function up() {
        $this->execute('
                CREATE TABLE ' .$this->tableName. ' (
                    id int AUTO_INCREMENT,
                    name varchar(80) NOT NULL,
                    price decimal NOT NULL,
                    description text,
                    quantity int NOT NULL,
                    PRIMARY KEY (id)
                )
            ');
    }
    public function down() {
        $this->execute('DROP TABLE ' . $this->tableName);
    }
}
