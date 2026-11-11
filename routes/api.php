<?php
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TaskAssignmentController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TaskStatusController;
use Illuminate\Support\Facades\Route;

Route::get('students', [StudentController::class, 'index']);
Route::get('students/{student}', [StudentController::class, 'show']);
Route::get('students/{student}/tasks', [StudentController::class, 'tasks']);
Route::get('categories', [CategoryController::class, 'index']);
Route::get('categories/{category}', [CategoryController::class, 'show']);
Route::get('categories/{category}/tasks', [CategoryController::class, 'tasks']);
Route::apiResource('tasks', TaskController::class);
Route::post('tasks/{task}/students', [TaskAssignmentController::class, 'store']);
Route::put('tasks/{task}/students', [TaskAssignmentController::class, 'update']);
Route::delete('tasks/{task}/students/{student}', [TaskAssignmentController::class, 'destroy']);
Route::patch('tasks/{task}/status', [TaskStatusController::class, 'update']);
