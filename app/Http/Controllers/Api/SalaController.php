<?php

namespace App\Http\Controllers\Api;

use App\Models\Sala;
use App\Models\Agente;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;

class SalaController
{
    /**
     * Listar todas las salas con estadísticas
     * GET /api/salas
     */
    public function index(): JsonResponse
    {
        try {
            $salas = Sala::with('agentes')->get();
            
            $salas_data = $salas->map(function ($sala) {
                return [
                    'id' => $sala->id,
                    'nombre' => $sala->nombre,
                    'descripcion' => $sala->descripcion,
                    'ubicacion' => $sala->ubicacion,
                    'capacidad' => $sala->capacidad,
                    'estado' => $sala->estado,
                    'estadisticas' => $sala->estadisticas(),
                    'agentes' => $sala->agentes->map(function ($agente) {
                        return [
                            'id' => $agente->id,
                            'nombre_pc' => $agente->nombre_pc,
                            'estado' => $agente->estado,
                            'ultimo_heartbeat' => $agente->ultimo_heartbeat,
                        ];
                    })->toArray(),
                ];
            });
            
            return response()->json([
                'success' => true,
                'total_salas' => $salas->count(),
                'salas' => $salas_data,
                'timestamp' => now()->toIso8601String(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error obteniendo salas: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Obtener una sala específica con sus agentes
     * GET /api/salas/{id}
     */
    public function show($id): JsonResponse
    {
        try {
            $sala = Sala::with('agentes')->findOrFail($id);
            
            return response()->json([
                'success' => true,
                'sala' => [
                    'id' => $sala->id,
                    'nombre' => $sala->nombre,
                    'descripcion' => $sala->descripcion,
                    'ubicacion' => $sala->ubicacion,
                    'capacidad' => $sala->capacidad,
                    'estado' => $sala->estado,
                    'estadisticas' => $sala->estadisticas(),
                    'agentes' => $sala->agentes->map(function ($agente) {
                        return [
                            'id' => $agente->id,
                            'nombre_pc' => $agente->nombre_pc,
                            'estado' => $agente->estado,
                            'ultimo_heartbeat' => $agente->ultimo_heartbeat,
                            'info_sistema' => $agente->info_sistema,
                        ];
                    })->toArray(),
                ],
                'timestamp' => now()->toIso8601String(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Sala no encontrada'
            ], Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Crear una nueva sala
     * POST /api/salas
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'nombre' => 'required|string|unique:salas,nombre',
                'descripcion' => 'nullable|string',
                'capacidad' => 'nullable|integer|min:1',
                'ubicacion' => 'nullable|string',
                'estado' => 'nullable|in:activa,inactiva,mantenimiento',
            ]);

            $sala = Sala::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Sala creada correctamente',
                'sala' => [
                    'id' => $sala->id,
                    'nombre' => $sala->nombre,
                    'descripcion' => $sala->descripcion,
                    'ubicacion' => $sala->ubicacion,
                    'capacidad' => $sala->capacidad,
                    'estado' => $sala->estado,
                ]
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
                'message' => 'Error creando sala: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Actualizar una sala
     * PUT /api/salas/{id}
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $sala = Sala::findOrFail($id);

            $validated = $request->validate([
                'nombre' => 'nullable|string|unique:salas,nombre,' . $id,
                'descripcion' => 'nullable|string',
                'capacidad' => 'nullable|integer|min:1',
                'ubicacion' => 'nullable|string',
                'estado' => 'nullable|in:activa,inactiva,mantenimiento',
            ]);

            $sala->update(array_filter($validated));

            return response()->json([
                'success' => true,
                'message' => 'Sala actualizada correctamente',
                'sala' => $sala->fresh()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error actualizando sala: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Eliminar una sala
     * DELETE /api/salas/{id}
     */
    public function destroy($id): JsonResponse
    {
        try {
            $sala = Sala::findOrFail($id);
            
            $nombre = $sala->nombre;
            $sala->delete();

            return response()->json([
                'success' => true,
                'message' => 'Sala ' . $nombre . ' eliminada correctamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error eliminando sala: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Obtener estadísticas generales de todas las salas
     * GET /api/salas-estadisticas
     */
    public function estadisticas(): JsonResponse
    {
        try {
            $salas = Sala::with('agentes')->get();
            
            $total_salas = $salas->count();
            $total_agentes = Agente::count();
            $agentes_conectados = Agente::where('estado', 'conectado')->count();
            $agentes_desconectados = Agente::where('estado', 'desconectado')->count();
            
            $salas_data = $salas->map(function ($sala) {
                return [
                    'id' => $sala->id,
                    'nombre' => $sala->nombre,
                    'total_agentes' => $sala->totalAgentes(),
                    'agentes_conectados' => $sala->agentesConectados(),
                    'agentes_desconectados' => $sala->agentesDesconectados(),
                    'porcentaje_ocupacion' => round(($sala->totalAgentes() / $sala->capacidad) * 100, 2),
                ];
            });
            
            return response()->json([
                'success' => true,
                'estadisticas_globales' => [
                    'total_salas' => $total_salas,
                    'total_agentes' => $total_agentes,
                    'agentes_conectados' => $agentes_conectados,
                    'agentes_desconectados' => $agentes_desconectados,
                    'porcentaje_actividad' => round(($agentes_conectados / max($total_agentes, 1)) * 100, 2),
                ],
                'salas' => $salas_data,
                'timestamp' => now()->toIso8601String(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error obteniendo estadísticas: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
