<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Usuario;
use App\Models\Persona;
use App\Http\Response\ApiResponse;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Facades\JWTFactory;
use Tymon\JWTAuth\Token;
use Exception;

class AutenticarController extends Controller
{
    /**
        * Realiza la autenticacion del usuario y retorna el token
    */
    public function autenticar(Request $request)
    {
        try {
            $paramet = $request->only('correo', 'clave');
                                 
            $validator = Validator::make($paramet, [
                'correo' => 'required|string|min:5|max:50',
                'clave' => 'required|string|min:5|max:20'
            ], getMessageApi());

            if($validator->fails())  { return ApiResponse::error('Error de validación', Response::HTTP_UNPROCESSABLE_ENTITY, $validator->errors()); }
            
            $paramet = (object) $paramet;
            // if (!$token = JWTAuth::attempt($paramet)) {
                // return ApiResponse::error('Credenciales invalidas', 400, []);
                // throw new Exception('Invalid credentials');
            // }
            
            $usuario = Usuario::where('correo' , '=', $paramet->correo)->first();
           
            if (!$usuario) { return ApiResponse::error('El usuario ingresado no existe', Response::HTTP_UNAUTHORIZED); }
            if (!Hash::check($paramet->clave, $usuario->clave)) { return ApiResponse::error('La clave es incorrecta', Response::HTTP_UNAUTHORIZED); }
            if ($usuario->flag_activo == 0) { return ApiResponse::error('El usuario se encuentra desactivado', Response::HTTP_UNAUTHORIZED); }
            
            $access_token = $this->generateToken($usuario);

        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
            return ApiResponse::error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            return ApiResponse::error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            return ApiResponse::error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        } catch (Exception $e) {
            return ApiResponse::error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }

        return ApiResponse::success('Usuario autenticado exitosamente', Response::HTTP_OK, ['access_token' => $access_token]);
    }

    /**
        * Genera el token
    */
    public function generateToken($usuario) {
        $persona = Persona::where('id_persona', '=', $usuario->id_persona)->first();

        $customData['data'] = [
            'id_usuario' => $usuario->id_usuario,
            'correo' => $usuario->correo,
            'nombre_completo' => $persona->nombres . ', '. $persona->apellidos
        ];
        
        $access_token = (string) JWTAuth::encode( JWTFactory::sub($usuario->id_usuario)->customClaims($customData)->make() );
        // $access_token = (string) JWTAuth::fromUser($usuario, $customData);
        return $access_token;
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

            if($validator->fails()) { return ApiResponse::error('Error de validación', Response::HTTP_UNPROCESSABLE_ENTITY, $validator->errors()); }

            // Convierte el string a formato token
            JWTAuth::setToken(new Token($request->access_token));

            if (!JWTAuth::check()) { return ApiResponse::error('El token ya se encuentra invalidado', Response::HTTP_UNPROCESSABLE_ENTITY); }
            
            JWTAuth::invalidate();    
        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
            return ApiResponse::error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            return ApiResponse::error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            return ApiResponse::error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        } catch (Exception $e) {
            return ApiResponse::error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }

        return ApiResponse::success('Token invalidado exitosamente', Response::HTTP_OK);
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

            if($validator->fails()) { return ApiResponse::error('Error de validación', Response::HTTP_UNPROCESSABLE_ENTITY, $validator->errors()); }

            // Convierte el string a formato token
            JWTAuth::setToken(new Token($request->access_token));

            if (JWTAuth::check()) { return ApiResponse::success('El token aun esta vigente', Response::HTTP_UNPROCESSABLE_ENTITY); }
            
            // refresca el token
            $access_token = JWTAuth::refresh();
        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
            return ApiResponse::error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            return ApiResponse::error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            return ApiResponse::error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        } catch (Exception $e) {
            return ApiResponse::error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
        
        return ApiResponse::success('Token actualizado exitosamente', Response::HTTP_OK, ['access_token' => $access_token]);
    }
    
    /**
        * Verifica el token
    */
    public function verificar_token(Request $request)
    {
        try {
            // $access_token = $request->header('Authorization');
            // $access_token = $request->bearerToken();

            $validator = Validator::make($request->only('access_token'), [
                'access_token' => 'required'
            ]);

            if($validator->fails()) { return ApiResponse::error('Error de validación', Response::HTTP_UNPROCESSABLE_ENTITY, $validator->errors()); }

            // Convierte el string a formato token
            $tokenString = $request->access_token;
            JWTAuth::setToken(new Token($tokenString));
            
            if (!$data = JWTAuth::authenticate()) { return ApiResponse::error('Token error', Response::HTTP_UNPROCESSABLE_ENTITY); }
            
            $access_token = (string) JWTAuth::getToken();
        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
            return ApiResponse::error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            return ApiResponse::error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            return ApiResponse::error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        } catch (Exception $e) {
            return ApiResponse::error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }

        return ApiResponse::success('Token activo', Response::HTTP_OK, ['access_token' => $access_token, 'data' => $data]);        
    }

    /**
        * Decodifica el token
    */
    public function decode_token(Request $request)
    {
        try {
            $validator = Validator::make($request->only('access_token'), [
                'access_token' => 'required'
            ]);

            if($validator->fails()) { return ApiResponse::error('Error de validación', Response::HTTP_UNPROCESSABLE_ENTITY, $validator->errors()); }

            // Decodificar el token
            $access_token = $request->access_token;
            $decodedToken = JWTAuth::decode(new Token($access_token));
                        
            // Acceder a las propiedades del token
            $data = [
                'data' => $decodedToken->get('data'),
            ];
        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
            return ApiResponse::error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            return ApiResponse::error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            return ApiResponse::error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        } catch (Exception $e) {
            return ApiResponse::error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }

        return ApiResponse::success('Token activo', Response::HTTP_OK, ['access_token' => $access_token, 'data' => $data]);
    }
}
