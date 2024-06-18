<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Motivo;
use App\Http\Response\ApiResponse;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;

class MotivoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $motivos = Motivo::all();
        return ApiResponse::success('ok', Response::HTTP_OK, $motivos);
    }
    
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'descripcion' => 'required|string|max:255',
        ], getMessageApi());

        if($validator->fails()) {
            return ApiResponse::error('Error de validación', Response::HTTP_UNPROCESSABLE_ENTITY, $validator->errors());
        }

        $motivo = Motivo::create($request->all());
        return ApiResponse::success('Se registró exitosamente', Response::HTTP_CREATED, $motivo);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $motivo = Motivo::findOrFail($id);
        return ApiResponse::success('ok', Response::HTTP_OK, $motivo);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $motivo = Motivo::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'descripcion' => 'required|string|max:255',
        ], getMessageApi());

        if($validator->fails()){
            return ApiResponse::error('Error de validación', Response::HTTP_UNPROCESSABLE_ENTITY, $validator->errors());
        }

        $motivo->update($request->all());
        return ApiResponse::success('Se actualizó exitosamente', Response::HTTP_OK, $motivo);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $motivo = Motivo::findOrFail($id);
        $motivo->delete();
        return ApiResponse::success('Se eliminó exitosamente', Response::HTTP_OK);
    }
}