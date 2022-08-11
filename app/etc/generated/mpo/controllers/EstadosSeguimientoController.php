<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\EstadosSeguimiento;
use App\Http\Resources\EstadosSeguimientoResource;

class EstadosSeguimientoController extends Controller
{
    static protected $rules = [
		'id' => 'nullable|integer',
		'nombre' => 'unique|required|string|max:20',
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
        return EstadosSeguimientoResource::collection( EstadosSeguimiento::paginate() );
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
    
        return new EstadosSeguimientoResource( EstadosSeguimiento::create($validated) );
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\EstadosSeguimiento  $obj
     * @return \Illuminate\Http\Response
     */
    public function show(EstadosSeguimiento $obj)
    {
        return new EstadosSeguimientoResource($obj);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\EstadosSeguimiento  $obj
     * @return \Illuminate\Http\Response
     */
    public function edit(EstadosSeguimiento $obj)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\EstadosSeguimiento  $obj
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, EstadosSeguimiento $obj)
    {
        try {
            $validated = $request->validate(static::$rules);
        } catch (\Exception $e){
            return new Response([
                'error' => $e->getMessage()
            ], 400);
        }
    
        $obj->update($validated);
        return new EstadosSeguimientoResource($obj);
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
            $affected_rows = (new \App\Models\EstadosSeguimiento())
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
