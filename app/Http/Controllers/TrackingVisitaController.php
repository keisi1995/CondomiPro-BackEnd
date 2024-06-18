<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TrackingVisita;
use App\Http\Response\ApiResponse;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;

class TrackingVisitaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
       /* $visitantes = Visitante::all();
        return ApiResponse::success('ok', Response::HTTP_OK, $visitantes);*/
        $tracking_visita = TrackingVisita::select(
            'tracking_visita.*',
            'solicitud_visita.*')
        ->join('solicitud_visita', 'solicitud_visita.id_solicitud_visita', '=', 'tracking_visita.id_solicitud_visita')->get();
        return ApiResponse::success('ok', Response::HTTP_OK, $tracking_visita);
    }
    
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'estado' => 'required|string|max:20',
            'id_solicitud_visita' => 'required|numeric|gt:0',
        ]);

        if ($validator->fails()) {
            return ApiResponse::error('Error de validación', Response::HTTP_UNPROCESSABLE_ENTITY, $validator->errors());
        }

        $tracking_visita = TrackingVisita::create($request->all());
        return ApiResponse::success('Se registró exitosamente', Response::HTTP_CREATED, $tracking_visita);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $tracking_visita = TrackingVisita::with('tipo_documento', 'createdBy', 'updatedBy')->findOrFail($id);
        return ApiResponse::success('ok', Response::HTTP_OK, $tracking_visita);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $tracking_visita = TrackingVisita::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'estado' => 'required|string|max:20',
            'id_solicitud_visita' => 'required|numeric|gt:0',
        ]);

        if ($validator->fails()) {
            return ApiResponse::error('Error de validación', Response::HTTP_UNPROCESSABLE_ENTITY, $validator->errors());
        }

        $tracking_visita->update($request->all());
        return ApiResponse::success('Se actualizó exitosamente', Response::HTTP_OK, $tracking_visita);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $tracking_visita = TrackingVisita::findOrFail($id);
        $tracking_visita->delete();
        return ApiResponse::success('Se eliminó exitosamente', Response::HTTP_OK);
    }
}