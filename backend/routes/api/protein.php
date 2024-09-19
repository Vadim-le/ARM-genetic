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

   // Route::get('', [StrainController::class, 'getStrains'])->withoutMiddleware('auth');
});