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
    public function index(Request $request)
    {
        // if (!$request->bearerToken()) {
        //     return response()->json("No autorizado", 401);
        // }

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
        // if (!$request->bearerToken()) {
        //     return response()->json("No autorizado", 401);
        // }

        $raw = $request->getContent();

        $res = [];
        $res['meta'] = array("Guardar un registro nuevo");
        
        if (empty(trim($raw))){
            return new Response([
                'meta'   => $res['meta'],
                'errors' => 'Error al leer la data. Si se esta enviando en el Body?'
            ], 400);
        }

        $data = json_decode($raw, true);

        if (empty($data)){
            return new Response([
                'meta'   => $res['meta'],
                'errors' => 'Error al leer la data. El formato JSON es correcto?'
            ], 400);
        }
        
        try {
            $validated = $request->validate(static::$store_rules);
        } catch (\Exception $e){
            return new Response([
                'errors' => $e->getMessage()
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
    public function show(Request $request, $id)
    {
        // if (!$request->bearerToken()) {
        //     return response()->json("No autorizado", 401);
        // }

        $include_deleted = isset($_GET['with_trashed']) && empty($_GET['with_trashed']) && ($_GET['with_trashed']!= '0');

        $res = [
            'meta'=> array("Consultar un registro existente")
        ];

        $data = __MODEL_NAME__::find($id);

        if(!empty($data))
        {
            $res['data'] = $data;    
            return response()->json($res, 200);
        }
        else
        {
            $res['message'] = 'No se encontró el recurso';   
            return response()->json($res, 200); // o 204 No Content
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
        // if (!$request->bearerToken()) {
        //     return response()->json("No autorizado", 401);
        // }

        $raw = $request->getContent();

        $include_deleted = isset($_GET['with_trashed']) && empty($_GET['with_trashed']) && ($_GET['with_trashed']!= '0');

        $res['meta'] = array("Actualizar un registro existente");

        $raw = $request->getContent();

        $res = [];
        $res['meta'] = array("Guardar un registro nuevo");
        
        if (empty(trim($raw))){
            return new Response([
                'meta'   => $res['meta'],
                'errors' => 'Error al leer la data. Si se esta enviando en el Body?'
            ], 400);
        }

        $data = json_decode($raw, true);

        if (empty($data)){
            return new Response([
                'meta'   => $res['meta'],
                'errors' => 'Error al leer la data. El formato JSON es correcto?'
            ], 400);
        }

        try {
            $validated = $request->validate(static::$update_rules);
        } catch (\Exception $e){
            return new Response([
                'errors' => $e->getMessage()
            ], 500);
        }

        $obj = __MODEL_NAME__::find($id);

        if(empty($obj))
        {
            return response()->json([
                "message" => "Recurso no encontrado"
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
    public function destroy(Request $request, $id)
    {
        // if (!$request->bearerToken()) {
        //     return response()->json("No autorizado", 401);
        // }

        $res['meta'] = array("Borrado lógico de un registro existente");

        try {
            $affected_rows = (new __MODEL_NAME__())
            ->where('id', $id)
            ->delete();
        } catch (\Exception $e){
             return new Response([
                'success' => false,
                'message' => "No se logró borrar",
                'errors' => $e->getMessage()
            ], 500);
        }    

        $ok = ($affected_rows != 0);

        $res['success'] = $ok;

        return new Response($res, $ok ? 200 : 404);
    }
}
