<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Inquilino;
use App\Models\Persona;
use App\Models\Usuario;
use App\Models\Socio;
use App\Models\DeclaracionJurada;
use App\Http\Response\ApiResponse;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class InquilinoController extends Controller
{
     /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $id_usuario = getValueToken($request, 'sub');

        $socio = Inquilino::select(
            'inquilino.*',
            'persona.*',
            'distrito.*',
            'tipo_documento.descripcion AS tipo_documento',
            'tipo_persona.descripcion AS tipo_persona',
            'departamento.descripcion AS departamento',
            'provincia.descripcion AS provincia',
            'distrito.descripcion AS distrito',
            'propiedad.nro_interior AS nro_interior',
            'edificacion.descripcion AS edificacion'
        )
        ->join('usuario', 'usuario.id_usuario', '=', 'inquilino.id_usuario')
        ->join('persona', 'persona.id_persona', '=', 'usuario.id_persona')
        ->join('tipo_documento', 'tipo_documento.id_tipo_documento', '=', 'persona.id_tipo_documento')
        ->join('tipo_persona', 'tipo_persona.id_tipo_persona', '=', 'persona.id_tipo_persona')
        ->join('distrito', 'distrito.id_distrito', '=', 'persona.id_distrito')
        ->join('provincia', 'provincia.id_provincia', '=', 'distrito.id_provincia')
        ->join('departamento', 'departamento.id_departamento', '=', 'distrito.id_departamento')
        ->join('declaracion_jurada as ddjj', 'ddjj.id_declaracion_jurada', '=', 'inquilino.id_declaracion_jurada')
        ->join('socio', 'socio.id_socio', '=', 'ddjj.id_socio')
        ->join('propiedad', 'propiedad.id_propiedad', '=', 'ddjj.id_propiedad')
        ->join('edificacion', 'edificacion.id_edificacion', '=', 'propiedad.id_edificacion')
        ->where('socio.id_usuario', '=', $id_usuario)->get();
           
        return ApiResponse::success('ok', Response::HTTP_OK, $socio);
    }
    
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $reglaValidator = [
            'id_edificacion' => 'required|numeric|gt:0|exists:edificacion,id_edificacion',
            'id_propiedad' => 'required|numeric|gt:0|exists:propiedad,id_propiedad',
            'id_persona' => 'required|numeric|gt:0|exists:persona,id_persona',
        ];

        $id_persona = $request->id_persona;
        $usuario = Usuario::all()->where('id_persona', $id_persona)->first();
       
        if (!$usuario) {
            $reglaValidator['correo'] = 'required|email|max:50|unique:usuario';
        } else {
            $inquilino = Inquilino::all()->where('id_usuario', $usuario->id_usuario)->first();
            if ($inquilino) {
                return ApiResponse::error('Error de validación', Response::HTTP_UNPROCESSABLE_ENTITY, [
                    'id_persona' => ['El campo persona ya se encuentra registrado.']
                ]);
            }
        }

        $validator = Validator::make($request->all(), $reglaValidator, getMessageApi());

        $validator->setAttributeNames([
            'id_edificacion' => 'edificacion',
            'id_propiedad' => 'propiedad',
            'id_persona' => 'persona'
        ]);

        if($validator->fails()) {
            return ApiResponse::error('Error de validación', Response::HTTP_UNPROCESSABLE_ENTITY, $validator->errors());
        }
       
        $sentEmail = false;
        $claveRandom = generarClaveRandom();
        $id_usuario = getValueToken($request, 'sub');
                
        DB::beginTransaction();
            if (!$usuario) {
                $usuario = Usuario::create([
                    'correo' => $request->correo,
                    'clave' => $claveRandom,
                    'id_persona' => $id_persona,
                    'id_tipo_usuario' => 3
                ]);
                $sentEmail = true;
            }

            $socio = Socio::where('id_usuario', '=', $id_usuario)->firstOrFail();
            
            $declaracionjurada = DeclaracionJurada::where([
                ['id_propiedad' , '=', $request->id_propiedad],
                ['id_socio', '=', $socio->id_socio]
            ])->firstOrFail();
  
            $inquilino = Inquilino::create([
                'id_declaracion_jurada' => $declaracionjurada->id_declaracion_jurada,
                'id_usuario' => $usuario->id_usuario
            ]);           
        DB::commit();
        
        if ($sentEmail) {
            $html = generarHtmlCorreo($request->correo, $claveRandom);
            $rstSendEmail = sendEmail('alfonsovegacelis6@gmail.com', 'BIENVENIDO A CONDOMIPRO', $html, '', [$request->correo], []);
        }

        return ApiResponse::success('Se registro exitosamente', Response::HTTP_CREATED, $inquilino);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $inquilino = Inquilino::select(
            'inquilino.*',
            'persona.*',
            'distrito.*',
            'tipo_documento.descripcion AS tipo_documento',
            'tipo_persona.descripcion AS tipo_persona',
            'departamento.descripcion AS departamento',
            'provincia.descripcion AS provincia',
            'distrito.descripcion AS distrito')
        ->join('usuario', 'usuario.id_usuario', '=', 'inquilino.id_usuario')
        ->join('persona', 'persona.id_persona', '=', 'usuario.id_persona')
        ->join('tipo_documento', 'tipo_documento.id_tipo_documento', '=', 'persona.id_tipo_documento')
        ->join('tipo_persona', 'tipo_persona.id_tipo_persona', '=', 'persona.id_tipo_persona')
        ->join('distrito', 'distrito.id_distrito', '=', 'persona.id_distrito')
        ->join('provincia', 'provincia.id_provincia', '=', 'distrito.id_provincia')
        ->join('departamento', 'departamento.id_departamento', '=', 'distrito.id_departamento')
        >where('inquilino.id_inquilino', '=', $id)->get();

        return ApiResponse::success('ok', Response::HTTP_OK, $inquilino);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $inquilino = Inquilino::findOrFail($id);
        $usuario = Usuario::findOrFail($inquilino->id_usuario);
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
        $inquilino = Inquilino::findOrFail($id);
        $usuario = Usuario::where('id_usuario', $inquilino->id_usuario)->first();
        
        DB::beginTransaction();
            $inquilino->delete();
            if ($usuario) { $usuario->delete(); }
        DB::commit();
        
        return ApiResponse::success('Se elimino exitosamente', Response::HTTP_OK);
    }
}
