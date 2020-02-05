<?php

use Phinx\Seed\AbstractSeed;

class AddAdminUser extends AbstractSeed
{
    public function run()
    {
        $table = $this->table('users');
        $table->truncate();

        $table->insert([[
            'id' => 1,
            'name' => 'admin',
            'password' => password_hash('123', PASSWORD_DEFAULT),
        ]]);

        $table->saveData();
    }
}
