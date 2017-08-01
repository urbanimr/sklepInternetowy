<?php

use Phinx\Migration\AbstractMigration;

class InsertDefaultValsToStatuses extends AbstractMigration
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
              'status_name'  => 'Basket'
            ],
            [
              'id'    => 2,
              'status_name'  => 'Submitted'
            ],
            [
              'id'    => 3,
              'status_name'  => 'Paid'
            ],
            [
              'id'    => 4,
              'status_name'  => 'Shipped'
            ],
            [
              'id'    => 5,
              'status_name'  => 'Delivered'
            ],
            [
              'id'    => 6,
              'status_name'  => 'Canceled'
            ]
        ];

        $this->insert('statuses', $rows);
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $this->execute('DELETE FROM statuses');
    }
}
