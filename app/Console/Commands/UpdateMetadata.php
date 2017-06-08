<?php

namespace App\Console\Commands;

use App\Repositories\Metadata\QuandlSSE;
use App\Jobs\UpdateQuandlMetadata;
use Illuminate\Console\Command;


class UpdateMetadata extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'metadata:update
                            {--provider= : Name of the provider (Quandl)}
                            {--max= : Maximum pages to be loaded}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Updates the metadata from data provider';

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
        $provider = $this->option('provider');
        $max = $this->option('max');

        if (!in_array($provider, ['Quandl', null])) {
            
            return $this->comment("Provider {$provider} not defined.");
        }


        if ($provider == 'Quandl' or $provider == null)
        {
            $meta = new QuandlSSE();
            $i = 0;
            
            do {
                $i++;
                $items = $meta->getItems(20);
                dispatch(new UpdateQuandlMetadata($meta, $items));
            } while( $items != [] and $i < $max);
        }

        return $this->info("Done. \n");
    }
}