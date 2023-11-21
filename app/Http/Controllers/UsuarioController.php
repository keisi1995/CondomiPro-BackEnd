<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Usuario;
use App\Http\Response\ApiResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Exception;

class UsuarioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try{
            $usuarios = Usuario::all();
            return ApiResponse::success('Listado', 200, $usuarios);
        } catch (Exception $e) {
            return ApiResponse::error('Error: ' . $e->getMessage(), 500);
        }
    }    

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try{
            $validator = Validator::make($request->all(), [
                'usuario' => 'required|string|min:5|max:20|unique:usuario',
                // 'email' => 'required|string|email|max:255|unique:users',
                'clave' => 'required|string|min:5|max:20|confirmed',
            ]);

            if ($validator->fails()) {
                return ApiResponse::error('Error de validación', 422, $validator->errors());
            }
         
            $usuario = Usuario::create($request->all());
            return ApiResponse::success('Se registro exitosamente', 201, $usuario);
        } catch (\Illuminate\Database\QueryException $e) {
            return ApiResponse::error('Code Error Query: ' . ($e->errorInfo)[1], 500);
        } catch (Exception $e){
            return ApiResponse::error('Error: ' . $e->getMessage(), 500);
        }
    }   
}
