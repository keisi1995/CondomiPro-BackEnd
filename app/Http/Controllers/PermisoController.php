<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Permiso;
use App\Models\Modulo;
use App\Http\Response\ApiResponse;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class PermisoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $permiso = Permiso::all();
        return ApiResponse::success('ok', Response::HTTP_OK, $permiso);
    }
    
    /**
     * Display a listing of the resource.
     */
    public function permissionByUser(Request $request, $id_usuario)
    {
        $modulo = Modulo::whereHas('menu', function ($query) use ($id_usuario) {
            $query->join('permiso', 'permiso.id_menu', '=', 'menu.id_menu')->where('permiso.id_usuario', '=', $id_usuario);
        })->with(['menu' => function ($query) use ($id_usuario) {
            $query->join('permiso', 'permiso.id_menu', '=', 'menu.id_menu')->where('permiso.id_usuario', '=', $id_usuario)
            ->orderBy('menu.nro_orden', 'asc');
        }])->orderBy('modulo.nro_orden', 'asc')->get();

        return ApiResponse::success('ok', Response::HTTP_OK, $modulo);
    }
    
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_usuario' => 'required|numeric|gt:0|exists:usuario,id_usuario',
            'id_menu' => 'required|numeric|gt:0|exists:menu,id_menu',
        ]);

        if($validator->fails()) {
            return ApiResponse::error('Error de validación', Response::HTTP_UNPROCESSABLE_ENTITY, $validator->errors());
        }

        $permiso = Permiso::create($request->all());
        return ApiResponse::success('Se registró exitosamente', Response::HTTP_CREATED, $permiso);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $permiso = Permiso::findOrFail($id);
        return ApiResponse::success('ok', Response::HTTP_OK, $permiso);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $permiso = Permiso::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'id_usuario' => 'required|numeric|gt:0|exists:usuario,id_usuario',
            'id_menu' => 'required|numeric|gt:0|exists:menu,id_menu',
        ]);

        if($validator->fails()){
            return ApiResponse::error('Error de validación', Response::HTTP_UNPROCESSABLE_ENTITY, $validator->errors());
        }

        $permiso->update($request->all());
        return ApiResponse::success('Se actualizó exitosamente', Response::HTTP_OK, $permiso);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $permiso = Permiso::findOrFail($id);
        $permiso->delete();
        return ApiResponse::success('Se eliminó exitosamente', Response::HTTP_OK);
    }
}
