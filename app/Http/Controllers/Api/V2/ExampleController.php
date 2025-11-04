<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Example Modern API Controller
 * 
 * Este é um exemplo de controller moderno que será roteado pelo middleware
 */
class ExampleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        return response()->json([
            'message' => 'API v2 funcionando!',
            'type' => 'modern',
            'data' => [
                'version' => '2.0.0',
                'timestamp' => now()->toDateTimeString(),
            ],
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        return response()->json([
            'message' => 'Recurso criado com sucesso',
            'data' => $validated,
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        return response()->json([
            'message' => 'Recurso encontrado',
            'data' => [
                'id' => $id,
                'name' => 'Exemplo',
                'description' => 'Este é um recurso de exemplo',
            ],
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
        ]);

        return response()->json([
            'message' => 'Recurso atualizado com sucesso',
            'data' => array_merge(['id' => $id], $validated),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        return response()->json([
            'message' => 'Recurso deletado com sucesso',
            'id' => $id,
        ]);
    }

    /**
     * Example of integration with legacy database
     *
     * @return JsonResponse
     */
    public function legacyIntegration(): JsonResponse
    {
        try {
            // Exemplo: consultar usuários do e-Cidade
            $users = \DB::connection('pgsql')
                ->table('configuracoes.db_usuarios')
                ->select('id_usuario', 'nome', 'login')
                ->where('usuarioativo', 1)
                ->limit(10)
                ->get();

            return response()->json([
                'message' => 'Integração com banco legado funcionando',
                'total' => $users->count(),
                'users' => $users,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao acessar dados legados',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
