<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AgenteController;
use App\Http\Controllers\Api\ServidorController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group.
|
*/

// ===== RUTAS PARA AGENTES (Clientes HTTP) =====
Route::prefix('esclavo')->group(function () {
    // Registro del agente
    Route::post('/register', [AgenteController::class, 'register']);

    // Heartbeat / Polling para recibir comandos
    Route::post('/heartbeat', [AgenteController::class, 'heartbeat']);

    // Reportar resultado de comandos ejecutados
    Route::post('/resultado', [AgenteController::class, 'resultado']);
});

// ===== RUTAS PARA SERVIDOR LOCAL (Profesor) =====
Route::prefix('servidor')->group(function () {
    // Enviar comando a un agente
    Route::post('/enviar-comando', [ServidorController::class, 'enviarComando']);

    // Obtener estado de agentes en una sala
    Route::get('/estado', [ServidorController::class, 'estado']);
});

// ===== RUTAS PÚBLICAS =====

// Health check
Route::get('/health', [AgenteController::class, 'health']);

// Listar todos los agentes
Route::get('/agentes', [AgenteController::class, 'listar']);

// API info
Route::get('/', function () {
    return response()->json([
        'status' => 'ok',
        'message' => 'Control Ciber API - REST HTTP',
        'version' => '1.0.0',
        'endpoints' => [
            'POST /api/esclavo/register',
            'POST /api/esclavo/heartbeat',
            'POST /api/esclavo/resultado',
            'POST /api/servidor/enviar-comando',
            'GET /api/servidor/estado',
            'GET /api/agentes',
            'GET /api/health'
        ]
    ]);
});
