<?php

use Phinx\Migration\AbstractMigration;

class CreateOrdersTable extends AbstractMigration
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
        $this->table('orders')
            ->addColumn('user_id', 'integer', [
                'null' => true
            ])
            ->addColumn('billing_address', 'integer', [
                'null' => true
            ])
            ->addColumn('shipping_address', 'integer', [
                'null' => true
            ])
            ->addColumn('carrier_id', 'integer', [
                'null' => false
            ])
            ->addColumn('payment_id', 'integer', [
                'null' => false
            ])
            ->addColumn('comment', 'string', [
                'null' => true,
                'length' => 255,
                'collation' => 'utf8_polish_ci'
            ])
            ->addColumn('shipping_cost', 'decimal', [
                'null' => false,
                'precision' => 10,
                'scale' => 2,
            ])
            ->addColumn('total_amount', 'decimal', [
                'null' => false,
                'precision' => 10,
                'scale' => 2,
            ])
            ->addForeignKey('user_id', 'users', 'id', [
                'delete' => 'SET NULL'
            ])
            ->addForeignKey('billing_address', 'addresses', 'id', [
                'delete' => 'SET NULL'
            ])
            ->addForeignKey('shipping_address', 'addresses', 'id', [
                'delete' => 'SET NULL'
            ])
            ->addForeignKey('carrier_id', 'carriers', 'id', [
                'delete' => 'RESTRICT'
            ])
            ->addForeignKey('payment_id', 'payment_methods', 'id', [
                'delete' => 'RESTRICT'
            ]) 
            ->create();
    }
}
