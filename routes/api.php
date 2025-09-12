<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\TeacherController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::prefix('v1')->group(function () {
    Route::post('/admin-register',[AdminController::class, 'adminRegistration']);
    Route::post('/admin-login',[AdminController::class, 'adminSignIn']);
    Route::post('/teacher-login', [TeacherController::class, 'teacherSignIn']);
});

Route::middleware('auth:sanctum')->group( function () {
    Route::post('/add-subject',[SubjectController::class, 'addSubject']);
    Route::post('/admin-logout',[AdminController::class, 'adminSignOut']);
    Route::post('/teacher-registration',[TeacherController::class, 'teacherRegister']);
    Route::post('/teacher-logout',[TeacherController::class, 'teacherSignOut']);
});