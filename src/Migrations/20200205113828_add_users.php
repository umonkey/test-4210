<?php

use Phinx\Migration\AbstractMigration;

class AddUsers extends AbstractMigration
{
    public function change()
    {
        $this->table('users', ['signed' => false])
            ->addColumn('name', 'string')
            ->addColumn('password', 'string')
            ->addIndex(['name'])
            ->save();
    }
}
