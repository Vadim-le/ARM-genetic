<?php 

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrganizationController;


Route::group([
    'middleware' => ['auth:api'],
    'prefix' => 'organizations'
], function () {
    Route::get('', [OrganizationController::class, 'getOrganizations']);
    Route::post('', [OrganizationController::class, 'store']);
    Route::put('{id}', [OrganizationController::class, 'update']);
    Route::delete('{id}', [OrganizationController::class, 'destroy']);
    Route::put('{id}/status', [OrganizationController::class, 'updateStatus']);
});