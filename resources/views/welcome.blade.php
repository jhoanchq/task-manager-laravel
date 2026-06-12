<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Task Manager') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gradient-to-br from-gray-50 via-white to-blue-50 min-h-screen">
    <div class="relative">
        @if (Route::has('login'))
            <nav class="flex items-center justify-end gap-4 p-6">
                @auth
                    <a href="{{ url('/tasks') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition text-sm font-medium">Ir a Mis Tareas</a>
                @else
                    <a href="{{ route('login') }}" class="text-gray-600 hover:text-gray-900 text-sm font-medium">Iniciar Sesión</a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition text-sm font-medium">Registrarse</a>
                    @endif
                @endauth
            </nav>
        @endif

        <main class="max-w-4xl mx-auto px-6 py-16 text-center">
            <div class="mx-auto w-20 h-20 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-3xl flex items-center justify-center shadow-xl mb-8">
                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                </svg>
            </div>

            <h1 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">Task Manager</h1>
            <p class="text-lg text-gray-600 mb-8 max-w-xl mx-auto">Aplicación web para gestionar tareas con interfaz gráfica y <strong>API REST</strong> con autenticación.</p>

            <div class="flex flex-wrap justify-center gap-4 mb-12">
                <a href="{{ route('login') }}" class="inline-flex items-center px-6 py-3 bg-indigo-600 text-white font-semibold rounded-xl shadow-lg hover:bg-indigo-700 hover:shadow-xl transition-all duration-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path></svg>
                    Iniciar Sesión
                </a>
                <a href="{{ route('register') }}" class="inline-flex items-center px-6 py-3 bg-white text-indigo-600 font-semibold rounded-xl shadow-lg border-2 border-indigo-600 hover:bg-indigo-50 transition-all duration-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                    Registrarse
                </a>
            </div>

            <div class="grid md:grid-cols-3 gap-6 text-left">
                <div class="bg-white p-6 rounded-xl shadow-md">
                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mb-3">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path></svg>
                    </div>
                    <h3 class="font-semibold text-gray-900 mb-1">CRUD Web</h3>
                    <p class="text-sm text-gray-500">Gestiona tus tareas desde una interfaz web moderna con Blade + Tailwind.</p>
                </div>
                <div class="bg-white p-6 rounded-xl shadow-md">
                    <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mb-3">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M12 5l7 7-7 7"></path></svg>
                    </div>
                    <h3 class="font-semibold text-gray-900 mb-1">API REST</h3>
                    <p class="text-sm text-gray-500">API protegida con Sanctum. Endpoints para integración con otras apps.</p>
                </div>
                <div class="bg-white p-6 rounded-xl shadow-md">
                    <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center mb-3">
                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                    </div>
                    <h3 class="font-semibold text-gray-900 mb-1">Segura</h3>
                    <p class="text-sm text-gray-500">Autenticación con Laravel Breeze + tokens Bearer para la API.</p>
                </div>
            </div>

            <div class="mt-12 pt-6 border-t border-gray-200">
                <p class="text-xs text-gray-400">Implementación de Plataformas Web — Semana 10 &mdash; IESTP "Jorge Basadre"</p>
            </div>
        </main>
    </div>
</body>
</html>
