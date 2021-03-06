<?php

Route::middleware('auth')->group(function() {

    Route::get('/notifications/index', [
        'as' => 'notifications.index',
        'uses' => 'NotificationController@index'
    ]);

    Route::delete('/notifications/delete', [
        'as' => 'notifications.delete',
        'uses' => 'NotificationController@delete'
    ]);
});