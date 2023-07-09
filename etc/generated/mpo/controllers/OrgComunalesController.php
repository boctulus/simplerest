<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\OrgComunales;
use App\Http\Resources\OrgComunalesResource;

class OrgComunalesController extends Controller
{
    static protected $rules = [
		'id' => 'nullable|integer',
		'nombre_org_comunal' => 'required|string|max:60',
		'fecha_aplicacion' => 'required|date',
		'zona' => 'nullable|string|max:30',
		'comuna_corregimiento' => 'required|string|max:50',
		'direccion' => 'required|string|max:255',
		'departamento_id' => 'required|integer',
		'subregion_id' => 'required|integer',
		'municipio_id' => 'required|integer',
		'representante_legal_id' => 'required|integer',
		'tipo_organismo_org_comunal_id' => 'required|integer',
		'estado_seguimiento_id' => 'required|integer',
		'institucion_avalante' => 'nullable|string',
		'sector_actividad_org_comunal_id' => 'required|integer',
		'objetivo_social' => 'required|string|max:255',
		'certificacion_que_emite_org_comunal_id' => 'required|integer',
		'areas_tematicas' => 'required|string',
		'fortalezas_organizacion' => 'required|string',
		'redes' => 'nullable|string',
		'_tiene_estatutos' => 'required|boolean',
		'_registrada_camara_com' => 'required|boolean',
		'num_radicado_camara_com' => 'nullable|integer',
		'porc_cumpl_plan_ult_anno' => 'required|boolean',
		'plan_de_trabajo' => 'required|string',
		'plan_de_trabajo_pla_pub' => 'required|string',
		'plan_de_trabajo_pol_pub' => 'required|string',
		'instrumento_planeacion_id' => 'required|integer',
		'recursos_economicos' => 'nullable|string|max:255',
		'fuentes_financiacion' => 'nullable|string|max:255',
		'prespuesto_anual' => 'required|string|max:30',
		'_recursos_propios' => 'required|boolean',
		'proyectos_ejecutados_rec_propios' => 'nullable|string',
		'proyectos_ejecutados_rec_publicos' => 'nullable|string',
		'proyectos_ejecutados_cooperacion' => 'nullable|string',
		'_registro_ingresos_y_gastos' => 'required|boolean',
		'_obligaciones_tributarias' => 'required|boolean',
		'_contador' => 'required|boolean',
		'actividades_conjuntas_privadas' => 'nullable|string|max:255',
		'actividades_conjuntas_publicas' => 'nullable|string|max:255',
		'actividades_conjuntas_otras_org' => 'nullable|string|max:255',
		'escala_territorial_id' => 'required|integer',
		'medios_promocion' => 'required|string|max:255',
		'link_canal_emisora' => 'nullable|string|max:255',
		'_rendicion_cuentas' => 'required|boolean',
		'ultima_rend_cuenta' => 'required|string|max:120',
		'acciones_control_social' => 'nullable|string|max:255',
		'necesidades_asesoria' => 'nullable|string|max:255',
		'puntuacion' => 'required|string|max:20',
		'nivel_id' => 'required|integer',
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
        return OrgComunalesResource::collection( OrgComunales::paginate() );
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
    
        return new OrgComunalesResource( OrgComunales::create($validated) );
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\OrgComunales  $obj
     * @return \Illuminate\Http\Response
     */
    public function show(OrgComunales $obj)
    {
        return new OrgComunalesResource($obj);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\OrgComunales  $obj
     * @return \Illuminate\Http\Response
     */
    public function edit(OrgComunales $obj)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\OrgComunales  $obj
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, OrgComunales $obj)
    {
        try {
            $validated = $request->validate(static::$rules);
        } catch (\Exception $e){
            return new Response([
                'error' => $e->getMessage()
            ], 400);
        }
    
        $obj->update($validated);
        return new OrgComunalesResource($obj);
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
            $affected_rows = (new \App\Models\OrgComunales())
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
