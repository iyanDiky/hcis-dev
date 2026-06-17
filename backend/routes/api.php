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
    Route::post('/provinsi/all', [App\Http\Controllers\MsProvinsiController::class, 'all']);

    // ms_kota_kab routes
    Route::post('/kota/list', [App\Http\Controllers\MsKotaKabController::class, 'list']);
    Route::post('/kota/detail', [App\Http\Controllers\MsKotaKabController::class, 'detail']);
    Route::post('/kota/create', [App\Http\Controllers\MsKotaKabController::class, 'create']);
    Route::post('/kota/update', [App\Http\Controllers\MsKotaKabController::class, 'update']);
    Route::post('/kota/delete', [App\Http\Controllers\MsKotaKabController::class, 'delete']);

    // SdmJenisController routes
    Route::post('/sdm-jenis/list', [\App\Http\Controllers\SdmJenisController::class, 'list']);
    Route::post('/sdm-jenis/all', [\App\Http\Controllers\SdmJenisController::class, 'all']);
    Route::post('/sdm-jenis/detail', [\App\Http\Controllers\SdmJenisController::class, 'detail']);
    Route::post('/sdm-jenis/create', [\App\Http\Controllers\SdmJenisController::class, 'create']);
    Route::post('/sdm-jenis/update', [\App\Http\Controllers\SdmJenisController::class, 'update']);
    Route::post('/sdm-jenis/delete', [\App\Http\Controllers\SdmJenisController::class, 'delete']);
});
