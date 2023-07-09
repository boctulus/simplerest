<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\OrgComunalEntidadRegistrante;
use App\Http\Resources\OrgComunalEntidadRegistranteResource;

class OrgComunalEntidadRegistranteController extends Controller
{
    static protected $rules = [
		'id' => 'nullable|integer',
		'tipo_vinculo' => 'required|string|max:255',
		'cant_personas' => 'required|integer',
		'org_comunal_id' => 'required|integer',
		'entidad_registrante_id' => 'required|integer',
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
        return OrgComunalEntidadRegistranteResource::collection( OrgComunalEntidadRegistrante::paginate() );
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
    
        return new OrgComunalEntidadRegistranteResource( OrgComunalEntidadRegistrante::create($validated) );
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\OrgComunalEntidadRegistrante  $obj
     * @return \Illuminate\Http\Response
     */
    public function show(OrgComunalEntidadRegistrante $obj)
    {
        return new OrgComunalEntidadRegistranteResource($obj);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\OrgComunalEntidadRegistrante  $obj
     * @return \Illuminate\Http\Response
     */
    public function edit(OrgComunalEntidadRegistrante $obj)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\OrgComunalEntidadRegistrante  $obj
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, OrgComunalEntidadRegistrante $obj)
    {
        try {
            $validated = $request->validate(static::$rules);
        } catch (\Exception $e){
            return new Response([
                'error' => $e->getMessage()
            ], 400);
        }
    
        $obj->update($validated);
        return new OrgComunalEntidadRegistranteResource($obj);
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
            $affected_rows = (new OrgComunalEntidadRegistrante())
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
