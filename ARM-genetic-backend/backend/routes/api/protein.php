<?php 

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProteinController;


//Route::group([
//    'middleware' => ['auth:api'],
//    'prefix' => 'strain'
//], function () {
//    Route::post('', [ProteinController::class, 'translate']);
//});

Route::group([
    'middleware' => ['api'],
    'prefix' => 'proteins'
], function () {
    Route::post('/translate', [ProteinController::class, 'translate']);
    Route::post('/analyze', [ProteinController::class, 'analyze'])->withoutMiddleware('auth'); // Новый маршрут для анализа


   // Route::get('', [StrainController::class, 'getStrains'])->withoutMiddleware('auth');
});