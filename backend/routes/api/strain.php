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
    Route::post('/find-repeats', [StrainController::class, 'findRepeats']);
    Route::get('/name', [StrainController::class, 'getAllStrainNames']);
    

});

Route::group([
    'middleware' => ['api'],
    'prefix' => 'strain'
], function () {
    Log::info('получить последовательность!');
    Route::get('', [StrainController::class, 'getStrains'])->withoutMiddleware('auth');
});