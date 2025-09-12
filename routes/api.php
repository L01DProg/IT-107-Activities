<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\SubjectController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::prefix('v1')->group(function () {
    Route::post('admin-register',[AdminController::class, 'adminRegistration']);
    Route::post('/admin-login',[AdminController::class, 'adminSignIn']);
});

Route::middleware('auth:sanctum')->group( function () {
    Route::post('/add-subject',[SubjectController::class, 'assignSubject']);
    Route::post('/admin-logout',[AdminController::class, 'adminSignOut']);
});