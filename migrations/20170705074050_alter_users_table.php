<?php

use Phinx\Migration\AbstractMigration;

class AlterUsersTable extends AbstractMigration
{
    public function change() {
        //add 'UNIQUE' index to email
        $this->table('users')
            ->addIndex(['email'], ['unique' => true])
            ->update();
    }
}
