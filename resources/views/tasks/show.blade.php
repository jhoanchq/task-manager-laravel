<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $task->title }}</h2>
            <div class="flex gap-2">
                <a href="{{ route('tasks.edit', $task) }}" class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded text-sm">Editar</a>
                <a href="{{ route('tasks.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded text-sm">Volver</a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">{{ session('success') }}</div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                @php
                    $colors = ['pending' => 'bg-yellow-100 text-yellow-800', 'in_progress' => 'bg-blue-100 text-blue-800', 'completed' => 'bg-green-100 text-green-800'];
                    $labels = ['pending' => 'Pendiente', 'in_progress' => 'En progreso', 'completed' => 'Completada'];
                @endphp

                <div class="mb-4">
                    <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full {{ $colors[$task->status] }}">
                        {{ $labels[$task->status] }}
                    </span>
                </div>

                <div class="mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Descripción</h3>
                    <p class="text-gray-700">{{ $task->description ?: 'Sin descripción.' }}</p>
                </div>

                <div class="border-t pt-4 text-sm text-gray-500">
                    <p>Creada: {{ $task->created_at->format('d/m/Y h:i A') }}</p>
                    <p>Actualizada: {{ $task->updated_at->format('d/m/Y h:i A') }}</p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
