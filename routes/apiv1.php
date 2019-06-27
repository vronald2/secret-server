<?php

Route::post('/secret', 'ApiV1Controller@addSecret')->name('addSecret');
Route::get('/secret/{hash}', 'ApiV1Controller@getSecretByHash')->name('getSecretByHash');