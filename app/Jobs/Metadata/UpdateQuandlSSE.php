<?php

namespace App\Jobs\Metadata;

use App\Repositories\Metadata\Quandl\QuandlSSE;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;



class UpdateQuandlSSE implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    protected $repo;


    public function __construct()
    {
        $this->repo = new QuandlSSE();
    }


    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->repo->updateDatabase();
    }



}
