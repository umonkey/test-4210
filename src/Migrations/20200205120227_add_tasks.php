<?php

use Phinx\Migration\AbstractMigration;

class AddTasks extends AbstractMigration
{
    public function change()
    {
        $this->table('tasks', ['signed' => false])
             ->addColumn('user', 'string')
             ->addColumn('email', 'string')
             ->addColumn('text', 'text')
             ->addColumn('completed', 'boolean')
             ->addIndex(['user'])
             ->addIndex(['email'])
             ->addIndex(['completed'])
             ->save();
    }
}
