<?php

namespace App\Http\Controllers;

use App\Models\Edificacion;
use Illuminate\Http\Request;

use App\Http\Response\ApiResponse;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;

class EdificacionController extends Controller
{
    
     /**
         * Display a listing of the resource.
         */
        public function index()
        {
            $edificacion = Edificacion::all();
            return ApiResponse::success('Listado', 200, $edificacion);
        }
        
        /**
         * Store a newly created resource in storage.
         */
        public function store(Request $request)
        {
            $validator = Validator::make($request->all(), [
                'descripcion' => 'required|string|max:50',
                'direccion' => 'required|string|max:100',
                'cantidad_pisos' => 'required|numeric|gt:0',
            ], getMessageApi());
    
            if($validator->fails()) {
                return ApiResponse::error('Error de validación', Response::HTTP_UNPROCESSABLE_ENTITY, $validator->errors());
            }
    
            $edificacion = Edificacion::create($request->all());
            return ApiResponse::success('Se registro exitosamente', Response::HTTP_CREATED, $edificacion);
        }
    
        /**
         * Display the specified resource.
         */
        public function show($id)
        {
            $edificacion = Edificacion::findOrFail($id);
            return ApiResponse::success('ok', Response::HTTP_OK, $edificacion);
        }
    
        /**
         * Update the specified resource in storage.
         */
        public function update(Request $request, $id)
        {
            $edificacion = Edificacion::findOrFail($id);
    
            $validator = Validator::make($request->all(), [
                'descripcion' => 'required|string|max:50',
                'direccion' => 'required|string|max:100',
                'cantidad_pisos' => 'required|numeric|gt:0',
            ], getMessageApi());
    
    
            if($validator->fails()){
                return ApiResponse::error('Error de validacion ', Response::HTTP_UNPROCESSABLE_ENTITY, $validator->errors());
            }
    
            $edificacion->update($request->all());
            return ApiResponse::success('Se actualizo exitosamente', Response::HTTP_OK, $edificacion);
        }
    
        /**
         * Remove the specified resource from storage.
         */
        public function destroy($id)
        {
            $edificacion = Edificacion::findOrFail($id);
            $edificacion->delete();
            return ApiResponse::success('Se elimino exitosamente', Response::HTTP_OK);
        }    
}
