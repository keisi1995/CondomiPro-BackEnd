<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\TipoDocumento;
use App\Http\Response\ApiResponse;
use Symfony\Component\HttpFoundation\Response;

class TipoDocumentoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tipos_documento = TipoDocumento::all();
        return ApiResponse::success('ok', Response::HTTP_OK, $tipos_documento);
    }
}
