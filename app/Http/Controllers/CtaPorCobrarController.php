<?php

namespace App\Http\Controllers;

use App\Models\CtaPorCobrar;

use Illuminate\Http\Request;
use App\Http\Response\ApiResponse;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator; 
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class CtaPorCobrarController extends Controller
{
   /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $ctaporcobrar = CtaPorCobrar::select(
            'servicio.anio',
            'servicio.periodo',
            'declaracion_jurada.id_declaracion_jurada',
            'concepto.id_concepto',
            'concepto.descripcion AS concepto',
            'tipo_concepto.descripcion AS tipo_concepto',
            'propiedad.nro_interior',
            DB::raw("CONCAT(persona.nombres, ' ', persona.apellidos) AS nombre_completo_socio"),
            DB::raw("DATE_FORMAT(cta_por_cobrar.created_at, '%m/%d/%Y') AS fecha_registro"),
            'cta_por_cobrar.insoluto',
            'cta_por_cobrar.intereses',
            'cta_por_cobrar.descuento',
            'cta_por_cobrar.total',
        )
        ->join('declaracion_jurada', 'declaracion_jurada.id_declaracion_jurada', '=', 'cta_por_cobrar.id_declaracion_jurada')
        ->join('servicio', 'servicio.id_servicio', '=', 'cta_por_cobrar.id_servicio')
        ->join('concepto', 'concepto.id_concepto', '=', 'servicio.id_concepto')
        ->join('tipo_concepto', 'tipo_concepto.id_tipo_concepto', '=', 'concepto.id_tipo_concepto')        
        ->join('propiedad', 'propiedad.id_propiedad', '=', 'declaracion_jurada.id_propiedad')
        ->join('socio', 'socio.id_socio', '=', 'declaracion_jurada.id_socio')
        ->join('usuario', 'usuario.id_usuario', '=', 'socio.id_usuario')
        ->join('persona', 'persona.id_persona', '=', 'usuario.id_persona')->get();

        return ApiResponse::success('ok', Response::HTTP_OK, $ctaporcobrar);
    }
    
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [

            'total' => 'required|numeric|between:0,999.99',
            'insoluto' => 'required|numeric|between:0,999.99',
            'intereses' => 'required|numeric|between:0,999.99',
            'descuento' => 'required|numeric|between:0,999.99', // Puedes ajustar el rango según tus necesidades
            'estado' => 'required|string|max:100',
            'observacion' => 'required|string|max:200',
            'id_servicio' => 'required|numeric|gt:0',
            'id_declaracion_jurada' => 'required|numeric|gt:0'
        ]);

        if($validator->fails()) {
            return ApiResponse::error('Error de validación', Response::HTTP_UNPROCESSABLE_ENTITY, $validator->errors());
        }

        $ctaporcobrar = CtaPorCobrar::create($request->all());
        return ApiResponse::success('Se registro exitosamente', Response::HTTP_CREATED, $ctaporcobrar);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $ctaporcobrar = CtaPorCobrar::findOrFail($id);
        return ApiResponse::success('ok', Response::HTTP_OK, $ctaporcobrar);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $ctaporcobrar = CtaPorCobrar::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'total' => 'required|numeric|between:0,999.99',
            'insoluto' => 'required|numeric|between:0,999.99',
            'intereses' => 'required|numeric|between:0,999.99',
            'descuento' => 'required|numeric|between:0,999.99', // Puedes ajustar el rango según tus necesidades
            'estado' => 'required|string|max:100',
            'observacion' => 'required|string|max:200',
            'id_servicio' => 'required|numeric|gt:0',
            'id_declaracion_jurada' => 'required|numeric|gt:0'
        ]);

        if($validator->fails()){
            return ApiResponse::error('Error de validacion ', Response::HTTP_UNPROCESSABLE_ENTITY, $validator->errors());
        }

        $ctaporcobrar->update($request->all());
        return ApiResponse::success('Se actualizo exitosamente', Response::HTTP_OK, $ctaporcobrar);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $ctaporcobrar = CtaPorCobrar::findOrFail($id);
        $ctaporcobrar->delete();
        return ApiResponse::success('Se elimino exitosamente', Response::HTTP_OK);
    }
}
