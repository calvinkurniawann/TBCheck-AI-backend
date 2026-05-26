<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Register a new user and return a Sanctum token.
     */
    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'jenis_kelamin' => ['nullable', 'string', 'in:Laki-laki,Perempuan'],
            'usia' => ['nullable', 'integer', 'min:1', 'max:120'],
            'tinggi_badan' => ['nullable', 'numeric', 'min:1'],
            'berat_badan' => ['nullable', 'numeric', 'min:1'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'jenis_kelamin' => $validated['jenis_kelamin'] ?? null,
            'usia' => $validated['usia'] ?? null,
            'tinggi_badan' => $validated['tinggi_badan'] ?? null,
            'berat_badan' => $validated['berat_badan'] ?? null,
        ]);

        $token = $user->createToken('mobile-app')->plainTextToken;

        return response()->json([
            'success' => true,
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'jenis_kelamin' => $user->jenis_kelamin,
                    'usia' => $user->usia,
                    'tinggi_badan' => $user->tinggi_badan,
                    'berat_badan' => $user->berat_badan,
                ],
                'token' => $token,
            ],
        ], 201);
    }

    /**
     * Login user and return a Sanctum token.
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Email atau password salah.'],
            ]);
        }

        // Revoke old tokens
        $user->tokens()->delete();

        $token = $user->createToken('mobile-app')->plainTextToken;

        return response()->json([
            'success' => true,
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'jenis_kelamin' => $user->jenis_kelamin,
                    'usia' => $user->usia,
                    'tinggi_badan' => $user->tinggi_badan,
                    'berat_badan' => $user->berat_badan,
                ],
                'token' => $token,
            ],
        ], 200);
    }

    /**
     * Logout: revoke current token.
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Berhasil logout.',
        ]);
    }

    /**
     * Get authenticated user profile.
     */
    public function profile(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'jenis_kelamin' => $user->jenis_kelamin,
                'usia' => $user->usia,
                'tinggi_badan' => $user->tinggi_badan,
                'berat_badan' => $user->berat_badan,
            ],
        ]);
    }

    /**
     * Update user profile.
     */
    public function updateProfile(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'jenis_kelamin' => ['sometimes', 'nullable', 'string', 'in:Laki-laki,Perempuan'],
            'usia' => ['sometimes', 'nullable', 'integer', 'min:1', 'max:120'],
            'tinggi_badan' => ['sometimes', 'nullable', 'numeric', 'min:1'],
            'berat_badan' => ['sometimes', 'nullable', 'numeric', 'min:1'],
        ]);

        $user = $request->user();
        $user->update($validated);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'jenis_kelamin' => $user->jenis_kelamin,
                'usia' => $user->usia,
                'tinggi_badan' => $user->tinggi_badan,
                'berat_badan' => $user->berat_badan,
            ],
        ]);
    }
}
