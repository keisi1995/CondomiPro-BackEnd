<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Visita;
use App\Http\Response\ApiResponse;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class VisitaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $id_usuario = getValueToken($request, 'sub');

        $visita = Visita::select(
            'visita.*',
            'visitante.*',
            DB::raw("DATE_FORMAT(visita.fecha_hora_visita, '%d/%m/%Y') AS fecha_hora_visita"),
            DB::raw("CONCAT(visitante.nombres, ', ', visitante.apellidos) AS nombre_completo_visitante"),
            'visitante.nro_documento AS nro_documento_visitante',
            'propiedad.nro_interior AS propiedad',
            'edificacion.descripcion AS edificacion',
            'motivo.descripcion AS motivo',
            'propiedad.id_edificacion',
            DB::raw("IF(visita.flag_movilidad = 1, 'SI', 'NO') AS movilidad")
        )->join('usuario as usuario_visita', 'usuario_visita.id_usuario', '=', 'visita.id_usuario_visita')
        ->leftJoin('usuario as usuario_seguridad', 'usuario_seguridad.id_usuario', '=', 'visita.id_usuario_seguridad')
        ->join('propiedad', 'propiedad.id_propiedad', '=', 'visita.id_propiedad')
        ->join('edificacion', 'edificacion.id_edificacion', '=', 'propiedad.id_edificacion')
        ->join('visitante', 'visitante.id_visitante', '=', 'visita.id_visitante')
        ->join('motivo', 'motivo.id_motivo', '=', 'visita.id_motivo')
        ->where('visita.id_usuario_visita', '=', $id_usuario)->get();

        return ApiResponse::success('ok', Response::HTTP_OK, $visita);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_usuario_visita' => 'required|numeric|exists:usuario,id_usuario',
            'id_edificacion' => 'required|numeric|exists:edificacion,id_edificacion',
            'id_propiedad' => 'required|numeric|exists:propiedad,id_propiedad',
            'id_visitante' => 'required|numeric|exists:visitante,id_visitante',
            'id_motivo' => 'required|numeric|exists:motivo,id_motivo',
            'fecha_hora_visita' => 'required|datetime',
            'flag_movilidad' => 'required|boolean',
            'observacion' => 'string|max:255|nullable',
            'placa_vehiculo' => 'string|max:10|nullable', 
        ], getMessageApi());

        $validator->setAttributeNames([
            'id_edificacion' => 'edificacion',
            'id_propiedad' => 'propiedad',
        ]);

        if ($validator->fails()) {
            return ApiResponse::error('Error de validación', Response::HTTP_UNPROCESSABLE_ENTITY, $validator->errors());
        }

        $paramet['id_usuario_registro'] = getValueToken($request, 'sub');
        $request->merge($paramet);

        $visita = Visita::create($request->all());
        return ApiResponse::success('Se registró exitosamente', Response::HTTP_CREATED, $visita);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $visita = Visita::with('usuario_solicitante', 'usuario_seguridad', 'propiedad', 'visitante', 'motivo')->findOrFail($id);
        return ApiResponse::success('ok', Response::HTTP_OK, $visita);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $visita = Visita::findOrFail($id);
    
        $validator = Validator::make($request->all(), [
            'id_usuario_visita' => 'required|numeric|exists:usuario,id_usuario',
            'id_edificacion' => 'required|numeric|exists:edificacion,id_edificacion',
            'id_propiedad' => 'required|numeric|exists:propiedad,id_propiedad',
            'id_visitante' => 'required|numeric|exists:visitante,id_visitante',
            'id_motivo' => 'required|numeric|exists:motivo,id_motivo',
            'fecha_hora_visita' => 'required|datetime',
            'flag_movilidad' => 'required|boolean',
            'observacion' => 'string|max:255|nullable',
            'placa_vehiculo' => 'string|max:10|nullable', 
        ], getMessageApi());
                
        $validator->setAttributeNames([            
            'id_edificacion' => 'edificacion',
            'id_propiedad' => 'propiedad'
        ]);
        
        if ($validator->fails()) {
            return ApiResponse::error('Error de validación', Response::HTTP_UNPROCESSABLE_ENTITY, $validator->errors());
        }

        $paramet['fecha_hora_visita'] = date('Y-m-d', strtotime($request->fecha_hora_visita));
        $request->merge($paramet);
            
        $visita->update($request->all());
        return ApiResponse::success('Se actualizó exitosamente', Response::HTTP_OK, $visita);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $visita = Visita::findOrFail($id);
        $visita->delete();
        return ApiResponse::success('Se eliminó exitosamente', Response::HTTP_OK);
    }
}
