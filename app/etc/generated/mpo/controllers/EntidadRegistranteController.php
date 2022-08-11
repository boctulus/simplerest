<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\EntidadRegistrante;
use App\Http\Resources\EntidadRegistranteResource;

class EntidadRegistranteController extends Controller
{
    static protected $rules = [
		'id' => 'nullable|integer',
		'num_radicado' => 'required|integer',
		'fecha_registro' => 'required|date',
		'_cert_vigente_gob' => 'required|boolean',
		'sede_organizacion' => 'required|string|max:255',
		'actividades' => 'required|string',
		'portafolio_servicios' => 'nullable|string',
		'presencia_geografica' => 'nullable|string',
		'grupo_poblacional_id' => 'required|integer',
		'mision' => 'nullable|string',
		'vision' => 'nullable|string',
		'objetivos' => 'nullable|string',
		'cant_miembros' => 'required|integer',
		'organo_direccion' => 'required|string|max:255',
		'cant_miembros_organo_dir' => 'required|integer',
		'_completo_organo_dir' => 'required|boolean',
		'como_elige_organo_dir' => 'required|string|max:255',
		'denominacion_organo_dir' => 'required|string|max:255',
		'frec_reunion_organo_dir' => 'required|string|max:255',
		'frec_reunion_miembros' => 'required|string|max:255',
		'tiempo_formulacion' => 'required|string|max:255',
		'deleted_at' => 'nullable',
		'created_at' => 'nullable',
		'updated_at' => 'nullable',
	];


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return EntidadRegistranteResource::collection( EntidadRegistrante::paginate() );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate(static::$rules);
        } catch (\Exception $e){
            return new Response([
                'error' => $e->getMessage()
            ], 400);
        }
    
        return new EntidadRegistranteResource( EntidadRegistrante::create($validated) );
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\EntidadRegistrante  $obj
     * @return \Illuminate\Http\Response
     */
    public function show(EntidadRegistrante $obj)
    {
        return new EntidadRegistranteResource($obj);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\EntidadRegistrante  $obj
     * @return \Illuminate\Http\Response
     */
    public function edit(EntidadRegistrante $obj)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\EntidadRegistrante  $obj
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, EntidadRegistrante $obj)
    {
        try {
            $validated = $request->validate(static::$rules);
        } catch (\Exception $e){
            return new Response([
                'error' => $e->getMessage()
            ], 400);
        }
    
        $obj->update($validated);
        return new EntidadRegistranteResource($obj);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $affected_rows = (new EntidadRegistrante())
            ->where('id', $id)
            ->delete();
        } catch (\Exception $e){
            return new Response([
                'error' => $e->getMessage()
            ], 400);
        }    

        $ok = ($affected_rows != 0);

        return new Response(['exito' => $ok], $ok ? 200 : 404);
    }
}
