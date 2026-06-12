<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Nueva Tarea</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-lg mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-xl shadow p-6">
                <form method="POST" action="{{ route('tasks.store') }}">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-medium mb-1">Título *</label>
                        <input type="text" name="title" value="{{ old('title') }}" required class="w-full border rounded-lg px-3 py-2">
                        @error('title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium mb-1">Descripción</label>
                        <textarea name="description" rows="4" class="w-full border rounded-lg px-3 py-2">{{ old('description') }}</textarea>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium mb-1">Estado</label>
                        <select name="status" class="w-full border rounded-lg px-3 py-2">
                            <option value="pending">Pendiente</option>
                            <option value="in_progress">En progreso</option>
                            <option value="completed">Completada</option>
                        </select>
                    </div>
                    <div class="flex justify-between">
                        <a href="{{ route('tasks.index') }}" class="text-gray-600 hover:text-gray-900">Cancelar</a>
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">Crear Tarea</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
