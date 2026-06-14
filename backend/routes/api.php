<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // ms_provinsi routes
    Route::post('/provinsi/list', [App\Http\Controllers\MsProvinsiController::class, 'list']);
    Route::post('/provinsi/detail', [App\Http\Controllers\MsProvinsiController::class, 'detail']);
    Route::post('/provinsi/create', [App\Http\Controllers\MsProvinsiController::class, 'create']);
    Route::post('/provinsi/update', [App\Http\Controllers\MsProvinsiController::class, 'update']);
    Route::post('/provinsi/delete', [App\Http\Controllers\MsProvinsiController::class, 'delete']);
});
