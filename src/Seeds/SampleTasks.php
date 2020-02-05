<?php


use Phinx\Seed\AbstractSeed;

class SampleTasks extends AbstractSeed
{
    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeders is available here:
     * http://docs.phinx.org/en/latest/seeding.html
     */
    public function run()
    {
        $tasks = [];
        $users = ['alice', 'bob', 'charlie'];

        for ($n = 1; $n <= 100; $n++) {
            $tasks[] = [
                'id' => $n,
                'user' => $users[$n % 3],
                'email' => $users[$n % 3] . '@mail.ru',
                'text' => 'Help me please',
                'completed' => $n % 5 == 0,
            ];
        }

        $table = $this->table('tasks');
        $table->truncate();
        $table->insert($tasks);
        $table->saveData();
    }
}
