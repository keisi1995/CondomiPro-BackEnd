<?php

namespace App\Http\Controllers;

use App\Models\Comprobante;
use Illuminate\Http\Request;

use App\Http\Response\ApiResponse;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;   

class ComprobanteController extends Controller
{
 /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $comprobante = Comprobante::with('tipo_comprobante')->get();
        return ApiResponse::success('ok', Response::HTTP_OK, $comprobante);
    }
    
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [

            'nro_comprobante' => 'required|string|max:20',
            'total' => 'required|numeric|between:0,999.99', // Puedes ajustar el rango según tus necesidades
            'estado' => 'required|string|max:50',
            'observacion' => 'required|string|max:200',
            'id_tipo_comprobante' => 'required|numeric|gt:0'
        ]);

        if($validator->fails()) {
            return ApiResponse::error('Error de validación', Response::HTTP_UNPROCESSABLE_ENTITY, $validator->errors());
        }

        $comprobante = Comprobante::create($request->all());
        return ApiResponse::success('Se registro exitosamente', Response::HTTP_CREATED, $comprobante);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $comprobante = Comprobante::findOrFail($id);
        return ApiResponse::success('ok', Response::HTTP_OK, $comprobante);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $comprobante = Comprobante::findOrFail($id);

        $validator = Validator::make($request->all(), [
           'nro_comprobante' => 'required|string|max:20',
            'total' => 'required|numeric|between:0,999.99', // Puedes ajustar el rango según tus necesidades
            'estado' => 'required|string|max:50',
            'observacion' => 'required|string|max:200',
            'id_tipo_comprobante' => 'required|numeric|gt:0'
        ]);

        if($validator->fails()){
            return ApiResponse::error('Error de validacion ', Response::HTTP_UNPROCESSABLE_ENTITY, $validator->errors());
        }

        $comprobante->update($request->all());
        return ApiResponse::success('Se actualizo exitosamente', Response::HTTP_OK, $comprobante);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $comprobante = Comprobante::findOrFail($id);
        $comprobante->delete();
        return ApiResponse::success('Se elimino exitosamente', Response::HTTP_OK);
    }

}