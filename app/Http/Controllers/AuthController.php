<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Firebase\JWT\JWT;

/**
 * @OA\Tag(
 *     name="Autenticação",
 *     description="Operações de autenticação"
 * )
 */
class AuthController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/login",
     *     summary="Login do usuário",
     *     tags={"Autenticação"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email", "password"},
     *             @OA\Property(property="email", type="string", format="email", example="user@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="password123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Login realizado com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="token", type="string", example="eyJhbGciOiJIUzI1...")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Credenciais inválidas")
     * )
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        // Implementação da autenticação com Keycloak
        $response = Http::asForm()->post(env('KEYCLOAK_TOKEN_ENDPOINT'), [
            'client_id' => env('KEYCLOAK_CLIENT_ID'),
            'username' => $request->email,
            'password' => $request->password,
            'grant_type' => 'password',
        ]);

        if ($response->failed()) {
            return response()->json(['message' => 'Credenciais inválidas'], 401);
        }

        $data = $response->json();

        // Gerar um novo token JWT
        $token = JWT::encode(
            [
                'sub' => $data['sub'],
                'iat' => time(),
                'exp' => time() + 3600 // O token é válido por 1 hora
            ],
            env('JWT_SECRET'),
            'HS256'
        );

        return response()->json(['token' => $token], 200);
    }
}
