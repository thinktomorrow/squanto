<?php

/**
 * -----------------------------------------------------------------
 * SQUANTO MANAGER ROUTES
 * -----------------------------------------------------------------
 */
Route::group(
    ['prefix' => 'admin','middleware' => ['web', 'auth']],
    function () {
        Route::get('translations/{id}/edit', [\Thinktomorrow\Squanto\Manager\Http\ManagerController::class, 'edit'])->name('squanto.edit');
        Route::put('translations/{id}', [\Thinktomorrow\Squanto\Manager\Http\ManagerController::class, 'update'])->name('squanto.update');
        Route::get('translations', [\Thinktomorrow\Squanto\Manager\Http\ManagerController::class, 'index'])->name('squanto.index');
    }
);
