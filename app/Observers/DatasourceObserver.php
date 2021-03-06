<?php


namespace App\Observers;


use App\Entities\Datasource;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;


class DatasourceObserver
{

    public function created(Datasource $datasource)
    {
        Log::notice(sprintf('Create datasource %s.', $datasource->key()));
        Cache::forget($datasource->key());
    }

    public function updating(Datasource $datasource)
    {
        if ($datasource->getDirty('valid') and !$datasource->valid)
            Log::warning(sprintf('%s invalidated', $datasource->key()));

        if ($datasource->getDirty('refreshed_at'))
            Log::info(sprintf('%s - refreshed at: %s', $datasource->key(), $datasource->refreshed_at));

        Log::debug("Delete cache for {$datasource->key()}");
        Cache::forget($datasource->key());
    }
}