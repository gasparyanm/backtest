<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class TasksSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $tasks = file_get_contents(resource_path('flagmer/tasks.json'));
        $tasks = json_decode($tasks, true);

        foreach ($tasks as $task) {
            DB::table('tasks')->insert([
                'category' => $task['category'],
                'task' => $task['task'],
                'data' => json_encode($task['data']),
                'onQueue' => false,
                'status' => 0
            ]);
        }
    }
}
