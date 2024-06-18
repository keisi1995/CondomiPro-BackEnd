<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\TipoPersona;
use App\Http\Response\ApiResponse;
use Symfony\Component\HttpFoundation\Response;

class TipoPersonaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tipos_persona = TipoPersona::all();
        return ApiResponse::success('ok', Response::HTTP_OK, $tipos_persona);
    }
}
