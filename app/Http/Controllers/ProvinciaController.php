<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Provincia;
use App\Http\Response\ApiResponse;
use Symfony\Component\HttpFoundation\Response;
 
class ProvinciaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($id_departamento)
    {
        $provincias = Provincia::where('id_departamento', '=', $id_departamento)->get();
        return ApiResponse::success('ok', Response::HTTP_OK, $provincias);
    }
}
