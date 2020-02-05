<?php

use Phinx\Migration\AbstractMigration;

class AddTaskEditedField extends AbstractMigration
{
    public function change()
    {
        $this->table('tasks')
             ->addColumn('edited', 'boolean')
             ->save();
    }
}
