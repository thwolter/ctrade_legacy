<?php

// Search resources
Route::get('portfolios/{portfolio}/search/index', [
    'as' => 'search.index',
    'uses' => 'SearchController@index'
]);


Route::get('portfolios/{portfolio}/search', [
    'as' => 'search.show',
    'uses' => 'SearchController@show'
]);
