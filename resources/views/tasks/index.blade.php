<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Mis Tareas</h2>
        </div>
    </x-slot>

    <div class="py-12" x-data="{ modal: null }">
        <div class="max-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex justify-end mb-4">
                <button @click="modal = 'create'" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg text-sm flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Nueva Tarea
                </button>
            </div>

            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-4">{{ session('success') }}</div>
            @endif
            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-4">
                    <ul class="list-disc pl-5">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                </div>
            @endif

            @if ($tasks->isEmpty())
                <div class="bg-white rounded-xl shadow p-12 text-center">
                    <p class="text-gray-500 mb-4">No tienes tareas registradas</p>
                    <button @click="modal = 'create'" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">+ Nueva Tarea</button>
                </div>
            @else
                <div class="bg-white rounded-xl shadow overflow-hidden">
                    <table class="w-full">
                        <thead>
                            <tr class="bg-gray-50 text-left text-xs font-semibold text-gray-500 uppercase">
                                <th class="px-6 py-3">Título</th><th class="px-6 py-3">Estado</th><th class="px-6 py-3">Creada</th><th class="px-6 py-3 text-right">Acciones</th>
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
                                    <button @click="modal = 'edit-{{ $task->id }}'" class="text-blue-600 hover:text-blue-800 mr-3">Editar</button>
                                    <button @click="modal = 'delete-{{ $task->id }}'" class="text-red-600 hover:text-red-800">Eliminar</button>
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
        <template x-teleport="body">
            <div x-show="modal === 'create'" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
                <div class="fixed inset-0 bg-black/50" @click="modal = null"></div>
                <div class="relative bg-white rounded-xl shadow-2xl w-full max-w-lg p-6 z-10">
                    <h3 class="text-lg font-bold mb-4">Nueva Tarea</h3>
                    <form method="POST" action="{{ route('tasks.store') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="block text-sm font-medium mb-1">Título *</label>
                            <input type="text" name="title" value="{{ old('title') }}" required class="w-full border rounded-lg px-3 py-2 text-sm">
                            @error('title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="block text-sm font-medium mb-1">Descripción</label>
                            <textarea name="description" rows="3" class="w-full border rounded-lg px-3 py-2 text-sm">{{ old('description') }}</textarea>
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
                            <button type="button" @click="modal = null" class="px-4 py-2 text-sm text-gray-600 border rounded-lg hover:bg-gray-50">Cancelar</button>
                            <button type="submit" class="px-4 py-2 text-sm text-white bg-blue-600 rounded-lg hover:bg-blue-700">Crear Tarea</button>
                        </div>
                    </form>
                </div>
            </div>
        </template>

        {{-- MODALES EDITAR Y ELIMINAR --}}
        @foreach ($tasks as $task)
        <template x-teleport="body">
            <div x-show="modal === 'edit-{{ $task->id }}'" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
                <div class="fixed inset-0 bg-black/50" @click="modal = null"></div>
                <div class="relative bg-white rounded-xl shadow-2xl w-full max-w-lg p-6 z-10">
                    <h3 class="text-lg font-bold mb-4">Editar Tarea</h3>
                    <form method="POST" action="{{ route('tasks.update', $task) }}">
                        @csrf @method('PUT')
                        <div class="mb-3">
                            <label class="block text-sm font-medium mb-1">Título *</label>
                            <input type="text" name="title" value="{{ old('title', $task->title) }}" required class="w-full border rounded-lg px-3 py-2 text-sm">
                            @error('title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="block text-sm font-medium mb-1">Descripción</label>
                            <textarea name="description" rows="3" class="w-full border rounded-lg px-3 py-2 text-sm">{{ old('description', $task->description) }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label class="block text-sm font-medium mb-1">Estado</label>
                            <select name="status" class="w-full border rounded-lg px-3 py-2 text-sm">
                                <option value="pending" {{ $task->status=='pending'?'selected':'' }}>Pendiente</option>
                                <option value="in_progress" {{ $task->status=='in_progress'?'selected':'' }}>En progreso</option>
                                <option value="completed" {{ $task->status=='completed'?'selected':'' }}>Completada</option>
                            </select>
                        </div>
                        <div class="flex justify-end gap-2 mt-4">
                            <button type="button" @click="modal = null" class="px-4 py-2 text-sm text-gray-600 border rounded-lg hover:bg-gray-50">Cancelar</button>
                            <button type="submit" class="px-4 py-2 text-sm text-white bg-indigo-600 rounded-lg hover:bg-indigo-700">Guardar</button>
                        </div>
                    </form>
                </div>
            </div>

            <div x-show="modal === 'delete-{{ $task->id }}'" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
                <div class="fixed inset-0 bg-black/50" @click="modal = null"></div>
                <div class="relative bg-white rounded-xl shadow-2xl w-full max-w-sm p-6 z-10 text-center">
                    <p class="text-gray-700 mb-4">¿Eliminar "<strong>{{ $task->title }}</strong>"?</p>
                    <form method="POST" action="{{ route('tasks.destroy', $task) }}">
                        @csrf @method('DELETE')
                        <div class="flex justify-center gap-2">
                            <button type="button" @click="modal = null" class="px-4 py-2 text-sm text-gray-600 border rounded-lg hover:bg-gray-50">Cancelar</button>
                            <button type="submit" class="px-4 py-2 text-sm text-white bg-red-600 rounded-lg hover:bg-red-700">Eliminar</button>
                        </div>
                    </form>
                </div>
            </div>
        </template>
        @endforeach
    </div>
</x-app-layout>
