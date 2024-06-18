<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Socio;
use App\Models\Persona;
use App\Models\Usuario;
use App\Models\Permiso;
use App\Http\Response\ApiResponse;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class SocioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // ->join('persona', function ($join) {
        //     $join->on('persona.id_persona', '=', 'socio.id_persona')->whereNull('persona.deleted_at');
        // })
        
        $socio = Socio::select(
            'socio.*',
            'persona.*',
            'distrito.*',
            DB::raw("CONCAT(persona.nombres, ', ', persona.apellidos) AS nombre_completo"),
            DB::raw("CONCAT(persona.nombres, ', ', persona.apellidos, ' - ', persona.nro_documento) AS nombre_completo_nro_documento"),
            'tipo_documento.descripcion AS tipo_documento',
            'tipo_persona.descripcion AS tipo_persona',
            'departamento.descripcion AS departamento',
            'provincia.descripcion AS provincia',
            'distrito.descripcion AS distrito')
        ->join('usuario', 'usuario.id_usuario', '=', 'socio.id_usuario')
        ->join('persona', 'persona.id_persona', '=', 'usuario.id_persona')
        ->join('tipo_documento', 'tipo_documento.id_tipo_documento', '=', 'persona.id_tipo_documento')
        ->join('tipo_persona', 'tipo_persona.id_tipo_persona', '=', 'persona.id_tipo_persona')
        ->join('distrito', 'distrito.id_distrito', '=', 'persona.id_distrito')
        ->join('provincia', 'provincia.id_provincia', '=', 'distrito.id_provincia')
        ->join('departamento', 'departamento.id_departamento', '=', 'distrito.id_departamento')
        ->get();
           
        return ApiResponse::success('ok', Response::HTTP_OK, $socio);
    }
    
    /**
        * Store a newly created resource in storage.
    */
    public function store(Request $request)
    {   
        $reglaValidator = [
            'id_persona' => 'required|numeric|gt:0|exists:persona,id_persona',
        ];

        $id_persona = $request->id_persona;
        $usuario = Usuario::all()->where('id_persona', $id_persona)->first();
       
        if (!$usuario) {
            $reglaValidator['correo'] = 'required|email|max:50|unique:usuario';
        } else {
            $socio = Socio::all()->where('id_usuario', $usuario->id_usuario)->first();            
            if ($socio) {
                return ApiResponse::error('Error de validación', Response::HTTP_UNPROCESSABLE_ENTITY, [
                    'id_persona' => ['El campo persona ya se encuentra registrado.']
                ]);
            }
        }
                
        $validator = Validator::make($request->all(), $reglaValidator, getMessageApi());

        $validator->setAttributeNames(['id_persona' => 'persona']);
        
        if($validator->fails()) {
            return ApiResponse::error('Error de validación', Response::HTTP_UNPROCESSABLE_ENTITY, $validator->errors());
        }
                                                    
        $sentEmail = false;
        $claveRandom = generarClaveRandom();
        
        DB::beginTransaction();            
            if (!$usuario) {
                $usuario = Usuario::create([
                    'correo' => $request->correo,
                    'clave' => $claveRandom,
                    'id_persona' => $id_persona,
                    'id_tipo_usuario' => 2
                ]);

                $id_usuario = $usuario->id_usuario;
                
                // registra los permisos para el socio
                $dataPersonaSocio = [2, 20];
                $dataPermiso = [];
                foreach ($dataPersonaSocio as $key => $value) {
                    $dataPermiso[] = [
                        'id_usuario' => $id_usuario,
                        'id_menu' => $value,
                        'created_at' => now(),
                        'updated_at'=> now()
                    ];
                }
                Permiso::insert($dataPermiso);

                $sentEmail = true;
            }

            $socio = Socio::create(['id_usuario' => $id_usuario]);
        DB::commit();

        if ($sentEmail) {
            $html = generarHtmlCorreo($request->correo, $claveRandom);
            $rstSendEmail = sendEmail('alfonsovegacelis6@gmail.com', 'BIENVENIDO A CONDOMIPRO', $html, '', [$request->correo], []);
            // return $rstSendEmail;
        }
                
        return ApiResponse::success('Se registro exitosamente', Response::HTTP_CREATED, $socio);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $socio = Socio::select(
            'socio.*',
            'persona.*',
            'distrito.*',
            DB::raw("CONCAT(persona.nombres, ', ', persona.apellidos) AS nombre_completo"),
            DB::raw("CONCAT(persona.nombres, ', ', persona.apellidos, ' - ', persona.nro_documento) AS nombre_completo_nro_documento"),
            'tipo_documento.descripcion AS tipo_documento',
            'tipo_persona.descripcion AS tipo_persona',
            'departamento.descripcion AS departamento',
            'provincia.descripcion AS provincia',
            'distrito.descripcion AS distrito')
        ->join('usuario', 'usuario.id_usuario', '=', 'socio.id_usuario')
        ->join('persona', 'persona.id_persona', '=', 'usuario.id_persona')
        ->join('tipo_documento', 'tipo_documento.id_tipo_documento', '=', 'persona.id_tipo_documento')
        ->join('tipo_persona', 'tipo_persona.id_tipo_persona', '=', 'persona.id_tipo_persona')
        ->join('distrito', 'distrito.id_distrito', '=', 'persona.id_distrito')
        ->join('provincia', 'provincia.id_provincia', '=', 'distrito.id_provincia')
        ->join('departamento', 'departamento.id_departamento', '=', 'distrito.id_departamento')
        ->where('socio.id_socio', '=', $id)->get();

        return ApiResponse::success('ok', Response::HTTP_OK, $socio);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $socio = Socio::findOrFail($id);
        $usuario = Usuario::findOrFail($socio->id_usuario);
        $persona = Persona::findOrFail($usuario->id_persona);

        $validator = Validator::make($request->all(), [
            'nombres' => 'required|string|max:50',
            'apellidos' => 'required|string|max:50',
            'direccion' => 'required|string|max:100',
            'nro_documento' => [
                'required', 'string', 'max:20', \Illuminate\Validation\Rule::unique('persona')->ignore($persona)
            ],
            'telefono' => 'required|string|max:20',
            'correo' => 'required|email|max:50',
            'id_departamento' => 'required|string|min:2|max:2',
            'id_provincia' => 'required|string|min:4|max:4',
            'id_distrito' => 'required|string|min:6|max:6',
            'id_tipo_documento' => 'required|numeric|gt:0',
            'id_tipo_persona' => 'required|numeric|gt:0',
        ], getMessageApi());

        $validator->setAttributeNames([
            'id_departamento' => 'departamento',
            'id_provincia' => 'provincia',
            'id_distrito' => 'distrito',
            'id_tipo_documento' => 'tipo documento',
            'id_tipo_persona' => 'tipo persona',
        ]);

        if($validator->fails()){
            return ApiResponse::error('Error de validacion ', Response::HTTP_UNPROCESSABLE_ENTITY, $validator->errors());
        }
    
        DB::beginTransaction();
            $persona->update($request->all());
        DB::commit();

        return ApiResponse::success('Se actualizo exitosamente', Response::HTTP_OK, $persona);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $socio = Socio::findOrFail($id);
        $id_usuario = $socio->id_usuario;
        $usuario = Usuario::where('id_usuario', $id_usuario)->first();
        $permisos = Permiso::where('id_usuario', $id_usuario)->get();
        
        DB::beginTransaction();
            $socio->delete();
            if (count($permisos) > 0) {
                Permiso::where('id_usuario', $id_usuario)->delete();
            }
            if ($usuario) { $usuario->delete(); }
        DB::commit();
        
        return ApiResponse::success('Se elimino exitosamente', Response::HTTP_OK);
    }
}
