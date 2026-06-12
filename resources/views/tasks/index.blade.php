<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Mis Tareas</h2>
            <a href="{{ route('tasks.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium py-2 px-4 rounded-lg flex items-center gap-1">
                + Nueva Tarea
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-4">{{ session('success') }}</div>
            @endif

            @if ($tasks->isEmpty())
                <div class="bg-white rounded-xl shadow p-12 text-center">
                    <p class="text-gray-500 mb-4">No tienes tareas registradas</p>
                    <a href="{{ route('tasks.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 inline-block">+ Nueva Tarea</a>
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
                                    @php
                                        $s = ['pending'=>['bg-yellow-100 text-yellow-800','Pendiente'],'in_progress'=>['bg-blue-100 text-blue-800','En progreso'],'completed'=>['bg-green-100 text-green-800','Completada']];
                                    @endphp
                                    <span class="px-2 py-1 text-xs rounded-full font-medium {{ $s[$task->status][0] }}">{{ $s[$task->status][1] }}</span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-400">{{ $task->created_at->diffForHumans() }}</td>
                                <td class="px-6 py-4 text-right text-sm">
                                    <a href="{{ route('tasks.edit', $task) }}" class="text-blue-600 hover:text-blue-800 mr-3">Editar</a>
                                    <form method="POST" action="{{ route('tasks.destroy', $task) }}" class="inline" onsubmit="return confirm('¿Eliminar esta tarea?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800">Eliminar</button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">{{ $tasks->links() }}</div>
            @endif
        </div>
    </div>
</x-app-layout>
