<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

/**
 * @group Autenticación
 *
 * Endpoints para registro, inicio de sesión y cierre de sesión.
 */
class AuthController extends Controller
{
    /**
     * Registro de usuario
     *
     * Crea un nuevo usuario y devuelve un token de acceso.
     *
     * @bodyParam name string required Nombre del usuario. Example: Juan Pérez
     * @bodyParam email string required Correo electrónico. Example: juan@mail.com
     * @bodyParam password string required Contraseña (mínimo 8 caracteres). Example: secret123
     * @bodyParam password_confirmation string required Confirmación de contraseña. Example: secret123
     *
     * @response 201 {
     *   "user": {
     *     "id": 1,
     *     "name": "Juan Pérez",
     *     "email": "juan@mail.com"
     *   },
     *   "token": "1|abc123def456..."
     * }
     */
    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
            'token' => $token,
        ], 201);
    }

    /**
     * Inicio de sesión
     *
     * Autentica un usuario y devuelve un token de acceso.
     *
     * @bodyParam email string required Correo electrónico. Example: juan@mail.com
     * @bodyParam password string required Contraseña. Example: secret123
     *
     * @response {
     *   "user": {
     *     "id": 1,
     *     "name": "Juan Pérez",
     *     "email": "juan@mail.com"
     *   },
     *   "token": "1|abc123def456..."
     * }
     *
     * @response 422 {
     *   "message": "Credenciales inválidas.",
     *   "errors": { "email": ["Credenciales inválidas."] }
     * }
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Credenciales inválidas.'],
            ]);
        }

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
            'token' => $token,
        ]);
    }

    /**
     * Cerrar sesión
     *
     * Revoca el token de acceso actual del usuario.
     *
     * @authenticated
     *
     * @response {
     *   "message": "Sesión cerrada exitosamente."
     * }
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Sesión cerrada exitosamente.']);
    }

    /**
     * Perfil del usuario
     *
     * Devuelve los datos del usuario autenticado.
     *
     * @authenticated
     *
     * @response {
     *   "user": {
     *     "id": 1,
     *     "name": "Juan Pérez",
     *     "email": "juan@mail.com"
     *   }
     * }
     */
    public function profile(Request $request): JsonResponse
    {
        return response()->json([
            'user' => [
                'id' => $request->user()->id,
                'name' => $request->user()->name,
                'email' => $request->user()->email,
            ],
        ]);
    }
}
