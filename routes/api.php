<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AgenteController;
use App\Http\Controllers\Api\ServidorController;
use App\Http\Controllers\Api\SalaController;

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

// ===== RUTAS PARA SALAS (Gestión) =====
Route::prefix('salas')->group(function () {
    // Listar todas las salas
    Route::get('/', [SalaController::class, 'index']);

    // Obtener una sala específica
    Route::get('/{id}', [SalaController::class, 'show']);

    // Crear nueva sala
    Route::post('/', [SalaController::class, 'store']);

    // Actualizar sala
    Route::put('/{id}', [SalaController::class, 'update']);

    // Eliminar sala
    Route::delete('/{id}', [SalaController::class, 'destroy']);

    // Estadísticas de salas
    Route::get('/estadisticas/resumen', [SalaController::class, 'estadisticas']);
});

// ===== RUTAS PARA AGENTES (Clientes HTTP) =====
Route::prefix('esclavo')->group(function () {
    // Registro del agente (requiere sala_id)
    Route::post('/register', [AgenteController::class, 'register']);

    // Heartbeat / Polling para recibir comandos (requiere sala_id)
    Route::post('/heartbeat', [AgenteController::class, 'heartbeat']);

    // Reportar resultado de comandos ejecutados
    Route::post('/resultado', [AgenteController::class, 'resultado']);
});

// ===== RUTAS PARA SERVIDOR LOCAL (Profesor) =====
Route::prefix('servidor')->group(function () {
    // Enviar comando a un agente en una sala específica
    Route::post('/enviar-comando', [ServidorController::class, 'enviarComando']);

    // Obtener estado de agentes en una sala específica
    Route::get('/estado/{sala_id}', [ServidorController::class, 'estado']);

    // Obtener estado global de todas las salas
    Route::get('/estado', [ServidorController::class, 'estadoGlobal']);
});

// ===== RUTAS PÚBLICAS =====

// Health check
Route::get('/health', [AgenteController::class, 'health']);

// Listar todos los agentes
Route::get('/agentes', [AgenteController::class, 'listar']);

// Listar agentes de una sala específica
Route::get('/agentes/{sala_id}', [AgenteController::class, 'agentesPorSala']);

// API info
Route::get('/', function () {
    return response()->json([
        'success' => true,
        'message' => 'Control Ciber API - REST HTTP',
        'version' => '1.0.0',
        'description' => 'Sistema de control remoto de PCs por salas/aulas',
        'endpoints' => [
            'SALAS' => [
                'GET /api/salas',
                'GET /api/salas/{id}',
                'POST /api/salas',
                'PUT /api/salas/{id}',
                'DELETE /api/salas/{id}',
                'GET /api/salas/estadisticas/resumen',
            ],
            'AGENTES' => [
                'POST /api/esclavo/register (requiere: nombre_pc, sala_id)',
                'POST /api/esclavo/heartbeat (requiere: nombre_pc, sala_id)',
                'POST /api/esclavo/resultado (requiere: nombre_pc, comando_id, resultado)',
                'GET /api/agentes',
                'GET /api/agentes/{sala_id}',
            ],
            'SERVIDOR' => [
                'POST /api/servidor/enviar-comando (requiere: nombre_pc, sala_id, tipo)',
                'GET /api/servidor/estado/{sala_id}',
                'GET /api/servidor/estado',
            ],
            'SALUD' => [
                'GET /api/health',
            ]
        ]
    ]);
});
