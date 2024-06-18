<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Visitante;
use App\Http\Response\ApiResponse;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class VisitanteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $visitante = Visitante::select(
            'visitante.*',
            'tipo_documento.descripcion as tipo_documento')
        ->join('tipo_documento', 'tipo_documento.id_tipo_documento', '=', 'visitante.id_tipo_documento');

        if ($request->id_tipo_documento) {
            $visitante = $visitante->where('visitante.id_tipo_documento', '=', $request->id_tipo_documento);
        }

        if ($request->nro_documento) {
            $visitante = $visitante->where('visitante.nro_documento', '=', $request->nro_documento);
        }

        $visitante = $visitante->get();

        if (count($visitante) > 0 ) {
            return ApiResponse::success('ok', Response::HTTP_OK, $visitante);
        } else {
            return ApiResponse::error('no se encontraron resultado', Response::HTTP_NOT_FOUND);
        }        
    }
    
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_tipo_documento' => 'required|numeric|gt:0',
            'nro_documento' => 'required|string|max:20|unique:visitante',
            'nombres' => 'required|string|max:50',
            'apellidos' => 'required|string|max:50',
        ], getMessageApi());
        
        $validator->setAttributeNames([            
            'id_tipo_documento' => 'tipo documento',
            'id_propiedad' => 'propiedad'
        ]);

        if ($validator->fails()) {
            return ApiResponse::error('Error de validación', Response::HTTP_UNPROCESSABLE_ENTITY, $validator->errors());
        }

        $visitante = Visitante::create($request->all());
        return ApiResponse::success('Se registro exitosamente', Response::HTTP_CREATED, $visitante);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $visitante = Visitante::with('tipo_documento', 'createdBy', 'updatedBy')->findOrFail($id);
        return ApiResponse::success('ok', Response::HTTP_OK, $visitante);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $visitante = Visitante::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'nombres' => 'required|string|max:50',
            'apellidos' => 'required|string|max:50',
            'telefono' => 'required|string|max:20',
            'id_tipo_documento' => 'required|numeric|gt:0',
            'nro_documento' => [
                'required', 'string', 'max:20', \Illuminate\Validation\Rule::unique('visitante')->ignore($visitante)
            ],
        ]);

        if ($validator->fails()) {
            return ApiResponse::error('Error de validación', Response::HTTP_UNPROCESSABLE_ENTITY, $validator->errors());
        }

        $visitante->update($request->all());
        return ApiResponse::success('Se actualizó exitosamente', Response::HTTP_OK, $visitante);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $visitante = Visitante::findOrFail($id);
        $visitante->delete();
        return ApiResponse::success('Se eliminó exitosamente', Response::HTTP_OK);
    }
}