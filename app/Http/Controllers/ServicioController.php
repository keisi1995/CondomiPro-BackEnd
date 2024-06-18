<?php

namespace App\Http\Controllers;

use App\Models\Servicio;
use App\Models\CtaPorCobrar;

use Illuminate\Http\Request;
use App\Http\Response\ApiResponse;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ServicioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // $servicio = Servicio::with('concepto')->get();
        $servicio = Servicio::select('servicio.*', 'concepto.descripcion AS concepto')
        ->join('concepto', 'concepto.id_concepto', '=', 'servicio.id_concepto')->get();

        return ApiResponse::success('ok', Response::HTTP_OK, $servicio);
    }
    
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'periodo' => 'required|string|max:2',
            'anio' => 'required|string|max:20',
            'total' => 'required|numeric|between:1,1000000.99',
            'id_concepto' => [ 'required', 'numeric', 'gt:0',
                Rule::unique('servicio')->where(function ($query) use ($request) {
                    return $query->where([
                        ['periodo', $request->periodo],
                        ['anio', $request->anio],
                        ['estado', 'activo'],
                    ]);
                })
            ],
            'detail' => 'required|array'
        ], getMessageApi());

        $validator->setAttributeNames([
            'id_concepto' => 'concepto',
        ]);

        if($validator->fails()) {
            return ApiResponse::error('Error de validación', Response::HTTP_UNPROCESSABLE_ENTITY, $validator->errors());
        }
                
        foreach ($request->detail as $value) {
            $validator = Validator::make($value, [
                'id_declaracion_jurada' => 'required|numeric|gt:0',
                'total' => 'required|numeric|between:1,1000000.99',
            ], getMessageApi());
    
            if($validator->fails()) {
                return ApiResponse::error('Error de validación', Response::HTTP_UNPROCESSABLE_ENTITY, ['detail' => $validator->errors()]);
            }
        }
     
        DB::beginTransaction();
            $servicio = Servicio::create($request->all());
            $id_servicio = $servicio->id_servicio;
            
            $detalle = array_map(function ($objeto) use ($id_servicio) {
                $objeto['id_servicio'] = $id_servicio;
                $objeto['created_at'] = now();
                $objeto['updated_at'] = now();
                return $objeto;
            }, $request->detail);
            
            CtaPorCobrar::insert($detalle);
        DB::commit();
        
        return ApiResponse::success('Se registro exitosamente', Response::HTTP_CREATED, $servicio);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $servicio = Servicio::findOrFail($id);
        return ApiResponse::success('ok', Response::HTTP_OK, $servicio);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $servicio = Servicio::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'periodo' => 'required|string|max:2',
            'anio' => 'required|string|max:20',
            'total' => 'required|numeric|between:0,1000000.99', // Puedes ajustar el rango según tus necesidades
            'id_concepto' => 'required|numeric|gt:0'
        ], getMessageApi());

        if($validator->fails()){
            return ApiResponse::error('Error de validacion ', Response::HTTP_UNPROCESSABLE_ENTITY, $validator->errors());
        }

        $servicio->update($request->all());
        return ApiResponse::success('Se actualizo exitosamente', Response::HTTP_OK, $servicio);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $servicio = Servicio::findOrFail($id);
        $servicio->delete();
        return ApiResponse::success('Se elimino exitosamente', Response::HTTP_OK);
    }
}
