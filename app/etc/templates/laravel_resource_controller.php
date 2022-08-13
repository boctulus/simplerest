<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\__MODEL_NAME__;
use App\Http\Resources\__RESOURCE_NAME__;

class __CONTROLLER_NAME__ extends Controller
{
    __VALIDATION_RULES__

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return __RESOURCE_NAME__::collection( __MODEL_NAME__::paginate() );
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
            $validated = $request->validate(static::$store_rules);
        } catch (\Exception $e){
            return new Response([
                'error' => $e->getMessage()
            ], 400);
        }
    
        return new __RESOURCE_NAME__( __MODEL_NAME__::create($validated) );
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\__MODEL_NAME__  $obj
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $obj = __MODEL_NAME__::find($id);

        if(!empty($obj))
        {
            return response()->json($obj);
        }
        else
        {
            return response()->json([
                "mensaje" => "Recurso no encontrado"
            ], 404);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\__MODEL_NAME__  $obj
     * @return \Illuminate\Http\Response
     */
    public function edit(__MODEL_NAME__ $obj)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\__MODEL_NAME__  $obj
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate(static::$update_rules);
        } catch (\Exception $e){
            return new Response([
                'error' => $e->getMessage()
            ], 400);
        }

        $obj = __MODEL_NAME__::find($id);

        if(empty($obj))
        {
            return response()->json([
                "mensaje" => "Recurso no encontrado"
            ], 404);
        }      
    
        $obj->update($validated);
        return new __RESOURCE_NAME__($obj);
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
            $affected_rows = (new __MODEL_NAME__())
            ->where('id', $id)
            ->delete();
        } catch (\Exception $e){
            return new Response([
                'error' => $e->getMessage(),
                'exito' => false
            ], 400);
        }    

        $ok = ($affected_rows != 0);

        return new Response(['exito' => $ok], $ok ? 200 : 404);
    }
}
