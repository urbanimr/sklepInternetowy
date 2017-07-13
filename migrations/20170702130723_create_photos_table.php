<?php

use Phinx\Migration\AbstractMigration;

class CreatePhotosTable extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    private $tableName = 'photos';

    public function up()
    {
        $this->execute('CREATE TABLE' . $this->tableName .'(
                              id INT AUTO_INCREMENT, 
                              product_id int NOT NULL, 
                              picture_name VARCHAR(80) NOT NULL ,
                              path VARCHAR(80) NOT NULL , 
                              picture_description VARCHAR(80), 
                              PRIMARY KEY (id), 
                              FOREIGN KEY (product_id) REFERENCES products(id))');
    }
    
    public function down() {
        $this->execute('DROP TABLE' . $this->tableName);
    }

    public function change()
    {

    }
}
