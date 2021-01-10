<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use \Flagmer\Billing\Account as Account;
use \Flagmer\Integrations\AmoCrm as AmoCrm;

class processFlagmer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'process:flagmer';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send tasks to services in background';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $task = $this->getFreeTask();

        if (!$task) {
            return false;
        }

        $action = $task->task.'Action';
        $data = json_decode($task->data, true);

        $namespace = '\\Flagmer\\' . ($task->category === 'account' ? 'Billing\\Account' : 'Integrations\\AmoCrm');
        $paramsObjPath = $namespace. '\\' . $task->task.'Dto';
        $paramsObj = new $paramsObjPath;

        foreach ($data as $item => $key) {
            $paramsObj->{$item} = $key;
        }

        $this->pushTaskToQueue($task);
        (new $namespace())->{$action}($paramsObj);
        $this->updateTaskStatus($task);
        $this->removeTaskFromQueue($task);
        dump(DB::table('tasks')->where('id', $task->id)->first());
    }

    private function getFreeTask()
    {
        return DB::table('tasks')
            ->where('onQueue', false)
            ->where('status', '0')
            ->first();
    }

    private function pushTaskToQueue($task)
    {
        DB::table('tasks')
            ->where('id', $task->id)
            ->update(['onQueue' => '1']);
    }

    private function updateTaskStatus($task)
    {
        DB::table('tasks')
            ->where('id', $task->id)
            ->update(['status' => '1']);
    }

    private function removeTaskFromQueue($task)
    {
        DB::table('tasks')
            ->where('id', $task->id)
            ->update(['onQueue' => '0']);
    }
}
