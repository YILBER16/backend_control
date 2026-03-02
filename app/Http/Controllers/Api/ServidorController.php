<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Agente;
use App\Models\Comando;
use Illuminate\Http\Request;

class ServidorController extends Controller
{
    /**
     * POST /api/servidor/enviar-comando
     * Servidor Local (profesor) envía comando a un agente específico
     */
    public function enviarComando(Request $request)
    {
        $validated = $request->validate([
            'nombre_pc' => 'required|string|max:255',
            'tipo' => 'required|in:lock,apagar,reiniciar,limpiar_temp',
            'parametros' => 'nullable|array',
        ]);

        try {
            // Verificar que el agente existe
            $agente = Agente::where('nombre_pc', $validated['nombre_pc'])->first();

            if (!$agente) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Agente no encontrado',
                    'nombre_pc' => $validated['nombre_pc']
                ], 404);
            }

            // Crear el comando
            $comando = Comando::create([
                'agente_id' => $agente->id,
                'nombre_pc' => $validated['nombre_pc'],
                'tipo' => $validated['tipo'],
                'parametros' => json_encode($validated['parametros'] ?? []),
                'estado' => 'pendiente',
                'fecha_envio' => now()
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Comando enviado exitosamente',
                'comando_id' => $comando->id,
                'nombre_pc' => $validated['nombre_pc'],
                'tipo' => $validated['tipo'],
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
     * GET /api/servidor/estado
     * Obtiene estado de todos los agentes la sala especificada
     */
    public function estado(Request $request)
    {
        try {
            $sala = $request->input('sala', '1');

            $agentes = Agente::where('sala', $sala)
                ->get()
                ->map(function ($agente) {
                    // Contar comandos pendientes
                    $comandos_pendientes = Comando::where('agente_id', $agente->id)
                        ->where('estado', 'pendiente')
                        ->count();

                    return [
                        'id' => $agente->id,
                        'nombre_pc' => $agente->nombre_pc,
                        'sala' => $agente->sala,
                        'estado' => $agente->estado,
                        'comandos_pendientes' => $comandos_pendientes,
                        'ultimo_heartbeat' => $agente->ultimo_heartbeat?->diffForHumans(),
                        'en_linea' => $agente->ultimo_heartbeat?->diffInSeconds(now()) < 30
                    ];
                });

            return response()->json([
                'status' => 'ok',
                'sala' => $sala,
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
}

