<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use Illuminate\Http\Request;

use App\Http\Response\ApiResponse;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class MenuController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $menu = Menu::select(
            'menu.*',            
            'modulo.descripcion AS modulo',
        )->join('modulo', 'modulo.id_modulo', '=', 'menu.id_modulo')
        ->get();

        return ApiResponse::success('ok', Response::HTTP_OK, $menu);
    }
        
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'descripcion' => 'required|string|max:100',
            'icono' => 'required|string|max:200',
            'ruta' => 'required|string|max:200',
            'id_modulo' => 'required|numeric|gt:0|exists:modulo,id_modulo',
        ], getMessageApi());
        
        $validator->setAttributeNames([
            'id_modulo' => 'modulo'
        ]);

        if($validator->fails()) {
            return ApiResponse::error('Error de validación', Response::HTTP_UNPROCESSABLE_ENTITY, $validator->errors());
        }

        $menu = Menu::create($request->all());
        return ApiResponse::success('Se registró exitosamente', Response::HTTP_CREATED, $menu);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $menu = Menu::findOrFail($id);
        return ApiResponse::success('ok', Response::HTTP_OK, $menu);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $menu = Menu::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'descripcion' => 'required|string|max:100',
            'icono' => 'required|string|max:200',
            'ruta' => 'required|string|max:200',
            'id_modulo' => 'required|numeric|gt:0|exists:modulo,id_modulo',
        ], getMessageApi());

        $validator->setAttributeNames([
            'id_modulo' => 'modulo'
        ]);

        if($validator->fails()){
            return ApiResponse::error('Error de validación', Response::HTTP_UNPROCESSABLE_ENTITY, $validator->errors());
        }

        $menu->update($request->all());
        return ApiResponse::success('Se actualizó exitosamente', Response::HTTP_OK, $menu);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $menu = Menu::findOrFail($id);
        $menu->delete();
        return ApiResponse::success('Se eliminó exitosamente', Response::HTTP_OK);
    }
}
