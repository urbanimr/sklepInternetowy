<?php

use Phinx\Migration\AbstractMigration;

class InsertDefaultValsToPaymentMethods extends AbstractMigration
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
    public function up()
    {
        // inserting multiple rows
        $rows = [
            [
                'id'    => 1,
                'payment_name'  => 'Cash',
                'description' => 'Pay with cash upon receival',
                'active' => 1
            ],
            [
                'id'    => 2,
                'payment_name'  => 'Bank transfer',
                'description' => 'Pay with bank transfer',
                'active' => 1
            ],
            [
                'id'    => 3,
                'payment_name'  => 'Cheque',
                'description' => 'Pay with cheque',
                'active' => 0                
            ]
        ];

        $this->insert('payment_methods', $rows);
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $this->execute('DELETE FROM payment_methods');
    }
}
