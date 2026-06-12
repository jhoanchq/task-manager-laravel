<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Mis Tareas</h2>
        </div>
    </x-slot>

    <div class="py-12" x-data="{ modal: null }">
        <div class="max-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex justify-end mb-4">
                <button @click="modal = 'create'" class="bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 text-white font-bold py-2.5 px-5 rounded-lg shadow-md hover:shadow-lg transition-all duration-200 text-sm flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Nueva Tarea
                </button>
            </div>

            @if (session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 px-4 py-3 rounded-lg mb-4 flex items-center justify-between shadow-sm" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)">
                    <span>{{ session('success') }}</span>
                    <button @click="show = false" class="text-green-700 hover:text-green-900">&times;</button>
                </div>
            @endif

            @if ($tasks->isEmpty())
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-2xl p-12 text-center">
                    <div class="mx-auto w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                    </div>
                    <p class="text-lg text-gray-500 mb-2">No tienes tareas registradas</p>
                    <p class="text-sm text-gray-400 mb-6">Crea tu primera tarea para empezar</p>
                    <button @click="modal = 'create'" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition text-sm font-medium">+ Nueva Tarea</button>
                </div>
            @else
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-2xl">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr class="bg-gray-50">
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Título</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Estado</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Creada</th>
                                    <th class="px-6 py-4 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-100">
                                @foreach ($tasks as $task)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $task->title }}</div>
                                        @if($task->description)
                                            <div class="text-xs text-gray-400 mt-0.5 truncate max-w-xs">{{ Str::limit($task->description, 60) }}</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $colors = ['pending' => 'bg-yellow-50 text-yellow-700 border-yellow-200', 'in_progress' => 'bg-blue-50 text-blue-700 border-blue-200', 'completed' => 'bg-green-50 text-green-700 border-green-200'];
                                            $labels = ['pending' => 'Pendiente', 'in_progress' => 'En progreso', 'completed' => 'Completada'];
                                        @endphp
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full border {{ $colors[$task->status] }}">
                                            {{ $labels[$task->status] }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400">{{ $task->created_at->diffForHumans() }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <button @click="modal = 'edit-{{ $task->id }}'" class="text-indigo-600 hover:text-indigo-900 mr-3">
                                            <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                            Editar
                                        </button>
                                        <button @click="modal = 'delete-{{ $task->id }}'" class="text-red-500 hover:text-red-700">
                                            <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            Eliminar
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="mt-6">{{ $tasks->links() }}</div>
            @endif
        </div>

        {{-- MODAL Crear --}}
        <div x-show="modal === 'create'" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="fixed inset-0 bg-black/50" @click="modal = null"></div>
            <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg p-6 z-10">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold text-gray-900">Nueva Tarea</h3>
                    <button @click="modal = null" class="text-2xl text-gray-400 hover:text-gray-600 leading-none">&times;</button>
                </div>
                <form action="{{ route('tasks.store') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Título *</label>
                        <input type="text" name="title" required class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500" placeholder="¿Qué necesitas hacer?">
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Descripción</label>
                        <textarea name="description" rows="3" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500" placeholder="Detalles de la tarea..."></textarea>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Estado</label>
                        <select name="status" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="pending">Pendiente</option>
                            <option value="in_progress">En progreso</option>
                            <option value="completed">Completada</option>
                        </select>
                    </div>
                    <div class="flex justify-end gap-3 mt-6">
                        <button type="button" @click="modal = null" class="px-4 py-2 text-sm text-gray-600 hover:text-gray-900 font-medium">Cancelar</button>
                        <button type="submit" class="px-5 py-2 bg-gradient-to-r from-blue-500 to-indigo-600 text-white text-sm font-semibold rounded-lg hover:from-blue-600 hover:to-indigo-700 shadow-md">Crear Tarea</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- MODALES Editar y Eliminar --}}
        @foreach ($tasks as $task)
        <div x-show="modal === 'edit-{{ $task->id }}'" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="fixed inset-0 bg-black/50" @click="modal = null"></div>
            <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg p-6 z-10">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold text-gray-900">Editar Tarea</h3>
                    <button @click="modal = null" class="text-2xl text-gray-400 hover:text-gray-600 leading-none">&times;</button>
                </div>
                <form action="{{ route('tasks.update', $task) }}" method="POST">
                    @csrf @method('PUT')
                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Título *</label>
                        <input type="text" name="title" value="{{ $task->title }}" required class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Descripción</label>
                        <textarea name="description" rows="3" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">{{ $task->description }}</textarea>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Estado</label>
                        <select name="status" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="pending" {{ $task->status == 'pending' ? 'selected' : '' }}>Pendiente</option>
                            <option value="in_progress" {{ $task->status == 'in_progress' ? 'selected' : '' }}>En progreso</option>
                            <option value="completed" {{ $task->status == 'completed' ? 'selected' : '' }}>Completada</option>
                        </select>
                    </div>
                    <div class="flex justify-end gap-3 mt-6">
                        <button type="button" @click="modal = null" class="px-4 py-2 text-sm text-gray-600 hover:text-gray-900 font-medium">Cancelar</button>
                        <button type="submit" class="px-5 py-2 bg-gradient-to-r from-indigo-500 to-purple-600 text-white text-sm font-semibold rounded-lg hover:from-indigo-600 hover:to-purple-700 shadow-md">Guardar Cambios</button>
                    </div>
                </form>
            </div>
        </div>

        <div x-show="modal === 'delete-{{ $task->id }}'" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="fixed inset-0 bg-black/50" @click="modal = null"></div>
            <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md p-6 z-10 text-center">
                <div class="mx-auto w-14 h-14 bg-red-100 rounded-full flex items-center justify-center mb-4">
                    <svg class="w-7 h-7 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path></svg>
                </div>
                <h3 class="text-lg font-bold text-gray-900 mb-2">¿Eliminar tarea?</h3>
                <p class="text-sm text-gray-500 mb-6">"{{ $task->title }}" se eliminará permanentemente.</p>
                <form action="{{ route('tasks.destroy', $task) }}" method="POST">
                    @csrf @method('DELETE')
                    <div class="flex justify-center gap-3">
                        <button type="button" @click="modal = null" class="px-5 py-2 text-sm text-gray-600 hover:text-gray-900 font-medium border border-gray-300 rounded-lg hover:bg-gray-50">Cancelar</button>
                        <button type="submit" class="px-5 py-2 bg-red-600 text-white text-sm font-semibold rounded-lg hover:bg-red-700 shadow-md">Eliminar</button>
                    </div>
                </form>
            </div>
        </div>
        @endforeach
    </div>

    <style>[x-cloak] { display: none !important; }</style>
</x-app-layout>
