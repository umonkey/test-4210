<?php

use Phinx\Migration\AbstractMigration;

class AddSessions extends AbstractMigration
{
    public function change()
    {
        $this->table('sessions', ['id' => false])
             ->addColumn('id', 'string', ['null' => false, 'limit' => 32])
             ->addColumn('updated', 'datetime', ['null' => false])
             ->addColumn('data', 'blob')
             ->addIndex(['id'], ['unique' => true])
             ->addIndex(['updated'])
             ->save();
    }
}
