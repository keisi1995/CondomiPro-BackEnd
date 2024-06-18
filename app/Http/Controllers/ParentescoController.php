<?php

namespace App\Http\Controllers;

use App\Models\Parentesco;
use Illuminate\Http\Request;
use App\Http\Response\ApiResponse;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class ParentescoController extends Controller
{
 /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $parentesco = Parentesco::all();
        // $distrito = Distrito::with('persona')->get();
        return ApiResponse::success('ok', Response::HTTP_OK, $parentesco);
    }
    
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'descripcion' => 'required|string|max:50'
        ], getMessageApi());

        if($validator->fails()) {
            return ApiResponse::error('Error de validación', Response::HTTP_UNPROCESSABLE_ENTITY, $validator->errors());
        }

        $parentesco = Parentesco::create($request->all());
        return ApiResponse::success('Se registro exitosamente', Response::HTTP_CREATED, $parentesco);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $parentesco = Parentesco::findOrFail($id);
        return ApiResponse::success('ok', Response::HTTP_OK, $parentesco);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $parentesco = Parentesco::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'descripcion' => 'required|string|max:50'
        ], getMessageApi());

        if($validator->fails()){
            return ApiResponse::error('Error de validacion ', Response::HTTP_UNPROCESSABLE_ENTITY, $validator->errors());
        }

        $parentesco->update($request->all());
        return ApiResponse::success('Se actualizo exitosamente', Response::HTTP_OK, $parentesco);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $parentesco = Parentesco::findOrFail($id);
        $parentesco->delete();
        return ApiResponse::success('Se elimino exitosamente', Response::HTTP_OK);
    }
}
