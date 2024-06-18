<?php

namespace App\Http\Controllers;

use App\Models\Concepto;
use Illuminate\Http\Request;
use App\Http\Response\ApiResponse;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;

class ConceptoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
	public function index(Request $request)
    {
        // $concepto = Concepto::with('tipo_concepto');
        $concepto = Concepto::select('concepto.*', 'tipo_concepto.descripcion AS tipo_concepto')
        ->join('tipo_concepto', 'tipo_concepto.id_tipo_concepto', '=', 'concepto.id_tipo_concepto');

        if ($request->id_tipo_concepto) {
            $concepto = $concepto->where('concepto.id_tipo_concepto', '=', $request->id_tipo_concepto);
        }

        $concepto = $concepto->get();

        return ApiResponse::success('ok', Response::HTTP_OK, $concepto);
    }
        
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'descripcion' => 'required|string|max:50|unique:concepto',
            'id_tipo_concepto' => 'required|numeric|gt:0',
        ], getMessageApi());

        $validator->setAttributeNames([
            'id_tipo_concepto' => 'tipo concepto',
        ]);

        if($validator->fails()) {
            return ApiResponse::error('Error de validación', Response::HTTP_UNPROCESSABLE_ENTITY, $validator->errors());
        }

        $concepto = Concepto::create($request->all());
        return ApiResponse::success('Se registro exitosamente', Response::HTTP_CREATED, $concepto);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $concepto = Concepto::findOrFail($id);
        return ApiResponse::success('ok', Response::HTTP_OK, $concepto);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $concepto = Concepto::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'descripcion' => 'required|string|max:50',
            'id_tipo_concepto' => 'required|numeric|gt:0',
        ], getMessageApi());

        $validator->setAttributeNames([
            'id_tipo_concepto' => 'tipo concepto',
        ]);

        if($validator->fails()){
            return ApiResponse::error('Error de validacion ', Response::HTTP_UNPROCESSABLE_ENTITY, $validator->errors());
        }

        $concepto->update($request->all());
        return ApiResponse::success('Se actualizo exitosamente', Response::HTTP_OK, $concepto);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $concepto = Concepto::findOrFail($id);
        $concepto->delete();
        return ApiResponse::success('Se elimino exitosamente', Response::HTTP_OK);
    }

}
