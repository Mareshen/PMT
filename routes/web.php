<?php
use App\Http\Controllers\TaskPageController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect('/tasks'));
// http://localhost:8000/tasks
Route::get('/tasks', [TaskPageController::class, 'index']);
Route::get('/tasks/create', [TaskPageController::class, 'create']);
Route::post('/tasks', [TaskPageController::class, 'store']);
Route::get('/tasks/{task}', [TaskPageController::class, 'show']);
Route::get('/tasks/{task}/edit', [TaskPageController::class, 'edit']);
Route::put('/tasks/{task}', [TaskPageController::class, 'update']);
Route::delete('/tasks/{task}', [TaskPageController::class, 'destroy']);

Route::post('/tasks/{task}/assign', [TaskPageController::class, 'assign']);
Route::delete('/tasks/{task}/students/{student}', [TaskPageController::class, 'unassign']);
Route::patch('/tasks/{task}/status', [TaskPageController::class, 'updateStatus']);
