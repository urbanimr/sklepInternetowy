<?php

use Phinx\Migration\AbstractMigration;

class CreateOrderStatusesTable extends AbstractMigration
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
        $this->table('order_statuses')
            ->addColumn('order_id', 'integer', [
                'null' => false
            ])
            ->addColumn('status_id', 'integer', [
                'null' => false
            ])
            ->addColumn('date', 'datetime', [
                'null' => false
            ])
            ->addForeignKey('order_id', 'orders', 'id', ['delete' => 'CASCADE'])
            ->addForeignKey('status_id', 'statuses', 'id', ['delete' => 'RESTRICT'])
            ->create();
    }
}
