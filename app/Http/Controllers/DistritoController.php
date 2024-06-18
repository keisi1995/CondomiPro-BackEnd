<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Distrito;
use App\Http\Response\ApiResponse;
use Symfony\Component\HttpFoundation\Response;

class DistritoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($id_provincia, $id_departamento)
    {
        $distritos = Distrito::where([
            ['id_provincia', '=', $id_provincia],
            ['id_departamento', '=', $id_departamento]
        ])->get();
        return ApiResponse::success('ok', Response::HTTP_OK, $distritos);
    }
}
