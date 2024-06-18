<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Usuario;
use App\Models\Persona;
use App\Models\Permiso;
use App\Http\Response\ApiResponse;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;


class UsuarioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $usuario = Usuario::select(
            'usuario.*', 
            DB::raw("IF(usuario.flag_activo = 1, 'Activo', 'Inactivo') AS estado"),
            DB::raw("CONCAT(persona.nombres, ', ', persona.apellidos) AS nombre_completo"),
            DB::raw("CONCAT(persona.nombres, ', ', persona.apellidos, ' - ', persona.nro_documento) AS nombre_completo_nro_documento"),
            'persona.nombres AS nombres',
            'persona.apellidos AS apellidos',
            'persona.nro_documento AS nro_documento',
            'tipo_usuario.descripcion AS tipo_usuario',
            'tipo_documento.descripcion AS tipo_documento')
        ->join('persona', 'persona.id_persona', '=', 'usuario.id_persona')
        ->join('tipo_usuario', 'tipo_usuario.id_tipo_usuario', '=', 'usuario.id_tipo_usuario')
        ->join('tipo_documento', 'tipo_documento.id_tipo_documento', '=', 'persona.id_tipo_documento');
        
        if ($request->id_persona) {
            $usuario = $usuario->where('usuario.id_persona', '=', $request->id_persona);
        }

        $usuario = $usuario->get();

        return ApiResponse::success('Listado', Response::HTTP_OK, $usuario);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [            
            'id_persona' => 'required|numeric|gt:0|unique:usuario',
            'correo' => 'required|email|max:50|unique:usuario',
            'clave' => 'required|string|min:5|max:20|confirmed',
            'id_tipo_usuario' => 'required|numeric|gt:0'
        ], getMessageApi());
        
        $validator->setAttributeNames([            
            'id_persona' => 'persona',
        ]);

        if ($validator->fails()) {
            return ApiResponse::error('Error de validación', Response::HTTP_UNPROCESSABLE_ENTITY, $validator->errors());
        }

        DB::beginTransaction();
            $persona = Persona::findOrFail($request->id_persona);
            $usuario = Usuario::create($request->all());

            if (isset($request->permiso) && count($request->permiso) > 0) {
                $dataPermiso = [];
                foreach ($request->permiso as $key => $value) {
                    $dataPermiso[] = [
                        'id_usuario' => $usuario->id_usuario,
                        'id_menu' => $value,
                        'created_at' => now(),
                        'updated_at'=> now()
                    ];
                }
                Permiso::insert($dataPermiso);
            }
        DB::commit();

        return ApiResponse::success('Se registro exitosamente', Response::HTTP_CREATED, $usuario);
    }
    
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $usuario = Usuario::findOrFail($id);
        $persona = Persona::findOrFail($request->id_persona);

        $reglasValidacion = [
            'id_persona' => 'required|numeric|gt:0',
            'correo' => [
                'required', 'email', 'max:50', \Illuminate\Validation\Rule::unique('usuario')->ignore($usuario)
            ],
            'id_tipo_usuario' => 'required|numeric|gt:0'
        ];


        if (strlen($request->clave) > 0) {
            $reglasValidacion['clave'] = 'required|string|min:5|max:20|confirmed';
        }

        $validator = Validator::make($request->all(), $reglasValidacion, getMessageApi());

        $validator->setAttributeNames([
            'id_persona' => 'persona',
        ]);
 
        if($validator->fails()) {
            return ApiResponse::error('Error de validacion ', Response::HTTP_UNPROCESSABLE_ENTITY, $validator->errors());
        }

        DB::beginTransaction();
            $dataUpdate = [
                'correo' => $request->correo,
                'id_tipo_usuario' => $request->id_tipo_usuario
            ];
            
            if (strlen($request->clave) > 0) {
                $dataUpdate['clave'] = $request->clave;
            }

            $usuario->update($dataUpdate);
            Permiso::where('id_usuario', '=', $usuario->id_usuario)->delete();

            if (isset($request->permiso) && count($request->permiso) > 0) {
                $dataPermiso = [];
                foreach ($request->permiso as $key => $value) {
                    $dataPermiso[] = [
                        'id_usuario' => $usuario->id_usuario,
                        'id_menu' => $value,
                        'created_at' => now(),
                        'updated_at'=> now()
                    ];
                }
                Permiso::insert($dataPermiso);
            }
        DB::commit();
        
        return ApiResponse::success('Se actualizo exitosamente', Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $usuario = Usuario::findOrFail($id);
        $usuario->delete();
        return ApiResponse::success('Se elimino exitosamente', Response::HTTP_OK);
    }

}
