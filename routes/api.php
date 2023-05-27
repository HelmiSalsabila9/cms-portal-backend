<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Admin\LoginController;
use App\Http\Controllers\Api\Admin\TagController;
use App\Http\Controllers\Api\Admin\CategoryController;
use App\Http\Controllers\Api\Admin\PostController;
use App\Http\Controllers\Api\Admin\MenuController;
use App\Http\Controllers\Api\Admin\SliderController;
use App\Http\Controllers\Api\Admin\UserController;
use App\Http\Controllers\Api\Admin\DashboardController;
use App\Http\Controllers\Api\Web\TagController as WebTagController;
use App\Http\Controllers\Api\Web\CategoryController as WebCategoryController;
use App\Http\Controllers\Api\Web\PostController as WebPostController;
use App\Http\Controllers\Api\Web\MenuController as WebMenuController;
use App\Http\Controllers\Api\Web\SliderController as WebSliderController;
use App\Http\Controllers\Api\Web\SocialShareButtonsController as WebSharePage;

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

/*
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
*/

// Group route prefix Admin
Route::prefix('admin')->group(function() {
    
    // route LOGIN
    Route::post('/login', [LoginController::class, 'index']);
    
    // Group route with middleware AUTH
    Route::group(['middleware' => 'auth:api'], function() {
        
        // Data user
        Route::get('/user', [LoginController::class, 'getUser']);

        // Refresh token JWT
        Route::get('/refresh', [LoginController::class, 'refreshToken']);

        // Logout
        Route::post('/logout', [LoginController::class, 'logout']);

        // Tags
        Route::apiResource('/tags', TagController::class);

        // Category
        Route::apiResource('/categories', CategoryController::class);

        // Post
        Route::apiResource('/posts', PostController::class);

        // Menus
        Route::apiResource('/menus', MenuController::class);

        // Sliders
        Route::apiResource('/sliders', SliderController::class);

        // Users
        Route::apiResource('/users', UserController::class);

        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index']);
    });
});

// group route with prefix WEB
Route::prefix('web')->group(function() {

    // Index tags
    Route::get('/tags', [WebTagController::class, 'index']);

    // Show Tag
    Route::get('/tags/{slug}', [WebTagController::class, 'show']);

    // Tags Footer
    Route::get('footer', [WebTagController::class, 'footer']);

    // Index Category
    Route::get('/categories', [WebCategoryController::class, 'index']);

    // Show Category
    Route::get('/categories/{slug}', [WebCategoryController::class, 'show']);

    // Sidebar Category
    Route::get('/categorySidebar', [WebCategoryController::class, 'categorySidebar']);

    // Index Posts
    Route::get('/posts', [WebPostController::class, 'index']);

    // Show Posts
    Route::get('/posts/{slug}', [WebPostController::class, 'show']);

    // postHomepage
    Route::get('/postHomepage', [WebPostController::class, 'postHomepage']);

    // Store Comment
    Route::post('/posts/storeComment', [WebPostController::class, 'storeComment']);

    // Store Image
    Route::post('/posts/storeImage', [WebPostController::class, 'storeImagePost']);

    // Index Menus
    Route::get('/menus', [WebMenuController::class, 'index']);

    // Index Sliders
    Route::get('/sliders', [WebSliderController::class, 'index']);
});