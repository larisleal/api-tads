<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Firebase\JWT\JWT;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\SignatureInvalidException;
use Firebase\JWT\JWK;

class JwtMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $token = $request->bearerToken();

        // Verifica se o token foi fornecido
        if (!$token) {
            return response()->json(['message' => 'Token não fornecido'], 401);
        }

        try {
            // Obtendo a chave pública do JWKS
            $jwks = json_decode(file_get_contents(env('KEYCLOAK_JWKS_URI')), true);
            $keys = $jwks['keys'];

            // Encontrar a chave correta para decodificar
            $publicKey = null;
            foreach ($keys as $key) {
                if ($key['kty'] === 'RSA') {
                    $publicKey = JWK::parseKey($key);
                    break;
                }
            }

            // Se a chave pública não for encontrada, retorna erro
            if (!$publicKey) {
                return response()->json(['message' => 'Chave pública não encontrada'], 500);
            }

            // Decodificando o token usando a chave pública correta
            $credentials = JWT::decode($token, $publicKey, ['RS256']);
        } catch (ExpiredException $e) {
            return response()->json(['message' => 'Token expirado'], 401);
        } catch (SignatureInvalidException $e) {
            return response()->json(['message' => 'Token inválido'], 401);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erro ao decifrar token'], 401);
        }

        // Armazenar as informações do usuário no request
        $request->auth = $credentials;

        // Prosegue com a requisição
        return $next($request);
    }
}
