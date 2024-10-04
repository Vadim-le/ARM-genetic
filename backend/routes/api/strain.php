<?php 

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StrainController;


Route::group([
    'middleware' => ['auth:api'],
    'prefix' => 'strain'
], function () {
    Route::patch('/update_status/{id}', [StrainController::class, 'updateAnalyzeRecordStatus']);
    Route::put('{id}', [StrainController::class, 'update']);
    Route::post('', [StrainController::class, 'store']);
    Route::delete('{id}', [StrainController::class, 'destroy']);
    Route::post('/find-repeats', [StrainController::class, 'findRepeats']);
    Route::get('/name', [StrainController::class, 'getAllStrainNames']);
    Route::get('/analyze_records', [StrainController::class, 'getAnalyzeRecordsByStrainName']);
   

    

});

Route::group([
    'middleware' => ['api'],
    'prefix' => 'strain'
], function () {
    Route::get('', [StrainController::class, 'getStrains'])->withoutMiddleware('auth');
});