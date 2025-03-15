<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;
use App\Http\Controllers\DailyPlanController;
use App\Http\Controllers\TrackableController;
use App\Http\Controllers\GoalController;
use App\Http\Controllers\ObjectiveController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TodoController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\HomeController;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {

    Route::get('/home', [DailyPlanController::class, 'index'])->name('home2');
    // daily plans
    Route::get('/daily-plans/create', [DailyPlanController::class, 'create'])->name('daily-plans.create');
    Route::get('/today', [DailyPlanController::class, 'today'])->name('daily-plans.today');
    Route::get('/tomorrow', [DailyPlanController::class, 'tomorrow'])->name('daily-plans.tomorrow');
    Route::get('/yesterday', [DailyPlanController::class, 'yesterday'])->name('daily-plans.yesterday');
    Route::get('/day/{date}', [DailyPlanController::class, 'show'])->name('daily-plans.show');
    Route::post('/day', [DailyPlanController::class, 'show'])->name('daily-plans.day');
    Route::get('/calendar', [DailyPlanController::class, 'index'])->name('daily-plans.index');
    Route::post('/daily-plans', [DailyPlanController::class, 'store'])->name('daily-plans.store');
    Route::post('/daily-plans/activities', [DailyPlanController::class, 'getActivities'])->name('daily-plans.activities');

    // Trackables
    Route::get('/trackables', [TrackableController::class, 'index'])->name('trackables.index');
    Route::get('/trackables/{trackable}', [TrackableController::class, 'show'])->name('trackables.show');
    Route::post('/trackables/{trackable}', [TrackableController::class, 'update'])->name('trackables.update');

    // Goals
    Route::get('/goals', [GoalController::class, 'index'])->name('goals.index');
    Route::post('/goals/add', [GoalController::class, 'store'])->name('goals.store');

    // Objectives
    Route::get('/objectives', [ObjectiveController::class, 'index'])->name('objectives.index');
    Route::post('/objectives/add', [ObjectiveController::class, 'store'])->name('objectives.store');

    // Projects
    Route::get('/projects', [ProjectController::class, 'index'])->name('projects.index');
    Route::post('/projects/add', [ProjectController::class, 'store'])->name('projects.store');

    // Tasks
    Route::get('/tasks', [TaskController::class, 'index'])->name('tasks.index');
    Route::post('/tasks/add', [TaskController::class, 'store'])->name('tasks.store');

    // Todos
    Route::get('/todos', [TodoController::class, 'index'])->name('todos.index');
    Route::post('/todos/add', [TodoController::class, 'store'])->name('todos.store');

    // Activities
    Route::get('/activities', [ActivityController::class, 'index'])->name('activities.index');
    Route::post('/activities/add', [ActivityController::class, 'store'])->name('activities.store');

    // Subscribe
    Route::get('/subscribe', [SubscriptionController::class,'subscribe'])->name('subscribe');
    Route::post('/subscribe', [HomeController::class,'subscribe'])->name('subscribe');

//    // Unsubscribe
//    Route::get('/unsubscribe', [HomeController::class, 'unsubscribe'])->name('unsubscribe');
//    Route::post('/unsubscribe', [HomeController::class, 'unsubscribe'])->name('unsubscribe');
//
//    // Notifications
//    Route::get('/notifications', [HomeController::class, 'notifications'])->name('notifications');
//    Route::patch('/notifications/{notification}', [HomeController::class, 'markAsRead'])->name('notifications.markAsRead');
//    Route::delete('/notifications/{notification}', [HomeController::class, 'destroyNotification'])->name('notifications.destroy');
//
//    // Settings
//    Route::get('/settings', [HomeController::class,'settings'])->name('settings');
//    Route::patch('/settings', [HomeController::class,'settings'])->name('settings');



    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');
});

require __DIR__.'/auth.php';
