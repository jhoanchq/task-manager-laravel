<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Mis Tareas</h2>
        </div>
    </x-slot>

    @if (session('success'))
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mt-6">
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg" x-data="{ show: true }" x-show="show">
                {{ session('success') }}
                <button @click="show = false" class="float-right font-bold">&times;</button>
            </div>
        </div>
    @endif

    <div class="py-6" x-data="{ modal: null, taskId: null }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <button @click="modal = 'create'" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium py-2 px-4 rounded-lg flex items-center gap-1 mb-4">
                + Nueva Tarea
            </button>

            @if ($tasks->isEmpty())
                <div class="bg-white rounded-xl shadow p-12 text-center">
                    <p class="text-gray-500 mb-4">No tienes tareas registradas</p>
                    <button @click="modal = 'create'" class="bg-blue-600 text-white px-4 py-2 rounded-lg">+ Nueva Tarea</button>
                </div>
            @else
                <div class="bg-white rounded-xl shadow overflow-hidden">
                    <table class="w-full">
                        <thead>
                            <tr class="bg-gray-50 text-left text-xs font-semibold text-gray-500 uppercase">
                                <th class="px-6 py-3">Título</th>
                                <th class="px-6 py-3">Estado</th>
                                <th class="px-6 py-3">Creada</th>
                                <th class="px-6 py-3 text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @foreach ($tasks as $task)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-sm font-medium">{{ $task->title }}</td>
                                <td class="px-6 py-4">
                                    @php $s = ['pending'=>['bg-yellow-100 text-yellow-800','Pendiente'],'in_progress'=>['bg-blue-100 text-blue-800','En progreso'],'completed'=>['bg-green-100 text-green-800','Completada']] @endphp
                                    <span class="px-2 py-1 text-xs rounded-full font-medium {{ $s[$task->status][0] }}">{{ $s[$task->status][1] }}</span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-400">{{ $task->created_at->diffForHumans() }}</td>
                                <td class="px-6 py-4 text-right text-sm">
                                    <button @click="modal = 'edit'; taskId = {{ $task->id }}; $nextTick(() => { document.getElementById('edit-title-{{ $task->id }}').value = '{{ addslashes($task->title) }}'; document.getElementById('edit-desc-{{ $task->id }}').value = '{{ addslashes($task->description ?? '') }}'; document.getElementById('edit-status-{{ $task->id }}').value = '{{ $task->status }}' })" class="text-blue-600 hover:text-blue-800 mr-3">Editar</button>
                                    <button @click="modal = 'delete'; taskId = {{ $task->id }}" class="text-red-600 hover:text-red-800">Eliminar</button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">{{ $tasks->links() }}</div>
            @endif
        </div>

        {{-- MODAL CREAR --}}
        <div x-show="modal === 'create'" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="fixed inset-0 bg-black/50" @click="modal = null"></div>
            <div class="relative bg-white rounded-xl shadow-2xl w-full max-w-lg p-6 z-10">
                <h3 class="text-lg font-bold mb-4">Nueva Tarea</h3>
                <form method="POST" action="/tasks">
                    @csrf
                    <div class="mb-3">
                        <label class="block text-sm font-medium mb-1">Título *</label>
                        <input type="text" name="title" required class="w-full border rounded-lg px-3 py-2 text-sm">
                    </div>
                    <div class="mb-3">
                        <label class="block text-sm font-medium mb-1">Descripción</label>
                        <textarea name="description" rows="3" class="w-full border rounded-lg px-3 py-2 text-sm"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="block text-sm font-medium mb-1">Estado</label>
                        <select name="status" class="w-full border rounded-lg px-3 py-2 text-sm">
                            <option value="pending">Pendiente</option>
                            <option value="in_progress">En progreso</option>
                            <option value="completed">Completada</option>
                        </select>
                    </div>
                    <div class="flex justify-end gap-2 mt-4">
                        <button type="button" @click="modal = null" class="px-4 py-2 text-sm text-gray-600 border rounded-lg">Cancelar</button>
                        <button type="submit" class="px-4 py-2 text-sm text-white bg-blue-600 rounded-lg">Crear Tarea</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- MODAL EDITAR --}}
        <div x-show="modal === 'edit'" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="fixed inset-0 bg-black/50" @click="modal = null"></div>
            <div class="relative bg-white rounded-xl shadow-2xl w-full max-w-lg p-6 z-10">
                <h3 class="text-lg font-bold mb-4">Editar Tarea</h3>
                <form method="POST" action="/tasks/" :action="'/tasks/' + taskId">
                    @csrf @method('PUT')
                    <div class="mb-3">
                        <label class="block text-sm font-medium mb-1">Título *</label>
                        <input type="text" name="title" id="edit-title-0" required class="w-full border rounded-lg px-3 py-2 text-sm">
                    </div>
                    <div class="mb-3">
                        <label class="block text-sm font-medium mb-1">Descripción</label>
                        <textarea name="description" id="edit-desc-0" rows="3" class="w-full border rounded-lg px-3 py-2 text-sm"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="block text-sm font-medium mb-1">Estado</label>
                        <select name="status" id="edit-status-0" class="w-full border rounded-lg px-3 py-2 text-sm">
                            <option value="pending">Pendiente</option>
                            <option value="in_progress">En progreso</option>
                            <option value="completed">Completada</option>
                        </select>
                    </div>
                    <div class="flex justify-end gap-2 mt-4">
                        <button type="button" @click="modal = null" class="px-4 py-2 text-sm text-gray-600 border rounded-lg">Cancelar</button>
                        <button type="submit" class="px-4 py-2 text-sm text-white bg-indigo-600 rounded-lg">Guardar</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- MODAL ELIMINAR --}}
        <div x-show="modal === 'delete'" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="fixed inset-0 bg-black/50" @click="modal = null"></div>
            <div class="relative bg-white rounded-xl shadow-2xl w-full max-w-sm p-6 z-10 text-center">
                <p class="text-gray-700 mb-4">¿Eliminar esta tarea?</p>
                <form method="POST" action="/tasks/" :action="'/tasks/' + taskId">
                    @csrf @method('DELETE')
                    <div class="flex justify-center gap-2">
                        <button type="button" @click="modal = null" class="px-4 py-2 text-sm text-gray-600 border rounded-lg">Cancelar</button>
                        <button type="submit" class="px-4 py-2 text-sm text-white bg-red-600 rounded-lg">Eliminar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <style>[x-cloak] { display: none !important; }</style>
</x-app-layout>
