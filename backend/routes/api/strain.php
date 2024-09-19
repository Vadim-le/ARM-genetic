<?php 

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StrainController;


Route::group([
    'middleware' => ['auth:api'],
    'prefix' => 'strain'
], function () {
    Route::put('{id}', [StrainController::class, 'update']);
    Route::post('', [StrainController::class, 'store']);
    Route::delete('{id}', [StrainController::class, 'destroy']);
});

Route::group([
    'middleware' => ['api'],
    'prefix' => 'strain'
], function () {
    Route::get('', [StrainController::class, 'getStrains'])->withoutMiddleware('auth');
});