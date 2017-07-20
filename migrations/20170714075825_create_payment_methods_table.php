<?php

use Phinx\Migration\AbstractMigration;

class CreatePaymentMethodsTable extends AbstractMigration
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
        $this->table('payment_methods')
            ->addColumn('payment_name', 'string', [
                'null' => false,
                'length' => 80,
                'collation' => 'utf8_polish_ci'
            ])
            ->addColumn('description', 'string', [
                'null' => true,
                'length' => 255,
                'collation' => 'utf8_polish_ci'
            ])
            ->addColumn('active', 'boolean', [
                'null' => false
            ])
            ->create();
    }
}
