<?php

use App\Http\Controllers\Public\PublicDisplayController;

Route::group(array('middleware' => []), function () {

    Route::get('display/{shortName}/{token}', [PublicDisplayController::class, 'getBoardDisplay'])->name('public.display');
    Route::get('public/display/load-map/{shortName}/{token}/{base}', [PublicDisplayController::class, 'getLoadMap'])->name('public.display.load-map');

});
