<?php

namespace App\Http\Controllers;

use App\Models\TipoComprobante;
use Illuminate\Http\Request;

use App\Http\Response\ApiResponse;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;   

class TipoComprobanteController extends Controller
{
/**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tipocomprobante = TipoComprobante::all();
        return ApiResponse::success('Listado', 200, $tipocomprobante);
    }
    
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'descripcion' => 'required|string|max:50',
        ], getMessageApi());

        if($validator->fails()) {
            return ApiResponse::error('Error de validación', Response::HTTP_UNPROCESSABLE_ENTITY, $validator->errors());
        }

        $tipocomprobante = TipoComprobante::create($request->all());
        return ApiResponse::success('Se registro exitosamente', Response::HTTP_CREATED, $tipocomprobante);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $tipocomprobante = TipoComprobante::findOrFail($id);
        return ApiResponse::success('ok', Response::HTTP_OK, $tipocomprobante);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $tipocomprobante = TipoComprobante::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'descripcion' => 'required|string|max:50',
        ], getMessageApi());


        if($validator->fails()){
            return ApiResponse::error('Error de validacion ', Response::HTTP_UNPROCESSABLE_ENTITY, $validator->errors());
        }

        $tipocomprobante->update($request->all());
        return ApiResponse::success('Se actualizo exitosamente', Response::HTTP_OK, $tipocomprobante);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $tipocomprobante = TipoComprobante::findOrFail($id);
        $tipocomprobante->delete();
        return ApiResponse::success('Se elimino exitosamente', Response::HTTP_OK);
    }
}
