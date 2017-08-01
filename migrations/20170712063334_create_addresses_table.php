<?php

use Phinx\Migration\AbstractMigration;

class CreateAddressesTable extends AbstractMigration
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
    public function change()
    {
        //phinx automatically creates column id with auto_increment and primary key
        $this->table('addresses')
            ->addColumn('alias', 'string', [
                'null' => false,
                'length' => 80,
                'collation' => 'utf8_polish_ci'
            ])
            ->addColumn('company', 'string', [
                'null' => true,
                'length' => 255,
                'collation' => 'utf8_polish_ci'
            ])
            ->addColumn('name', 'string', [
                'null' => false,
                'length' => 255,
                'collation' => 'utf8_polish_ci'
            ])
            ->addColumn('address1', 'string', [
                'null' => false,
                'length' => 255,
                'collation' => 'utf8_polish_ci'
            ])
            ->addColumn('address2', 'string', [
                'null' => true,
                'length' => 255,
                'collation' => 'utf8_polish_ci'
            ])
            ->addColumn('postcode', 'string', [
                'null' => false,
                'length' => 20,
                'collation' => 'utf8_polish_ci'
            ])
            ->addColumn('city', 'string', [
                'null' => false,
                'length' => 255,
                'collation' => 'utf8_polish_ci'
            ])
            ->addColumn('country', 'string', [
                'null' => false,
                'length' => 255,
                'collation' => 'utf8_polish_ci'
            ])
            ->addColumn('phone', 'string', [
                'null' => false,
                'length' => 20,
                'collation' => 'utf8_polish_ci'
            ])
            ->addColumn('tax_no', 'string', [
                'null' => true,
                'length' => 20,
                'collation' => 'utf8_polish_ci'
            ])
            ->create();
    }
}
