<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * @group Tareas
 *
 * Endpoints para gestionar tareas (CRUD). Todos requieren autenticación.
 */
class TaskController extends Controller
{
    /**
     * Listar tareas
     *
     * Obtiene una lista paginada de tareas del usuario autenticado.
     *
     * @authenticated
     *
     * @queryParam page Número de página. Example: 1
     * @queryParam per_page Items por página. Example: 15
     *
     * @response {
     *   "data": [
     *     {
     *       "id": 1,
     *       "title": "Mi tarea",
     *       "description": "Descripción de la tarea",
     *       "status": "pending",
     *       "created_at": "2026-06-11T12:00:00.000000Z",
     *       "updated_at": "2026-06-11T12:00:00.000000Z"
     *     }
     *   ],
     *   "meta": {
     *     "current_page": 1,
     *     "last_page": 1,
     *     "per_page": 15,
     *     "total": 1
     *   }
     * }
     */
    public function index(): AnonymousResourceCollection
    {
        $tasks = Task::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return TaskResource::collection($tasks);
    }

    /**
     * Crear tarea
     *
     * Crea una nueva tarea para el usuario autenticado.
     *
     * @authenticated
     *
     * @bodyParam title string required Título de la tarea. Example: Comprar víveres
     * @bodyParam description string Descripción de la tarea. Example: Leche, pan, huevos
     * @bodyParam status string Estado: pending, in_progress, completed. Example: pending
     *
     * @response 201 {
     *   "message": "Tarea creada exitosamente.",
     *   "data": {
     *     "id": 1,
     *     "title": "Comprar víveres",
     *     "description": "Leche, pan, huevos",
     *     "status": "pending",
     *     "created_at": "2026-06-11T12:00:00.000000Z",
     *     "updated_at": "2026-06-11T12:00:00.000000Z"
     *   }
     * }
     */
    public function store(StoreTaskRequest $request): JsonResponse
    {
        $task = Task::create([
            ...$request->validated(),
            'user_id' => auth()->id(),
        ]);

        return response()->json([
            'message' => 'Tarea creada exitosamente.',
            'data' => new TaskResource($task),
        ], 201);
    }

    /**
     * Ver tarea
     *
     * Muestra los detalles de una tarea específica.
     *
     * @authenticated
     *
     * @urlParam task int required ID de la tarea. Example: 1
     *
     * @response {
     *   "data": {
     *     "id": 1,
     *     "title": "Comprar víveres",
     *     "description": "Leche, pan, huevos",
     *     "status": "pending",
     *     "created_at": "2026-06-11T12:00:00.000000Z",
     *     "updated_at": "2026-06-11T12:00:00.000000Z"
     *   }
     * }
     *
     * @response 403 {
     *   "message": "No autorizado."
     * }
     */
    public function show(Task $task): TaskResource|JsonResponse
    {
        if ($task->user_id !== auth()->id()) {
            return response()->json(['message' => 'No autorizado.'], 403);
        }

        return new TaskResource($task);
    }

    /**
     * Actualizar tarea
     *
     * Actualiza los datos de una tarea existente.
     *
     * @authenticated
     *
     * @urlParam task int required ID de la tarea. Example: 1
     * @bodyParam title string Título de la tarea. Example: Comprar víveres actualizado
     * @bodyParam description string Descripción de la tarea. Example: Solo leche y pan
     * @bodyParam status string Estado: pending, in_progress, completed. Example: in_progress
     *
     * @response {
     *   "data": {
     *     "id": 1,
     *     "title": "Comprar víveres actualizado",
     *     "description": "Solo leche y pan",
     *     "status": "in_progress",
     *     "created_at": "2026-06-11T12:00:00.000000Z",
     *     "updated_at": "2026-06-11T12:00:00.000000Z"
     *   }
     * }
     */
    public function update(UpdateTaskRequest $request, Task $task): TaskResource|JsonResponse
    {
        if ($task->user_id !== auth()->id()) {
            return response()->json(['message' => 'No autorizado.'], 403);
        }

        $task->update($request->validated());

        return new TaskResource($task);
    }

    /**
     * Eliminar tarea
     *
     * Elimina una tarea del sistema.
     *
     * @authenticated
     *
     * @urlParam task int required ID de la tarea. Example: 1
     *
     * @response {
     *   "message": "Tarea eliminada exitosamente."
     * }
     */
    public function destroy(Task $task): JsonResponse
    {
        if ($task->user_id !== auth()->id()) {
            return response()->json(['message' => 'No autorizado.'], 403);
        }

        $task->delete();

        return response()->json(['message' => 'Tarea eliminada exitosamente.']);
    }
}
