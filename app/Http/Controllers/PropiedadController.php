<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\DeclaracionJurada;
use App\Models\Propiedad;
use App\Models\Socio;
use App\Models\Inquilino;
use App\Http\Response\ApiResponse;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;

class PropiedadController extends Controller
{
    /**
        * Display a listing of the resource.
    */
    public function index(Request $request)
    {
        $propiedad = Propiedad::select(
            'propiedad.*',
            'edificacion.descripcion AS edificacion'
        )->join('edificacion', 'edificacion.id_edificacion', '=', 'propiedad.id_edificacion');
        
        if ($request->id_edificacion) {
            $propiedad = $propiedad->where('propiedad.id_edificacion', '=', $request->id_edificacion);
        }
        
        $propiedad = $propiedad->get();

        return ApiResponse::success('ok', Response::HTTP_OK, $propiedad);
    }
    
    public function listPropiedad(Request $request, $id_edificacion)
    {
        $id_usuario = getValueToken($request, 'sub');
              
        $propiedad = DeclaracionJurada::select(
            'propiedad.id_propiedad',
            'propiedad.nro_interior AS nro_interior',
            'propiedad.id_edificacion AS id_edificacion'
        )->join('propiedad', 'propiedad.id_propiedad', '=', 'declaracion_jurada.id_propiedad')
        ->join('socio', 'socio.id_socio', '=', 'declaracion_jurada.id_socio')
        ->where([
            ['propiedad.id_edificacion', '=', $id_edificacion],
            ['socio.id_usuario', '=', $id_usuario],            
        ]);
               
        $propiedad = $propiedad->union(
            Inquilino::select(
                'propiedad.id_propiedad',
                'propiedad.nro_interior AS nro_interior',
                'propiedad.id_edificacion AS id_edificacion'
            )->join('declaracion_jurada AS ddjj', 'ddjj.id_declaracion_jurada', '=', 'inquilino.id_declaracion_jurada')
            ->join('propiedad', 'propiedad.id_propiedad', '=', 'ddjj.id_propiedad')
            ->where([
                ['propiedad.id_edificacion', '=', $id_edificacion],
                ['inquilino.id_usuario', '=',  $id_usuario],            
            ])          
        )->distinct()->get();
        
        return ApiResponse::success('ok', Response::HTTP_OK, $propiedad);
    }
   
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nro_interior' => 'required|string|max:20',
            'area_propiedad' => 'required|string|max:20',
            'id_edificacion' => 'required|numeric|gt:0',
        ], getMessageApi());

        $validator->setAttributeNames(['id_edificacion' => 'edificacion']);

        if($validator->fails()) {
            return ApiResponse::error('Error de validación', Response::HTTP_UNPROCESSABLE_ENTITY, $validator->errors());
        }

        $propiedad = Propiedad::create($request->all());
        return ApiResponse::success('Se registro exitosamente', Response::HTTP_CREATED, $propiedad);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, $id_propiedad)
    {
        $validator = Validator::make(['id_propiedad' => validateParameter($id_propiedad)],[
            'id_propiedad' => 'required|numeric|gt:0'
        ], getMessageApi());

        $validator->setAttributeNames(['id_propiedad' => 'propiedad']);

        if($validator->fails()) {
            return ApiResponse::error('Error de validación', Response::HTTP_UNPROCESSABLE_ENTITY, $validator->errors());
        }

        $propiedad = Propiedad::with('edificacion')->findOrFail($id_propiedad);
        return ApiResponse::success('ok', Response::HTTP_OK, $propiedad);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $propiedad = Propiedad::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'nro_interior' => 'required|string|max:20',
            'area_propiedad' => 'required|string|max:20',
            'id_edificacion' => 'required|numeric|gt:0',
        ], getMessageApi());

        $validator->setAttributeNames(['id_edificacion' => 'edificacion']);

        if($validator->fails()){
            return ApiResponse::error('Error de validacion ', Response::HTTP_UNPROCESSABLE_ENTITY, $validator->errors());
        }

        $propiedad->update($request->all());
        return ApiResponse::success('Se actualizo exitosamente', Response::HTTP_OK, $propiedad);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $propiedad = Propiedad::findOrFail($id);
        $propiedad->delete();
        return ApiResponse::success('Se elimino exitosamente', Response::HTTP_OK);
    }
}
