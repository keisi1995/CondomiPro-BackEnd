<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Modulo;
use App\Models\Menu;
use App\Http\Response\ApiResponse;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class ModuloController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $modulo = Modulo::all();
        return ApiResponse::success('ok', Response::HTTP_OK, $modulo);
    }

    public function modulo_tree(Request $request) {
        $modulo = Modulo::has('menu')->with(['menu' => function ($query)  {
            $query->orderBy('menu.nro_orden', 'asc');
        }])->orderBy('modulo.nro_orden', 'asc')->get();

        return ApiResponse::success('ok', Response::HTTP_OK, $modulo);
    }
    
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'descripcion' => 'required|string|max:100',
            'icono' => 'required|string|max:200',
        ]);

        if($validator->fails()) {
            return ApiResponse::error('Error de validación', Response::HTTP_UNPROCESSABLE_ENTITY, $validator->errors());
        }

        $modulo = Modulo::create($request->all());
        return ApiResponse::success('Se registró exitosamente', Response::HTTP_CREATED, $modulo);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $modulo = Modulo::findOrFail($id);
        return ApiResponse::success('ok', Response::HTTP_OK, $modulo);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $modulo = Modulo::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'descripcion' => 'required|string|max:100',
            'icono' => 'required|string|max:200',
        ]);

        if($validator->fails()){
            return ApiResponse::error('Error de validación', Response::HTTP_UNPROCESSABLE_ENTITY, $validator->errors());
        }

        $modulo->update($request->all());
        return ApiResponse::success('Se actualizó exitosamente', Response::HTTP_OK, $modulo);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $modulo = Modulo::findOrFail($id);
        $modulo->delete();
        return ApiResponse::success('Se eliminó exitosamente', Response::HTTP_OK);
    }
}
