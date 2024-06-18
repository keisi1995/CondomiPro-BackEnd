<?php

namespace App\Http\Controllers;

use App\Models\DeclaracionJurada;
use App\Models\DeclaracionJuradaDetalle;
use App\Models\Parentesco;

use Illuminate\Http\Request;
use App\Http\Response\ApiResponse;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class DeclaracionJuradaController extends Controller
{
    /**
        * Display a listing of the resource.
    */
	public function index(Request $request)
    {
        $declaracion_jurada = DeclaracionJurada::select(            
            'declaracion_jurada.*',
            'parentesco.descripcion AS parentesco',
            DB::raw("CONCAT(persona.nombres, ' ', persona.apellidos) AS nombre_completo_socio"),
            DB::raw("CONCAT(persona.nombres, ' ', persona.apellidos, ' - ', persona.nro_documento) AS nombre_completo_nro_documento_socio"),
            DB::raw("CONCAT(persona_parentesco.nombres, ' ', persona_parentesco.apellidos) AS nombre_completo_parentesco"),
            DB::raw("CONCAT(persona_parentesco.nombres, ' ', persona_parentesco.apellidos, ' - ', persona_parentesco.nro_documento) AS nombre_completo_nro_documento_parentesco")
        )->join('socio', 'socio.id_socio', '=', 'declaracion_jurada.id_socio')
        ->join('usuario', 'usuario.id_usuario', '=', 'socio.id_usuario')
        ->join('persona', 'persona.id_persona', '=', 'usuario.id_persona')
        ->join('propiedad', 'propiedad.id_propiedad', '=', 'declaracion_jurada.id_propiedad')
        ->join('parentesco', 'parentesco.id_parentesco', '=', 'declaracion_jurada.id_parentesco')
        ->leftJoin('persona as persona_parentesco', 'persona_parentesco.id_persona', '=', 'declaracion_jurada.id_persona');
        
        if ($request->id_propiedad) {
            $declaracion_jurada = $declaracion_jurada->where('declaracion_jurada.id_propiedad', '=', $request->id_propiedad);
        }
        
        $declaracion_jurada = $declaracion_jurada->with(['declaracion_jurada_detalle' => function ($query) {
            $query->select(
                'declaracion_jurada_detalle.*',
                'concepto.id_tipo_concepto AS id_tipo_concepto',
                'concepto.descripcion AS concepto',
                'tipo_concepto.descripcion AS tipo_concepto',
                DB::raw("IF(declaracion_jurada_detalle.tipo_calculo = 'M', 'Manual', 'Automatico') AS desc_tipo_calculo")
            )->join('concepto', 'concepto.id_concepto', '=', 'declaracion_jurada_detalle.id_concepto')
            ->join('tipo_concepto', 'tipo_concepto.id_tipo_concepto', '=', 'concepto.id_tipo_concepto');
        }])->get();
        
        return ApiResponse::success('ok', Response::HTTP_OK, $declaracion_jurada);
    }
        
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $reglaValidator = [
            'descripcion' => 'required|string|max:100',
            'porcentaje_acciones' => 'required|regex:/^\d{1,5}(\.\d{1,2})?$/',
            'id_socio' => [ 'required', 'numeric', 'gt:0',
                Rule::unique('declaracion_jurada')->where(function ($query) use ($request) {
                    return $query->where('id_propiedad', $request->id_propiedad);
                })
            ],
            'id_propiedad' => 'required|numeric|gt:0',
            'id_parentesco' => 'required|numeric|gt:0',
            // 'detail' => 'required|json',
        ];

        if ($request->id_parentesco) {
            $parentesco = Parentesco::all()->where('id_parentesco', $request->id_parentesco)->first();

            if ($parentesco) {
                if (strtolower($parentesco->descripcion) !== 'titular') {
                    $reglaValidator['id_persona'] = 'required|numeric|gt:0';
                } else {
                    $request->merge(['id_persona' => null]);
                }
            }
        }

        $validator = Validator::make($request->all(), $reglaValidator, getMessageApi());

        $validator->setAttributeNames([
            'id_socio' => 'socio',
            'id_propiedad' => 'propiedad',
            'id_parentesco' => 'parentesco',
            'id_persona' => 'persona'
        ]);

        if($validator->fails()) {
            return ApiResponse::error('Error de validación', Response::HTTP_UNPROCESSABLE_ENTITY, $validator->errors());
        }
        
        $dataDetail = [];
        if (isset($request->detail)) {
            foreach ($request->detail as $value) {
                $validator = Validator::make($value, [
                    'tipo_calculo' => 'required|string|max:20',
                    'id_concepto' => 'required|numeric|gt:0',
                ], getMessageApi());
    
                $validator->setAttributeNames(['id_concepto' => 'concepto']);
        
                if($validator->fails()) {
                    return ApiResponse::error('Error de validación', Response::HTTP_UNPROCESSABLE_ENTITY, $validator->errors());
                }
            }

            $myKeys = ['tipo_calculo', 'id_concepto'];
            $dataDetail = array_map(function ($objeto) use ($myKeys) {
                return array_intersect_key($objeto, array_flip($myKeys));
            }, $request->detail);
        }
        
        DB::beginTransaction();
            $declaracionjurada = DeclaracionJurada::create($request->all());
            $id_declaracion_jurada = $declaracionjurada->id_declaracion_jurada;

            if (count($dataDetail) > 0) {
                $detalle = array_map(function ($objeto) use ($id_declaracion_jurada) {
                    $objeto['id_declaracion_jurada'] = $id_declaracion_jurada;
                    $objeto['created_at'] = now();
                    $objeto['updated_at'] = now();
                    return $objeto;
                }, $dataDetail);
                
                DeclaracionJuradaDetalle::insert($detalle);
            }
        DB::commit();

        return ApiResponse::success('Se registro exitosamente', Response::HTTP_CREATED, $declaracionjurada);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $declaracionjurada = DeclaracionJurada::findOrFail($id);
        return ApiResponse::success('ok', Response::HTTP_OK, $declaracionjurada);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $declaracionjurada = DeclaracionJurada::findOrFail($id);

        $reglaValidator = [
            'descripcion' => 'required|string|max:100',
            'porcentaje_acciones' => 'required|regex:/^\d{1,5}(\.\d{1,2})?$/',
            'id_socio' => [ 'required', 'numeric', 'gt:0',
                Rule::unique('declaracion_jurada')->where(function ($query) use ($request) {
                    return $query->where('id_propiedad', $request->id_propiedad);
                })->ignore($declaracionjurada)
            ],
            'id_parentesco' => 'required|numeric|gt:0',
        ];

        if ($request->id_parentesco) {
            $parentesco = Parentesco::all()->where('id_parentesco', $request->id_parentesco)->firstOrFail();
            if (strtolower($parentesco->descripcion) !== 'titular') { $reglaValidator['id_persona'] = 'required|numeric|gt:0'; }
        }

        $validator = Validator::make($request->all(), $reglaValidator, getMessageApi());
    
        $validator->setAttributeNames([
            'id_socio' => 'socio',
            'id_parentesco' => 'parentesco'
        ]);

        if ($validator->fails()) {
            return ApiResponse::error('Error de validacion ', Response::HTTP_UNPROCESSABLE_ENTITY, $validator->errors());
        }

        $declaracionjurada->update([
            'descripcion' => $request->descripcion,
            'porcentaje_acciones' => $request->porcentaje_acciones,
            'observacion' => $request->observacion,
            'id_socio' => $request->id_socio,
            'id_persona' => $request->id_persona,
            'id_parentesco' => $request->id_parentesco,
        ]);
        return ApiResponse::success('Se actualizo exitosamente', Response::HTTP_OK, $declaracionjurada);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $declaracionjurada = DeclaracionJurada::findOrFail($id);
        
        DB::beginTransaction();
            DeclaracionJuradaDetalle::where('id_declaracion_jurada', '=', $declaracionjurada->id_declaracion_jurada)->delete();
            $declaracionjurada->delete();
        DB::commit();

        return ApiResponse::success('Se elimino exitosamente', Response::HTTP_OK);
    }


}
