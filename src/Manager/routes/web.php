<?php

/**
 * -----------------------------------------------------------------
 * ADMIN ROUTES
 * -----------------------------------------------------------------
 */
Route::group(['prefix' => 'back','middleware' =>['web','auth']],function(){

    // Developer access
    Route::get('translations/lines/create',['middleware' => 'auth.superadmin', 'as' => 'back.squanto.lines.create','uses' => '\Thinktomorrow\Squanto\Manager\Controllers\LineController@create']);
    Route::delete('translations/lines/{id}',['middleware' => 'auth.superadmin', 'as' => 'back.squanto.lines.destroy','uses' => '\Thinktomorrow\Squanto\Manager\Controllers\LineController@destroy']);
    Route::get('translations/lines/{id}/edit',['middleware' => 'auth.superadmin', 'as' => 'back.squanto.lines.edit','uses' => '\Thinktomorrow\Squanto\Manager\Controllers\LineController@edit']);
    Route::put('translations/lines/{id}',['middleware' => 'auth.superadmin', 'as' => 'back.squanto.lines.update','uses' => '\Thinktomorrow\Squanto\Manager\Controllers\LineController@update']);
    Route::post('translations/lines',['middleware' => 'auth.superadmin', 'as' => 'back.squanto.lines.store','uses' => '\Thinktomorrow\Squanto\Manager\Controllers\LineController@store']);

    // Client access
    Route::get('translations/{id}/edit',['as' => 'back.squanto.edit','uses' => '\Thinktomorrow\Squanto\Manager\Controllers\TranslationController@edit']);
    Route::put('translations/{id}',['as' => 'back.squanto.update','uses' => '\Thinktomorrow\Squanto\Manager\Controllers\TranslationController@update']);
    Route::get('translations',['as' => 'back.squanto.index','uses' => '\Thinktomorrow\Squanto\Manager\Controllers\TranslationController@index']);

});