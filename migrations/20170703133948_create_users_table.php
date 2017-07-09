<?php

use Phinx\Migration\AbstractMigration;

class CreateUsersTable extends AbstractMigration
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
        $this->table('users')
            ->addColumn('name', 'string', [
                'null' => false,
                'length' => 80,
                'collation' => 'utf8_polish_ci'
            ])
            ->addColumn('email', 'string', [
                'null' => false,
                'length' => 255,
                'collation' => 'utf8_polish_ci'
                ])
            ->addColumn('password', 'string', [
                'null' => false,
                'length' => 60,
                'collation' => 'utf8_polish_ci'
                ])
            ->addColumn('date_created', 'datetime', ['null' => false])
            ->create();
    }
}
