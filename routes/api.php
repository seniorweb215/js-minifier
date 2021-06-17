<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\Themes;
use App\Http\Controllers\Api\Assets;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth.shopify')->group(function() {
    Route::get('/themes', [Themes::class, 'index']);
    Route::get('/assets/{theme_id}', [Assets::class, 'index']);
    Route::post('/assets/minify/{asset_id}', [Assets::class, 'minify']);
    Route::post('/assets/revert/{asset_id}', [Assets::class, 'revert']);
    Route::post('/assets/minifyAll/{theme_id}', [Assets::class, 'minifyAll']);
    Route::post('/assets/revertAll/{theme_id}', [Assets::class, 'revertAll']);
});