<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\GeneralStatsController;
use App\Http\Controllers\Web\TrackableController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::name('api.')->group(function () {
    Route::middleware('auth:sanctum')->group(function () {
        // Category routes
        Route::get('/getCategories', [GeneralStatsController::class, 'getCategories'])->name('getCategories');
        Route::get('/categories', [GeneralStatsController::class, 'getCategories'])->name('categories');

        // Visibility routes
        Route::get('/getVisibilities', [GeneralStatsController::class, 'getVisibilities'])->name('getVisibilities');

        // Goal routes
        Route::get('/getGoals', [GeneralStatsController::class, 'getGoals'])->name('getGoals');

        // Objective routes
        Route::get('/getObjectives', [GeneralStatsController::class, 'getObjectives'])->name('getObjectives');

        // Project routes
        Route::get('/getProjects', [GeneralStatsController::class, 'getProjects'])->name('getProjects');

        // Task routes
        Route::get('/getTasks', [GeneralStatsController::class, 'getTasks'])->name('getTasks');

        // Todo routes
        Route::get('/getTodos', [GeneralStatsController::class, 'getTodos'])->name('getTodos');

        // Activity routes
        Route::get('/getActivities', [GeneralStatsController::class, 'getActivities'])->name('getActivities');

        // Daily Target routes
        Route::get('/getDailyTargets', [GeneralStatsController::class, 'getDailyTargets'])->name('getDailyTargets');

        // Trackable routes
        Route::post('/trackables/get-item/{reference}', [TrackableController::class, 'getItem'])->name('getItem');
    });

});
