<?php

namespace App\Http\Controllers;

use App\Models\DeclaracionJuradaDetalle;
use App\Models\Concepto;

use Illuminate\Http\Request;
use App\Http\Response\ApiResponse;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class DeclaracionJuradaDetalleController extends Controller
{
    /**
        * Display a listing of the resource.
    */
	public function index(Request $request)
    {
        $ddjjdetalle = DeclaracionJuradaDetalle::select(
            'declaracion_jurada_detalle.*',
            'concepto.descripcion AS concepto',
            'concepto.id_tipo_concepto AS id_tipo_concepto',
            'tipo_concepto.descripcion AS tipo_concepto',                        
            'propiedad.nro_interior AS nro_interior',
            DB::raw("IF(declaracion_jurada_detalle.tipo_calculo = 'M', 'Manual', 'Automatico') AS desc_tipo_calculo"),
            DB::raw("CONCAT(persona.nombres, ' ', persona.apellidos) AS nombre_completo_socio"),
            'declaracion_jurada.porcentaje_acciones AS porcentaje_acciones',
            DB::raw("0 AS total"),            
        )->join('concepto', 'concepto.id_concepto', '=', 'declaracion_jurada_detalle.id_concepto')
        ->join('tipo_concepto', 'tipo_concepto.id_tipo_concepto', '=', 'concepto.id_tipo_concepto')
        ->join('declaracion_jurada', 'declaracion_jurada.id_declaracion_jurada', '=', 'declaracion_jurada_detalle.id_declaracion_jurada')
        ->join('propiedad', 'propiedad.id_propiedad', '=', 'declaracion_jurada.id_propiedad')
        ->join('socio', 'socio.id_socio', '=', 'declaracion_jurada.id_socio')
        ->join('usuario', 'usuario.id_usuario', '=', 'socio.id_usuario')
        ->join('persona', 'persona.id_persona', '=', 'usuario.id_persona');

        if ($request->id_declaracion_jurada) {
            $ddjjdetalle = $ddjjdetalle->where('declaracion_jurada_detalle.id_declaracion_jurada', '=', $request->id_declaracion_jurada);
        }

        if ($request->id_concepto) {
            $ddjjdetalle = $ddjjdetalle->where('declaracion_jurada_detalle.id_concepto', '=', $request->id_concepto);
        }

        $ddjjdetalle = $ddjjdetalle->get();

        return ApiResponse::success('ok', Response::HTTP_OK, $ddjjdetalle);
    }
    
    public function listCtaporCobrar(Request $request, $id_concepto)
    {
        $concepto = Concepto::findOrFail($id_concepto);

        $ddjjdetalle = DeclaracionJuradaDetalle::select(
            'declaracion_jurada.id_declaracion_jurada',
            'concepto.id_concepto',
            'concepto.descripcion AS concepto',
            'tipo_concepto.descripcion AS tipo_concepto',
            'propiedad.nro_interior',
            'edificacion.descripcion AS edificacion',            
            DB::raw("IF(declaracion_jurada_detalle.tipo_calculo = 'M', 'Manual', 'Automatico') AS desc_tipo_calculo"),
            DB::raw("CONCAT(persona.nombres, ' ', persona.apellidos) AS nombre_completo_socio"),
            'declaracion_jurada.porcentaje_acciones AS porcentaje_acciones',
            DB::raw("0 AS total"),
        )->join('concepto', 'concepto.id_concepto', '=', 'declaracion_jurada_detalle.id_concepto')
        ->join('tipo_concepto', 'tipo_concepto.id_tipo_concepto', '=', 'concepto.id_tipo_concepto')
        ->join('declaracion_jurada', 'declaracion_jurada.id_declaracion_jurada', '=', 'declaracion_jurada_detalle.id_declaracion_jurada')
        ->join('propiedad', 'propiedad.id_propiedad', '=', 'declaracion_jurada.id_propiedad')
        ->join('edificacion', 'edificacion.id_edificacion', '=', 'propiedad.id_edificacion')
        ->join('socio', 'socio.id_socio', '=', 'declaracion_jurada.id_socio')
        ->join('usuario', 'usuario.id_usuario', '=', 'socio.id_usuario')
        ->join('persona', 'persona.id_persona', '=', 'usuario.id_persona')
        ->where('declaracion_jurada_detalle.id_concepto', '=', $concepto->id_concepto)->get();

        return ApiResponse::success('ok', Response::HTTP_OK, $ddjjdetalle);
    }
       
    /**
        * Store a newly created resource in storage.
    */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tipo_calculo' => 'required|string|max:20',
            'id_tipo_concepto' => 'required|numeric|gt:0',
            'id_concepto' => [
                'required', 'numeric', 'gt:0', 
                Rule::unique('declaracion_jurada_detalle')->where(function ($query) use ($request) {
                    return $query->where('id_declaracion_jurada', $request->id_declaracion_jurada);
                })
            ],
            'id_declaracion_jurada' => 'required|numeric|gt:0'
        ], getMessageApi());
        
        $validator->setAttributeNames(['id_concepto' => 'concepto']);

        if($validator->fails()) {
            return ApiResponse::error('Error de validación', Response::HTTP_UNPROCESSABLE_ENTITY, $validator->errors());
        }

        $ddjjdetalle = DeclaracionJuradaDetalle::create($request->all());
        return ApiResponse::success('Se registro exitosamente', Response::HTTP_CREATED, $ddjjdetalle);
    }

    /**
        * Display the specified resource.
    */
    public function show($id)
    {
        $ddjjdetalle = DeclaracionJuradaDetalle::findOrFail($id);
        return ApiResponse::success('ok', Response::HTTP_OK, $ddjjdetalle);
    }

    /**
        * Update the specified resource in storage.
    */
    public function update(Request $request, $id)
    {
        $ddjjdetalle = DeclaracionJuradaDetalle::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'tipo_calculo' => 'required|string|max:20',
            'id_tipo_concepto' => 'required|numeric|gt:0',
            'id_concepto' => [ 'required', 'numeric', 'gt:0',  
                Rule::unique('declaracion_jurada_detalle')->where(function ($query) use ($request) {
                    return $query->where('id_declaracion_jurada', $request->id_declaracion_jurada);
                })->ignore($ddjjdetalle)
            ],
        ], getMessageApi());

        $validator->setAttributeNames([
            'id_tipo_concepto' => 'tipo concepto',
            'id_concepto' => 'concepto'
        ]);

        if($validator->fails()) {
            return ApiResponse::error('Error de validacion ', Response::HTTP_UNPROCESSABLE_ENTITY, $validator->errors());
        }

        $ddjjdetalle->update($request->only('tipo_calculo', 'id_concepto'));
        return ApiResponse::success('Se actualizo exitosamente', Response::HTTP_OK, $ddjjdetalle);
    }

    /**
        * Remove the specified resource from storage.
    */
    public function destroy($id)
    {
        $ddjjdetalle = DeclaracionJuradaDetalle::findOrFail($id);
        $ddjjdetalle->delete();
        return ApiResponse::success('Se elimino exitosamente', Response::HTTP_OK);
    }
}
