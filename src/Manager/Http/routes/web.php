<?php

/**
 * -----------------------------------------------------------------
 * ADMIN ROUTES
 * -----------------------------------------------------------------
 */
Route::group(['prefix' => 'back','middleware' =>['web','auth']], function () {

    // Developer access
    // You should protect these routes to developers only. The middleware squanto.developer is a default unsafe placeholder
    // for think tomorrow develoment but can be used as a starting point.
    Route::get('translations/lines/create', ['middleware' => 'squanto.developer', 'as' => 'squanto.lines.create','uses' => '\Thinktomorrow\Squanto\Manager\Http\Controllers\LineController@create']);
    Route::delete('translations/lines/{id}', ['middleware' => 'squanto.developer', 'as' => 'squanto.lines.destroy','uses' => '\Thinktomorrow\Squanto\Manager\Http\Controllers\LineController@destroy']);
    Route::get('translations/lines/{id}/edit', ['middleware' => 'squanto.developer', 'as' => 'squanto.lines.edit','uses' => '\Thinktomorrow\Squanto\Manager\Http\Controllers\LineController@edit']);
    Route::put('translations/lines/{id}', ['middleware' => 'squanto.developer', 'as' => 'squanto.lines.update','uses' => '\Thinktomorrow\Squanto\Manager\Http\Controllers\LineController@update']);
    Route::post('translations/lines', ['middleware' => 'squanto.developer', 'as' => 'squanto.lines.store','uses' => '\Thinktomorrow\Squanto\Manager\Http\Controllers\LineController@store']);

    // Client access
    Route::get('translations/{id}/edit', ['as' => 'squanto.edit','uses' => '\Thinktomorrow\Squanto\Manager\Http\Controllers\TranslationController@edit']);
    Route::put('translations/{id}', ['as' => 'squanto.update','uses' => '\Thinktomorrow\Squanto\Manager\Http\Controllers\TranslationController@update']);
    Route::get('translations', ['as' => 'squanto.index','uses' => '\Thinktomorrow\Squanto\Manager\Http\Controllers\TranslationController@index']);
});
