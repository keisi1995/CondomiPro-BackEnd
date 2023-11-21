<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Persona;
use App\Models\Distrito;
use App\Http\Response\ApiResponse;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;

class PersonaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $personas = Persona::with('distrito')
                    ->with('tipo_documento')
                    ->with('tipo_persona')->get();

        // $distrito = Distrito::with('persona')->get();
        return ApiResponse::success('ok', Response::HTTP_OK, $personas);
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
        ]);

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
        ]);

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