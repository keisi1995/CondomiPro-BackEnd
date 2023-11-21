<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Departamento;
use App\Http\Response\ApiResponse;
use Symfony\Component\HttpFoundation\Response;

class DepartamentoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $departamentos = Departamento::all();
        return ApiResponse::success('ok', Response::HTTP_OK, $departamentos);
    }
}
