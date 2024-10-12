<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\JWTMiddleware;
use App\Http\Middleware\CorsMiddleware;
use App\Http\Controllers\Api\TeamController;
use App\Http\Controllers\Api\ToolController;
use App\Http\Controllers\Api\UnitController;
use App\Http\Controllers\Api\LoginController;
use App\Http\Controllers\QuotationController;
use App\Http\Controllers\Api\ClientController;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\ContractController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api');

Route::post('login', [LoginController::class, 'login'])->name('login');

Route::post('create-account', [LoginController::class, 'store']);

Route::middleware([JWTMiddleware::class])->group(function () {
    Route::post('logout', [LoginController::class, 'logout']);
    Route::resource('client', ClientController::class);

    Route::get('/service', [ServiceController::class, 'index']);
    Route::get('/service/{id}', [ServiceController::class, 'show']);
    Route::post('/service', [ServiceController::class, 'store']);
    Route::post('/service/{id}', [ServiceController::class, 'update']);
    Route::delete('/service/{id}', [ServiceController::class, 'destroy']);
    Route::get('/get-service-of-client/{client_id}', [ServiceController::class, 'getServiceOfClient']);

    Route::get('/unit/{client_id}', [UnitController::class, 'show']);

    Route::resource('tool', ToolController::class);

    Route::resource('team', TeamController::class);

    Route::get('/contract_and_quotation', [ContractController::class, 'getContractAndQuotation']);
    Route::post('contract', [ContractController::class, 'addContractToForClient']);
    Route::post('quotation', [QuotationController::class, 'addQuotationToForClient']);
    Route::post('contract/approval/{contract_id}', [ContractController::class, 'approval']);
});

