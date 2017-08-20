<?php

use Illuminate\Support\Facades\Artisan;


Route::get('test', function() {
    return view('auth.confirmed.email', ['user' => \App\Entities\User::first()]);
});


Route::get('event', function(Request $request) {
    $limit = \App\Entities\Limit::firstOrFail();
    event(new \App\Events\Limits\LimitHasChanged($limit));
});

Route::get('metadata/update', function() { Artisan::call('metadata:update'); });

Route::get('temp/delete', function() { Artisan::call('temp:delete'); });

Route::get('calculate/risk', function() { Artisan::call('calculate:risk'); });
Route::get('calculate/value', function() { Artisan::call('calculate:value'); });

Route::get('quandl/{database}/{symbol}', function($database, $symbol) {
    Artisan::call('metadata:check', [
        'provider' => 'Quandl', 'database' => $database, 'symbol' => $symbol
    ]);
});