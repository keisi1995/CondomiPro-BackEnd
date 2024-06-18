<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\TipoConcepto;
use App\Http\Response\ApiResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

use Symfony\Component\HttpFoundation\Response;

use Exception;

class TipoConceptoController extends Controller
{    
 /**
     * Display a listing of the resource.
     */
	public function index()
    {
        $tipoconcepto = TipoConcepto::all();

        // $distrito = Distrito::with('persona')->get();
        return ApiResponse::success('ok', Response::HTTP_OK, $tipoconcepto);
    }
        
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // return $request->all();
        $validator = Validator::make($request->all(), [
            'descripcion' => 'required|string|max:50|unique:tipo_concepto',
        ], getMessageApi());

        if($validator->fails()) {
            return ApiResponse::error('Error de validación', Response::HTTP_UNPROCESSABLE_ENTITY, $validator->errors());
        }

        $tipoconcepto = TipoConcepto::create($request->all());
        return ApiResponse::success('Se registro exitosamente', Response::HTTP_CREATED, $tipoconcepto);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $tipoconcepto = TipoConcepto::findOrFail($id);
        return ApiResponse::success('ok', Response::HTTP_OK, $tipoconcepto);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $tipoconcepto = TipoConcepto::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'descripcion' => 'required|string|max:50',
        ], getMessageApi());

        if($validator->fails()){
            return ApiResponse::error('Error de validacion ', Response::HTTP_UNPROCESSABLE_ENTITY, $validator->errors());
        }

        $tipoconcepto->update($request->all());
        return ApiResponse::success('Se actualizo exitosamente', Response::HTTP_OK, $tipoconcepto);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $tipoconcepto = TipoConcepto::findOrFail($id);
        $tipoconcepto->delete();
        return ApiResponse::success('Se elimino exitosamente', Response::HTTP_OK);
    }


}
