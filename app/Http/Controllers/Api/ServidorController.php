<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Agente;
use App\Models\Sala;
use App\Models\Comando;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;

class ServidorController extends Controller
{
    /**
     * POST /api/servidor/enviar-comando
     * Servidor Local (profesor) envía comando a un agente específico
     * Requiere: nombre_pc, sala_id, tipo
     */
    public function enviarComando(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nombre_pc' => 'required|string|max:255',
            'sala_id' => 'required|integer|exists:salas,id',
            'tipo' => 'required|in:lock,unlock,apagar,reiniciar,limpiar_temp,cancelar_shutdown',
            'parametros' => 'nullable|array',
        ]);

        try {
            // Verificar que el agente existe y pertenece a esa sala
            $agente = Agente::where('nombre_pc', $validated['nombre_pc'])
                ->where('sala_id', $validated['sala_id'])
                ->first();

            if (!$agente) {
                return response()->json([
                    'success' => false,
                    'message' => 'Agente no encontrado en esa sala',
                    'nombre_pc' => $validated['nombre_pc'],
                    'sala_id' => $validated['sala_id']
                ], Response::HTTP_NOT_FOUND);
            }

            // Crear el comando
            $comando = Comando::create([
                'agente_id' => $agente->id,
                'sala_id' => $validated['sala_id'],
                'nombre_pc' => $validated['nombre_pc'],
                'tipo' => $validated['tipo'],
                'parametros' => $validated['parametros'] ?? null,
                'estado' => 'pendiente',
                'fecha_envio' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Comando enviado exitosamente',
                'comando_id' => $comando->id,
                'nombre_pc' => $validated['nombre_pc'],
                'tipo' => $validated['tipo'],
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
                'message' => 'Error: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * GET /api/servidor/estado/{sala_id}
     * Obtiene estado de todos los agentes en una sala específica
     */
    public function estado($sala_id): JsonResponse
    {
        try {
            $sala = Sala::findOrFail($sala_id);

            $agentes = Agente::where('sala_id', $sala_id)
                ->with('comandos')
                ->get()
                ->map(function ($agente) {
                    // Contar comandos pendientes
                    $comandos_pendientes = Comando::where('agente_id', $agente->id)
                        ->where('estado', 'pendiente')
                        ->count();

                    return [
                        'id' => $agente->id,
                        'nombre_pc' => $agente->nombre_pc,
                        'estado' => $agente->estado,
                        'comandos_pendientes' => $comandos_pendientes,
                        'ultimo_heartbeat' => $agente->ultimo_heartbeat,
                        'en_linea' => $agente->ultimo_heartbeat && $agente->ultimo_heartbeat->diffInSeconds(now()) < 30
                    ];
                });

            return response()->json([
                'success' => true,
                'sala' => [
                    'id' => $sala->id,
                    'nombre' => $sala->nombre,
                    'descripcion' => $sala->descripcion,
                ],
                'estadisticas' => $sala->estadisticas(),
                'agentes' => $agentes,
                'total_agentes' => count($agentes),
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
     * GET /api/servidor/estado
     * Obtiene estado general (todas las salas)
     */
    public function estadoGlobal(): JsonResponse
    {
        try {
            $salas = Sala::with('agentes')->get();

            $salas_data = $salas->map(function ($sala) {
                return [
                    'id' => $sala->id,
                    'nombre' => $sala->nombre,
                    'total_agentes' => $sala->totalAgentes(),
                    'agentes_conectados' => $sala->agentesConectados(),
                    'agentes_desconectados' => $sala->agentesDesconectados(),
                ];
            });

            return response()->json([
                'success' => true,
                'salas' => $salas_data,
                'timestamp' => now()->toIso8601String()
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
