<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Agente;
use App\Models\Comando;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AgenteController extends Controller
{
    /**
     * POST /api/esclavo/register
     * Registra un nuevo agente en el sistema
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'nombre_pc' => 'required|string|max:255',
            'sala' => 'required|string|max:255',
        ]);

        try {
            $agente = Agente::updateOrCreate(
                ['nombre_pc' => $validated['nombre_pc']],
                [
                    'sala' => $validated['sala'],
                    'estado' => 'conectado',
                    'ultimo_heartbeat' => now(),
                    'fecha_registro' => now(),
                    'info_sistema' => json_encode($request->all())
                ]
            );

            return response()->json([
                'status' => 'success',
                'message' => 'Agente registrado exitosamente',
                'agente_id' => $agente->id,
                'timestamp' => now()->toIso8601String()
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * POST /api/esclavo/heartbeat
     * Agente reporta su estado y recibe comandos pendientes
     */
    public function heartbeat(Request $request)
    {
        $validated = $request->validate([
            'nombre_pc' => 'required|string|max:255',
            'sala' => 'required|string|max:255',
        ]);

        try {
            // Actualizar último heartbeat del agente
            $agente = Agente::where('nombre_pc', $validated['nombre_pc'])->first();

            if (!$agente) {
                // Si no existe, registrarlo
                $agente = Agente::create([
                    'nombre_pc' => $validated['nombre_pc'],
                    'sala' => $validated['sala'],
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
            $comandos = Comando::where('nombre_pc', $validated['nombre_pc'])
                ->where('estado', 'pendiente')
                ->limit(5)
                ->get()
                ->map(function ($cmd) {
                    return [
                        'id' => $cmd->id,
                        'tipo' => $cmd->tipo,
                        'parametros' => $cmd->parametros ? json_decode($cmd->parametros) : null
                    ];
                });

            return response()->json([
                'status' => 'ok',
                'timestamp' => now()->toIso8601String(),
                'comandos' => $comandos,
                'count' => count($comandos)
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * POST /api/esclavo/resultado
     * Agente reporta el resultado de un comando ejecutado
     */
    public function resultado(Request $request)
    {
        $validated = $request->validate([
            'nombre_pc' => 'required|string|max:255',
            'comando_id' => 'required|integer',
            'estado' => 'required|in:ejecutado,error',
            'resultado' => 'nullable|string',
        ]);

        try {
            $comando = Comando::find($validated['comando_id']);

            if (!$comando) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Comando no encontrado'
                ], 404);
            }

            $comando->update([
                'estado' => $validated['estado'],
                'resultado' => $validated['resultado'] ?? null,
                'fecha_ejecucion' => now()
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Resultado registrado',
                'timestamp' => now()->toIso8601String()
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * GET /api/agentes
     * Obtiene lista de todos los agentes conectados
     */
    public function listar()
    {
        try {
            $agentes = Agente::all()
                ->map(function ($agente) {
                    return [
                        'id' => $agente->id,
                        'nombre_pc' => $agente->nombre_pc,
                        'sala' => $agente->sala,
                        'estado' => $agente->estado,
                        'ultimo_heartbeat' => $agente->ultimo_heartbeat?->diffForHumans(),
                        'fecha_registro' => $agente->fecha_registro?->toIso8601String()
                    ];
                });

            return response()->json([
                'status' => 'ok',
                'agentes' => $agentes,
                'total' => count($agentes),
                'timestamp' => now()->toIso8601String()
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * GET /api/health
     * Verifica que el API está operativo
     */
    public function health()
    {
        return response()->json([
            'status' => 'OK',
            'message' => 'API operativa',
            'agentes_conectados' => Agente::where('estado', 'conectado')->count(),
            'timestamp' => now()->toIso8601String()
        ], 200);
    }
}

