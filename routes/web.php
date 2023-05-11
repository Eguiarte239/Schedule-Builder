<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GoogleAuthController;
use App\Http\Livewire\PhaseController;
use App\Http\Livewire\ProjectController;
use App\Http\Livewire\TaskController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified'
])->group(function () {
    Route::get('/', ProjectController::class)->name('projects');
    Route::get('/phase', PhaseController::class)->name('phases');
    Route::get('/task', TaskController::class)->name('tasks');
    Route::get('/task-reminder', function (){
        return view('mails.task-reminder');
    })->name('task-reminder');
});

Route::get('/auth/redirect', [GoogleAuthController::class, 'redirect'])->name('google.redirect');
Route::get('/auth/callback', [GoogleAuthController::class, 'callback'])->name('google.callback');
