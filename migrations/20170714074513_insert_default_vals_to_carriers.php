<?php

use Phinx\Migration\AbstractMigration;

class InsertDefaultValsToCarriers extends AbstractMigration
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
                'carrier_name'  => 'In-store pickup',
                'description' => 'Visit our store and pick up your order free of charge',
                'price' => 0.00,
                'active' => 1
            ],
            [
                'id'    => 2,
                'carrier_name'  => 'DHL',
                'description' => 'Delivery within 48h',
                'price' => 14.00,
                'active' => 1
            ],
            [
                'id'    => 3,
                'carrier_name'  => 'Pocztex',
                'description' => 'Delivery within 48h',
                'price' => 18.50,
                'active' => 1
            ],
            [
                'id'    => 4,
                'carrier_name'  => 'Siódemka',
                'description' => 'Delivery within 48h',
                'price' => 15.50,
                'active' => 0
            ]
        ];

        $this->insert('carriers', $rows);
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $this->execute('DELETE FROM carriers');
    }
}
