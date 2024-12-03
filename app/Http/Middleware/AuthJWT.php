<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Firebase\JWT\JWT;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\Key;
use GuzzleHttp\Client;
use Illuminate\Http\Response;

class AuthJWT
{
    private $publicKeys;

    public function handle(Request $request, Closure $next)
    {

        $token = $request->header('Authorization');
        if (!$token) {
            return response()->json(['error' => 'Token não fornecido'], 401);
        }

        try {
            $keys = $this->fetchPublicKeys();
            $publicKey = $keys[$this->getKidFromToken($token)];
            $decodedHeader = JWT::decode($token, new Key($publicKey, 'RS256'));

            if (!$decodedHeader) {
                return response()->json(['errors' => ['default' => 'Chave pública não encontrada para validar o token']], Response::HTTP_UNAUTHORIZED);
            }


            $jwtData = JWT::decode($token, new Key($publicKey, 'RS256'));

            if (!isset($jwtData->sub)) {
                return response()->json(['errors' => ['default' => 'Token inválido']], Response::HTTP_UNAUTHORIZED);
            }

            // Altera a chave de atributo para user_id
            $request->attributes->set('user_id', $jwtData->sub);
            return $next($request);
        } catch (ExpiredException $e) {
            return response()->json(['error' => 'Token expirado'], 401);
        } catch (\Exception $e) {
            return response()->json(['errors' => ['default' => 'Token inválido', 'details' => $e->getMessage()]], Response::HTTP_UNAUTHORIZED);
        }
    }

    private function fetchPublicKeys()
    {
        if (!$this->publicKeys) {
            $client = new Client();
            $response = $client->get('https://tdsoft-auth.hsborges.dev/realms/trabalho-pratico/protocol/openid-connect/certs');
            $data = json_decode($response->getBody()->getContents(), true);
            $this->publicKeys = array_reduce($data['keys'], function ($acc, $key) {
                $acc[$key['kid']] = "-----BEGIN CERTIFICATE-----\n{$key['x5c'][0]}\n-----END CERTIFICATE-----";
                return $acc;
            }, []);
        }
        return $this->publicKeys;
    }

    private function getKidFromToken($token)
    {
        $parts = explode('.', $token);
        if (count($parts) !== 3) {
            throw new \InvalidArgumentException('Token inválido');
        }

        $header = json_decode(base64_decode(str_replace('Bearer ', '', $parts[0])), true);
        return $header['kid'] ?? null;
    }
}
