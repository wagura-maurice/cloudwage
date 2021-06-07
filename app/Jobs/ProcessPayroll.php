<?php

namespace App\Jobs;

use App\Http\Requests\Request;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Payroll\Handlers\PayrollProcessor;


class ProcessPayroll implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var string
     */
    protected $db_connection;

    /**
     * @var Request
     */
    private $request;


    /**
     * Create a new job instance.
     *
     * @param $request
     * @param $connection
     */
    public function __construct($request, $connection)
    {
        $this->request = $request;
        $this->db_connection = $connection;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $processor = new PayrollProcessor($this->request, $this->db_connection);
        $processor->handle();
    }
}
