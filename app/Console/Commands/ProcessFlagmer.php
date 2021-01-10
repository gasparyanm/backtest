<?php

namespace App\Console\Commands;

use App\Services\Flagmer\Integrations\Amocrm\sendLeadDto;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Services\Flagmer\Billing\Account;
use App\Services\Flagmer\Integrations\AmoCrm;

class ProcessFlagmer extends Command
{
    const TASK_CATEGORY_ACCOUNT = 'account';
    const TASK_CATEGORY_AMOCRM = 'amocrm';

    const SERVICE_ACCOUNT_ACTION = 'processPaymentAction';
    const SERVICE_AMOCRM_ACTION = 'sendLeadAction';

    private $service = null;
    private $serviceMethod = null;
    private $serviceAction = null;
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

        if (empty($task))
            exit('free task not found');

        $data = json_decode($task->data, true);

        if( $task->category === self::TASK_CATEGORY_ACCOUNT ){
            $this->serviceMethod = new Account();
            $this->serviceAction = self::SERVICE_ACCOUNT_ACTION;
            $this->service = new Account\processPaymentDto();
            $this->service->account_id = $data['account_id'];
            $this->service->amount = $data['amount'];

        }
        if( $task->category === self::TASK_CATEGORY_AMOCRM ){
            $this->serviceMethod = new AmoCrm();
            $this->serviceAction = self::SERVICE_AMOCRM_ACTION;
            $this->service = new Amocrm\sendLeadDto();
            $this->service->lead_id = $data['lead_id'];
        }

        if( empty($this->serviceMethod) )
            exit('service not found');

        $this->pushTaskToQueue($task);
        $this->serviceMethod->{$this->serviceAction}($this->service);
        $this->updateTaskStatus($task);
        $this->removeTaskFromQueue($task);
        dump(DB::table('tasks')->where('id', $task->id)->first());
    }

    /**
     * @return \Illuminate\Database\Query\Builder|object|null
     */
    private function getFreeTask()
    {
        return DB::table('tasks')
            ->where('onQueue', false)
            ->where('status', '0')
            ->first();
    }

    /**
     * @param $task
     * @return void
     */
    private function pushTaskToQueue($task)
    {
        DB::table('tasks')
            ->where('id', $task->id)
            ->update(['onQueue' => '1']);
    }

    /**
     * @param $task
     * @return void
     */
    private function updateTaskStatus($task)
    {
        DB::table('tasks')
            ->where('id', $task->id)
            ->update(['status' => '1']);
    }

    /**
     * @param $task
     * @return void
     */
    private function removeTaskFromQueue($task)
    {
        DB::table('tasks')
            ->where('id', $task->id)
            ->update(['onQueue' => '0']);
    }
}
