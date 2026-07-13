<?php

use Thinktomorrow\Squanto\Manager\Http\ManagerController;

/**
 * -----------------------------------------------------------------
 * SQUANTO MANAGER ROUTES
 * -----------------------------------------------------------------
 */
Route::group(
    ['prefix' => 'admin', 'middleware' => ['web', 'auth']],
    function () {
        Route::get('translations/{id}/edit', [ManagerController::class, 'edit'])->name('squanto.edit');
        Route::put('translations/{id}', [ManagerController::class, 'update'])->name('squanto.update');
        Route::get('translations', [ManagerController::class, 'index'])->name('squanto.index');
    }
);
