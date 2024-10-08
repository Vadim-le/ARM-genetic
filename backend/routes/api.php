<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProxyController;
use App\Http\Controllers\Auth\VKAuthController;

require_once __DIR__ . '/api/admin.php';
require_once __DIR__ . '/api/auth.php';
require_once __DIR__ . '/api/permissions.php';
require_once __DIR__ . '/api/roles.php';
require_once __DIR__ . '/api/users.php';
require_once __DIR__ . '/api/strain.php';
require_once __DIR__ . '/api/protein.php';


//TODO: Что делаем с этим?

// Прокси (временно, пока нет SSL)
Route::group([
    'middleware' => 'api',
    'prefix' => 'proxy'
], function () {
    Route::get('/vk', [ProxyController::class, 'proxyToVk']);
});


// Аутентификация VK
Route::group([
    'middleware' => 'api',
    'prefix' => 'auth/vkontakte'
], function () {
    //Route::post('/', [VKAuthController::class, 'redirectToProvider']);
    Route::get('/callback', [VKAuthController::class, 'handleProviderCallback']);
});
// Маршруты для VK
// Route::post('auth/vkontakte', [VKAuthController::class, 'redirectToProvider']);
// Route::get('auth/vkontakte/callback', [VKAuthController::class, 'handleProviderCallback']);
