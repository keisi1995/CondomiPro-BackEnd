<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Usuario;
use App\Http\Response\ApiResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Exception;

use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Facades\JWTFactory;
use Tymon\JWTAuth\Token;

class AutenticarController extends Controller
{
    /**
     * Realiza la autenticacion del usuario y retorna el token valido
    */
    public function autenticar(Request $request)
    {
        try {
            $paramet = $request->only('usuario', 'clave');
                        
            $validator = Validator::make($paramet, [
                'usuario' => 'required|string|min:5|max:20',
                'clave' => 'required|string|min:5|max:20'
            ]);

            if($validator->fails())  {
                return ApiResponse::error('Error de validación', 422, $validator->errors());
            }
            
            // if (!$token = JWTAuth::attempt($paramet)) {
                // return ApiResponse::error('Credenciales invalidas', 400, []);
                // throw new Exception('Invalid credentials');
            // }
            
            $usuario = Usuario::where('usuario' , '=', $request->usuario)->first();
            
            if (!$usuario) { 
                return ApiResponse::error('El usuario ingresado no existe', 401);
            }

            if (!Hash::check($request->clave, $usuario->clave)) {
                return ApiResponse::error('La clave es incorrecta', 401);
            }

            $access_token = JWTAuth::fromUser($usuario);
        
        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
            return ApiResponse::error('Excepción JWT: ' . $e->getMessage(), 500);
        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            return ApiResponse::error('Token no válido: ' . $e->getMessage(), 500);
        } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            return ApiResponse::error('Token expirado: ' . $e->getMessage(), 500);
        } catch (Exception $e) {
            return ApiResponse::error('Excepción: ' . $e->getMessage(), 500);
        }

        return ApiResponse::success('Usuario autenticado exitosamente', 200, ['access_token' => $access_token]);
    }

    /**
     * Realiza el logout
    */
    public function logout(Request $request)
    {
        try {
            $validator = Validator::make($request->only('access_token'), [
                'access_token' => 'required'
            ]);

            if($validator->fails()) { return ApiResponse::error('Error de validación', 422, $validator->errors()); }

            // Convierte el string a formato token
            $tokenString = $request->access_token;
            JWTAuth::setToken(new Token($tokenString));

            if (!JWTAuth::check()) { return ApiResponse::error('El token ya fue invalidado', 422, ['access_token' => $tokenString]); }
            
            JWTAuth::invalidate();    
        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
            return ApiResponse::error('Excepción JWT: ' . $e->getMessage(), 500);
        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            return ApiResponse::error('Token no válido: ' . $e->getMessage(), 500);
        } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            return ApiResponse::error('Token expirado: ' . $e->getMessage(), 500);
        } catch (Exception $e) {
            return ApiResponse::error('Excepción: ' . $e->getMessage(), 500);
        }

        return ApiResponse::success('Token invalidado exitosamente', 200, ['access_token' => $tokenString]);
    }

    /**
     * Realiza el refresh del token
    */
    public function refresh(Request $request)
    {
        try {
            $validator = Validator::make($request->only('access_token'), [
                'access_token' => 'required'
            ]);

            if($validator->fails()) { return ApiResponse::error('Error de validación', 422, $validator->errors()); }

            // Convierte el string a formato token
            $tokenString = $request->access_token;
            JWTAuth::setToken(new Token($tokenString));

            if (JWTAuth::check()) { return ApiResponse::success('El token aun esta vigente', 200, ['access_token' => $tokenString]); }
            
            // Convierte el string a formato token
            $access_token = JWTAuth::refresh();
        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
            return ApiResponse::error('Excepción JWT: ' . $e->getMessage(), 500);
        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            return ApiResponse::error('Token no válido: ' . $e->getMessage(), 500);
        } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            return ApiResponse::error('Token expirado: ' . $e->getMessage(), 500);
        } catch (Exception $e) {
            return ApiResponse::error('Excepción: ' . $e->getMessage(), 500);
        }
        
        return ApiResponse::success('Token actualizado exitosamente', 200, ['access_token' => $access_token]);
    }
    
    public function verificar_token(Request $request)
    {
        try {
            $validator = Validator::make($request->only('access_token'), [
                'access_token' => 'required'
            ]);

            if($validator->fails()) { return ApiResponse::error('Error de validación', 422, $validator->errors()); }

            // Convierte el string a formato token
            $tokenString = $request->access_token;
            JWTAuth::setToken(new Token($tokenString));
            
            if (!$data = JWTAuth::authenticate()) { return ApiResponse::error('Token error', 422, []); }
            
            $access_token = (string) JWTAuth::getToken();
            // $access_token = $request->header('Authorization');
            // $access_token = $request->bearerToken();
            
            // $data = JWTAuth::authenticate()->email;
        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
            return ApiResponse::error('Excepción JWT: ' . $e->getMessage(), 500);
        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            return ApiResponse::error('Token no válido: ' . $e->getMessage(), 500);
        } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            return ApiResponse::error('Token expirado: ' . $e->getMessage(), 500);
        } catch (Exception $e) {
            return ApiResponse::error('Excepción: ' . $e->getMessage(), 500);
        }

        return ApiResponse::success('Token activo', 200, ['access_token' => $access_token, 'data' => $data]);        
    }

    public function decode_token(Request $request)
    {
        try {
            // Realiza la validacion
            $validator = Validator::make($request->only('token'), [
                'token' => 'required'
            ]);

            if($validator->fails()) {
                return ApiResponse::error('Error de validación', 422, $validator->errors()); 
            }

            $tokenString = $request->token;
            
            // Convierte el string a formato token
            $token = new Token($tokenString);
        
            // Decodificar el token
            $decodedToken = JWTAuth::decode($token);

            // Acceder a las reclamaciones decodificadas
            $data = [
                'key1' => $decodedToken->get('key1'),
                'key2' => $decodedToken->get('key2'),
            ];
            
        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
            return ApiResponse::error('Excepción JWT: ' . $e->getMessage(), 500);
        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            return ApiResponse::error('Token no válido: ' . $e->getMessage(), 500);
        } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            return ApiResponse::error('Token expirado: ' . $e->getMessage(), 500);
        } catch (Exception $e) {
            return ApiResponse::error('Excepción: ' . $e->getMessage(), 500);
        }

        return ApiResponse::success('Token activo', 200, ['token' => $tokenString, 'data' => $data]);
    }

    public function new_token(Request $request)
    {
        try {
            // Generate a JWT token
            $user_id = 123;
            $payload = JWTFactory::sub( $user_id )->iat(time())
            ->customClaims([
                'key1' => 'value1',
                'key2' => 'value2',
            ])->make();

            // $payload = new Payload([
            //     'sub' => 123,
            //     'iat' => time()
            // ]);
            
            $token = (string) JWTAuth::encode($payload);        
        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
            return ApiResponse::error('Excepción JWT: ' . $e->getMessage(), 500);
        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            return ApiResponse::error('Token no válido: ' . $e->getMessage(), 500);
        } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            return ApiResponse::error('Token expirado: ' . $e->getMessage(), 500);
        } catch (Exception $e) {
            return ApiResponse::error('Excepción: ' . $e->getMessage(), 500);
        }

        return ApiResponse::success('Token generado exitosamente', 200, ['token' => $token]);
    }
    
}
