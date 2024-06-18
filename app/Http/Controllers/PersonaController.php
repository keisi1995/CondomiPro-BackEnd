<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Persona;
use App\Models\Distrito;
use App\Http\Response\ApiResponse;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class PersonaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $persona = Persona::select(
            'persona.*',
            'distrito.*',
            DB::raw("CONCAT(persona.nombres, ', ', persona.apellidos, ' - ') AS nombre_completo"),
            DB::raw("CONCAT(persona.nombres, ', ', persona.apellidos, ' - ', persona.nro_documento) AS nombre_completo_nro_documento"),
            'tipo_documento.descripcion AS tipo_documento',
            'tipo_persona.descripcion AS tipo_persona',
            'departamento.descripcion AS departamento',
            'provincia.descripcion AS provincia',
            'distrito.descripcion AS distrito')
        ->join('tipo_documento', 'tipo_documento.id_tipo_documento', '=', 'persona.id_tipo_documento')
        ->join('tipo_persona', 'tipo_persona.id_tipo_persona', '=', 'persona.id_tipo_persona')
        ->join('distrito', 'distrito.id_distrito', '=', 'persona.id_distrito')
        ->join('provincia', 'provincia.id_provincia', '=', 'distrito.id_provincia')
        ->join('departamento', 'departamento.id_departamento', '=', 'distrito.id_departamento')
        ->get();
        
        return ApiResponse::success('ok', Response::HTTP_OK, $persona);
    }
    
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombres' => 'required|string|max:50',
            'apellidos' => 'required|string|max:50',
            'direccion' => 'required|string|max:100',
            'nro_documento' => 'required|string|max:20|unique:persona',
            'telefono' => 'required|string|max:20',
            'correo' => 'required|email|max:50',
            'id_departamento' => 'required|string|min:2|max:2',
            'id_provincia' => 'required|string|min:4|max:4',
            'id_distrito' => 'required|string|min:6|max:6',
            'id_tipo_documento' => 'required|numeric|gt:0',
            'id_tipo_persona' => 'required|numeric|gt:0'
        ], getMessageApi());

        if($validator->fails()) {
            return ApiResponse::error('Error de validación', Response::HTTP_UNPROCESSABLE_ENTITY, $validator->errors());
        }

        $persona = Persona::create($request->all());
        return ApiResponse::success('Se registro exitosamente', Response::HTTP_CREATED, $persona);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $persona = Persona::findOrFail($id);
        return ApiResponse::success('ok', Response::HTTP_OK, $persona);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $persona = Persona::findOrFail($id);

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
            'id_tipo_documento' => 'required|numeric|gt:0',
            'id_tipo_persona' => 'required|numeric|gt:0'
        ], getMessageApi());

        if($validator->fails()){
            return ApiResponse::error('Error de validacion ', Response::HTTP_UNPROCESSABLE_ENTITY, $validator->errors());
        }

        $persona->update($request->all());
        return ApiResponse::success('Se actualizo exitosamente', Response::HTTP_OK, $persona);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $persona = Persona::findOrFail($id);
        $persona->delete();
        return ApiResponse::success('Se elimino exitosamente', Response::HTTP_OK);
    }
}