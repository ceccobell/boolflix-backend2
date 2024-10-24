<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Registra un nuovo utente.
     */
    public function register(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
        ]);

        // Genera un token personale
        $token = $user->createToken('auth_token')->plainTextToken;

        // Restituisci il token nella risposta
        return response()->json([
            'message' => 'User registered successfully',
            'token' => $token, // Aggiungi il token alla risposta
        ], 201);
    }


    /**
     * Effettua il login dell'utente.
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Le credenziali fornite non sono corrette.'],
            ]);
        }

        // Genera un token personale
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
        ], 200);
    }

    /**
     * Effettua il logout dell'utente.
     */
    public function logout(Request $request)
    {
        // Elimina tutti i token per l'utente autenticato
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'Logout effettuato con successo.',
        ]);
    }
}
