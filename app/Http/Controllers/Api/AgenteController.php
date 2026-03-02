<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Agente;
use App\Models\Sala;
use App\Models\Comando;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class AgenteController extends Controller
{
    /**
     * POST /api/esclavo/register
     * Registra un nuevo agente en el sistema
     * Requiere: nombre_pc, sala_id
     */
    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nombre_pc' => 'required|string|max:255',
            'sala_id' => 'required|integer|exists:salas,id',
            'info_sistema' => 'nullable|array',
        ]);

        try {
            $agente = Agente::updateOrCreate(
                ['nombre_pc' => $validated['nombre_pc']],
                [
                    'sala_id' => $validated['sala_id'],
                    'estado' => 'conectado',
                    'ultimo_heartbeat' => now(),
                    'fecha_registro' => now(),
                    'info_sistema' => $validated['info_sistema'] ?? null
                ]
            );

            $sala = Sala::find($validated['sala_id']);

            return response()->json([
                'success' => true,
                'message' => 'Agente registrado exitosamente',
                'agente_id' => $agente->id,
                'nombre_pc' => $agente->nombre_pc,
                'sala' => [
                    'id' => $sala->id,
                    'nombre' => $sala->nombre,
                ],
                'timestamp' => now()->toIso8601String()
            ], Response::HTTP_CREATED);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validación fallida',
                'errors' => $e->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error registrando agente: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * POST /api/esclavo/heartbeat
     * Agente reporta su estado y recibe comandos pendientes
     * Requiere: nombre_pc, sala_id
     */
    public function heartbeat(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nombre_pc' => 'required|string|max:255',
            'sala_id' => 'required|integer|exists:salas,id',
        ]);

        try {
            // Buscar agente
            $agente = Agente::where('nombre_pc', $validated['nombre_pc'])
                ->where('sala_id', $validated['sala_id'])
                ->first();

            if (!$agente) {
                // Si no existe, registrarlo
                $agente = Agente::create([
                    'nombre_pc' => $validated['nombre_pc'],
                    'sala_id' => $validated['sala_id'],
                    'estado' => 'conectado',
                    'ultimo_heartbeat' => now(),
                    'fecha_registro' => now()
                ]);
            } else {
                // Actualizar heartbeat
                $agente->update([
                    'estado' => 'conectado',
                    'ultimo_heartbeat' => now()
                ]);
            }

            // Obtener comandos pendientes para este agente (máximo 5)
            $comandos = Comando::where('agente_id', $agente->id)
                ->where('estado', 'pendiente')
                ->limit(5)
                ->get()
                ->map(function ($cmd) {
                    return [
                        'id' => $cmd->id,
                        'comando' => $cmd->tipo,  // Cambiar 'tipo' a 'comando'
                        'tipo' => $cmd->tipo,     // Mantener por compatibilidad
                        'sala_id' => $cmd->sala_id,
                        'parametros' => $cmd->parametros
                    ];
                });

            return response()->json([
                'success' => true,
                'agente_id' => $agente->id,
                'nombre_pc' => $agente->nombre_pc,
                'comandos' => $comandos,
                'timestamp' => now()->toIso8601String()
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error en heartbeat: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * POST /api/esclavo/resultado
     * Agente reporta el resultado de un comando ejecutado
     */
    public function resultado(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nombre_pc' => 'required|string|max:255',
            'comando_id' => 'required|integer',
            'resultado' => 'nullable|string',
        ]);

        try {
            $comando = Comando::findOrFail($validated['comando_id']);

            $comando->update([
                'estado' => 'ejecutado',
                'resultado' => $validated['resultado'] ?? null,
                'fecha_ejecucion' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Resultado registrado',
                'comando_id' => $comando->id,
                'timestamp' => now()->toIso8601String()
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * GET /api/agentes
     * Obtiene lista de todos los agentes conectados
     */
    public function listar(): JsonResponse
    {
        try {
            $agentes = Agente::with('sala')->get();

            $agentes_data = $agentes->map(function ($agente) {
                return [
                    'id' => $agente->id,
                    'nombre_pc' => $agente->nombre_pc,
                    'sala' => $agente->sala ? [
                        'id' => $agente->sala->id,
                        'nombre' => $agente->sala->nombre,
                    ] : null,
                    'estado' => $agente->estado,
                    'ultimo_heartbeat' => $agente->ultimo_heartbeat,
                    'info_sistema' => $agente->info_sistema,
                ];
            });

            return response()->json([
                'success' => true,
                'agentes' => $agentes_data,
                'total' => $agentes->count(),
                'timestamp' => now()->toIso8601String()
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * GET /api/agentes/{sala_id}
     * Obtiene agentes de una sala específica
     */
    public function agentesPorSala($sala_id): JsonResponse
    {
        try {
            $sala = Sala::findOrFail($sala_id);
            
            $agentes = $sala->agentes;

            return response()->json([
                'success' => true,
                'sala' => [
                    'id' => $sala->id,
                    'nombre' => $sala->nombre,
                ],
                'agentes' => $agentes->map(function ($agente) {
                    return [
                        'id' => $agente->id,
                        'nombre_pc' => $agente->nombre_pc,
                        'estado' => $agente->estado,
                        'ultimo_heartbeat' => $agente->ultimo_heartbeat,
                    ];
                })->toArray(),
                'total' => $agentes->count(),
                'timestamp' => now()->toIso8601String()
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * GET /api/health
     * Verifica que el API está operativo
     */
    public function health(): JsonResponse
    {
        $agentes_conectados = Agente::where('estado', 'conectado')->count();
        $total_agentes = Agente::count();
        $total_salas = Sala::count();

        return response()->json([
            'success' => true,
            'status' => 'OK',
            'message' => 'API operativa',
            'agentes_conectados' => $agentes_conectados,
            'total_agentes' => $total_agentes,
            'total_salas' => $total_salas,
            'timestamp' => now()->toIso8601String()
        ], Response::HTTP_OK);
    }
}
