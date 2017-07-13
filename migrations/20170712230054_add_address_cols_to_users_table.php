<?php

use Phinx\Migration\AbstractMigration;

class AddAddressColsToUsersTable extends AbstractMigration
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
        $this->table('users')
            ->addColumn('billing_address', 'integer', ['null' => false])
            ->addForeignKey('billing_address', 'addresses', 'id', ['delete' => 'RESTRICT'])
            ->addColumn('shipping_address', 'integer', ['null' => true])
            ->addForeignKey('shipping_address', 'addresses', 'id', ['delete' => 'SET NULL'])
            ->update();
    }
}
