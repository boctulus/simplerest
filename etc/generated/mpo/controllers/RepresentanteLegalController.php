<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\RepresentanteLegal;
use App\Http\Resources\RepresentanteLegalResource;

class RepresentanteLegalController extends Controller
{
    static protected $rules = [
		'id' => 'nullable|integer',
		'tipo_doc_id' => 'required|integer',
		'nro_doc' => 'required|string|max:25',
		'departamento_exp_id' => 'required|integer',
		'municipio_exp_id' => 'required|integer',
		'nombres' => 'required|string|max:80',
		'apellidos' => 'required|string|max:100',
		'fecha_nacimiento' => 'required|date',
		'genero_id' => 'required|integer',
		'profesion_oficio' => 'required|string|max:40',
		'tarjeta_profesional' => 'required|boolean',
		'estado_civil_id' => 'required|integer',
		'estado_laboral_id' => 'required|integer',
		'tel_fijo' => 'nullable|string|max:30',
		'tel_celular' => 'required|string|max:30',
		'email' => 'required|string|max:255',
		'direccion' => 'required|string|max:255',
		'zona' => 'nullable|string|max:255',
		'barrio' => 'required|string|max:255',
		'sabe_leer' => 'required|boolean',
		'sabe_escribir' => 'required|boolean',
		'nivel_escolaridad_id' => 'required|integer',
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
        return RepresentanteLegalResource::collection( RepresentanteLegal::paginate() );
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
    
        return new RepresentanteLegalResource( RepresentanteLegal::create($validated) );
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\RepresentanteLegal  $obj
     * @return \Illuminate\Http\Response
     */
    public function show(RepresentanteLegal $obj)
    {
        return new RepresentanteLegalResource($obj);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\RepresentanteLegal  $obj
     * @return \Illuminate\Http\Response
     */
    public function edit(RepresentanteLegal $obj)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\RepresentanteLegal  $obj
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, RepresentanteLegal $obj)
    {
        try {
            $validated = $request->validate(static::$rules);
        } catch (\Exception $e){
            return new Response([
                'error' => $e->getMessage()
            ], 400);
        }
    
        $obj->update($validated);
        return new RepresentanteLegalResource($obj);
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
            $affected_rows = (new RepresentanteLegal())
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
