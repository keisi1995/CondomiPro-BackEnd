<?php

namespace App\Http\Controllers;

use App\Models\DetalleComprobante;
use Illuminate\Http\Request;
use App\Http\Response\ApiResponse;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator; 

class DetalleComprobanteController extends Controller
{
   /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $detallecomprobante = DetalleComprobante::with('comprobante')
                                                ->with('cta_por_cobrar')->get();
        return ApiResponse::success('ok', Response::HTTP_OK, $detallecomprobante);
    }
    
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [

            'subtotal' => 'required|numeric|between:0,999.99', // Puedes ajustar el rango según tus necesidades
            'descuento' => 'required|numeric|between:0,999.99',
            'total' => 'required|numeric|between:0,999.99',
            'id_comprobante' => 'required|numeric|gt:0',
            'id_cta_por_cobrar' => 'required|numeric|gt:0'
        ]);

        if($validator->fails()) {
            return ApiResponse::error('Error de validación', Response::HTTP_UNPROCESSABLE_ENTITY, $validator->errors());
        }

        $detallecomprobante = DetalleComprobante::create($request->all());
        return ApiResponse::success('Se registro exitosamente', Response::HTTP_CREATED, $detallecomprobante);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $detallecomprobante = DetalleComprobante::findOrFail($id);
        return ApiResponse::success('ok', Response::HTTP_OK, $detallecomprobante);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $detallecomprobante = DetalleComprobante::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'subtotal' => 'required|numeric|between:0,999.99', // Puedes ajustar el rango según tus necesidades
            'descuento' => 'required|numeric|between:0,999.99',
            'total' => 'required|numeric|between:0,999.99',
            'id_comprobante' => 'required|numeric|gt:0',
            'id_cta_por_cobrar' => 'required|numeric|gt:0'
        ]);

        if($validator->fails()){
            return ApiResponse::error('Error de validacion ', Response::HTTP_UNPROCESSABLE_ENTITY, $validator->errors());
        }

        $detallecomprobante->update($request->all());
        return ApiResponse::success('Se actualizo exitosamente', Response::HTTP_OK, $detallecomprobante);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $detallecomprobante = DetalleComprobante::findOrFail($id);
        $detallecomprobante->delete();
        return ApiResponse::success('Se elimino exitosamente', Response::HTTP_OK);
    }

}
