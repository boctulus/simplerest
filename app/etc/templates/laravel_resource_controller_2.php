<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Libs\Validator;
use App\Models\__MODEL_NAME__;
use App\Models\UsuarioToken;
use App\Http\Resources\__RESOURCE_NAME__;
use Database\Factories\__MODEL_NAME__FakerFactory;
use Database\Seeders\__MODEL_NAME__ as Seeders__MODEL_NAME__;

date_default_timezone_set("America/Bogota");

class __CONTROLLER_NAME__ extends Controller
{
    __VALIDATION_RULES__

    private $respuesta = array('jsonapi' => ["version" => "1.0"]);
    private $error_code = 200;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (!$request->bearerToken()) {
            $this->respuesta['errors'] = "No autorizado";
            $this->error_code = 401;
            return response()->json($this->respuesta, $this->error_code);
        } 

        try {
            $ut = UsuarioToken::where('USE_TOKEN', $request->bearerToken())->first();
            if ($ut) {
                if ($ut->USE_ESTADO != "A") {
                    $this->respuesta['errors'] = "Token expirado";
                    $this->error_code = 401;
                    return response()->json($this->respuesta, $this->error_code);
                }
            } else {
                $this->respuesta['errors'] = "Token inexistente";
                $this->error_code = 401;
                return response()->json($this->respuesta, $this->error_code);
            }

            // OK
            if (!isset($request->paginado)) {
                ///TODOS
                $this->respuesta['meta'] = array("Listando todos los registros sin borrado lógico");
                $this->respuesta['data'] = __MODEL_NAME__::all();
            } else {
                ///paginado
                $this->respuesta['meta'] = array("Listando todos los registros paginados de {$request->paginado} en {$request->paginado}");
                $this->respuesta['data'] = __MODEL_NAME__::simplePaginate($request->paginado);
            }
        } catch (\Exception $e) {
            $this->respuesta['errors'] = $e->getMessage();
            $this->error_code = 500;
        }
    
        return response()->json($this->respuesta, $this->error_code);
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
        if (!$request->bearerToken()) {
            return response()->json("No autorizado", 401);
        }

        $ut = UsuarioToken::where('USE_TOKEN', $request->bearerToken())->first();
        if ($ut) {
            if ($ut->USE_ESTADO != "A") {
                $this->respuesta['errors'] = "Token expirado";
                $this->error_code = 401;
                return response()->json($this->respuesta, $this->error_code);
            }
        } else {
            $this->respuesta['errors'] = "Token inexistente";
            $this->error_code = 401;
            return response()->json($this->respuesta, $this->error_code);
        }

        $raw = $request->getContent();

        $this->respuesta = [];
        $this->respuesta['meta'] = array("Guardar un registro nuevo");
        
        if (empty(trim($raw))){
            return new Response([
                'meta'   => $this->respuesta['meta'],
                'errors' => 'Error al leer la data. Si se esta enviando en el Body?'
            ], 400);
        }

        $data = json_decode($raw, true);

        if (empty($data)){
            return new Response([
                'meta'   => $this->respuesta['meta'],
                'errors' => 'Error al leer la data. El formato JSON es correcto?'
            ], 400);
        }
        
        /*
            Validaciones
        */

        try {
            $fillables = (new __MODEL_NAME__)->getFillable();

            $v = new Validator();
            $validated = $v->validate(static::$validation_rules, $data, $fillables);

            if ($validated !== true){
                $this->respuesta['errors'] = $validated;
                $this->error_code = 400;
                return response()->json($this->respuesta, $this->error_code);
            }

            $id = __MODEL_NAME__::insertGetId($data);
           
            $this->respuesta = [
                'data' => [
                    __PRI_KEY__ => $id
                ],
                'message' => 'Registro creado exitosamente'
            ];

        } catch (\Exception $e){
            $this->respuesta['errors'] = $e->getMessage();
            $this->error_code = 500;
            return response()->json($this->respuesta, $this->error_code);
        }
    
        return response()->json($this->respuesta, $this->error_code);
    }
    /**
     * Display the specified resource.
     *
     * @param  \App\Models\__MODEL_NAME__  $obj
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        if (!$request->bearerToken()) {
            return response()->json("No autorizado", 401);
        }

        $ut = UsuarioToken::where('USE_TOKEN', $request->bearerToken())->first();
        if ($ut) {
            if ($ut->USE_ESTADO != "A") {
                $this->respuesta['errors'] = "Token expirado";
                $this->error_code = 401;
                return response()->json($this->respuesta, $this->error_code);
            }
        } else {
            $this->respuesta['errors'] = "Token inexistente";
            $this->error_code = 401;
            return response()->json($this->respuesta, $this->error_code);
        }

        $this->respuesta = [
            'meta'=> array("Consultar un registro existente")
        ];

        if (empty($id)){
            $this->respuesta['errors'] = "Debe enviar el id";
            return response()->json($this->respuesta, 400);
        }

        $include_deleted = isset($_GET['with_trashed']) && empty($_GET['with_trashed']) && ($_GET['with_trashed']!= '0');

        $instance = (new __MODEL_NAME__());
        
        $data = $instance
        ->find($id)
        // ->when(!$include_deleted, function($q){
        //     $q->where('__FIELD_BORRADO__', '!=', '1');
        // })
        ->first();

        if(!empty($data))
        {
            $this->respuesta['data'] = $data;    
            return response()->json($this->respuesta, 200);
        }
        else
        {
            $this->respuesta['message'] = 'No se encontró el recurso';   
            return response()->json($this->respuesta, 200); // o 204 No Content
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
        if (!$request->bearerToken()) {
            return response()->json("No autorizado", 401);
        }

        $ut = UsuarioToken::where('USE_TOKEN', $request->bearerToken())->first();
        if ($ut) {
            if ($ut->USE_ESTADO != "A") {
                $this->respuesta['errors'] = "Token expirado";
                $this->error_code = 401;
                return response()->json($this->respuesta, $this->error_code);
            }
        } else {
            $this->respuesta['errors'] = "Token inexistente";
            $this->error_code = 401;
            return response()->json($this->respuesta, $this->error_code);
        }

        $this->respuesta['meta'] = array("Actualizar un registro existente");

        if (empty($id)){
            $this->respuesta['errors'] = "Debe enviar el id";
            return response()->json($this->respuesta, 400);
        }

        $raw = $request->getContent();
        
        $include_deleted = isset($_GET['with_trashed']) && empty($_GET['with_trashed']) && ($_GET['with_trashed']!= '0');

        $this->respuesta = [];
        $this->respuesta['meta'] = array("Guardar un registro nuevo");
        
        if (empty(trim($raw))){
            return new Response([
                'meta'   => $this->respuesta['meta'],
                'errors' => 'Error al leer la data. Si se esta enviando en el Body?'
            ], 400);
        }

        $data = json_decode($raw, true);

        if (empty($data)){
            return new Response([
                'meta'   => $this->respuesta['meta'],
                'errors' => 'Error al leer la data. El formato JSON es correcto?'
            ], 400);
        }

        /*
            Validaciones
        */

        $instance = (new __MODEL_NAME__());

        try {
            $fillables = $instance->getFillable();

            $v = new Validator();
            
            $validated = $v
            ->setRequired(false)
            ->validate(static::$validation_rules, $data, $fillables);

            if ($validated !== true){
                $this->respuesta['errors'] = $validated;
                $this->error_code = 400;
                return response()->json($this->respuesta, $this->error_code);
            }

        } catch (\Exception $e){
            $this->respuesta['errors'] = $e->getMessage();
            $this->error_code = 500;
            return response()->json($this->respuesta, $this->error_code);
        }
        
        $exists = $instance
        ->find($id)
        // ->when(!$include_deleted, function($q){
        //     $q->where('__FIELD_BORRADO__', '!=', '1');
        // })
        ->exists();

        if (!$exists){
            $this->respuesta['message'] = "Recurso no encontrado";
            return response()->json($this->respuesta, 404);
        }

        $affected_rows = $instance
        ->find($id)
        ->update($data);

        $this->respuesta['data'] = $data;
        $this->respuesta['success'] = ($affected_rows != 0);

        return new Response($this->respuesta, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        if (!$request->bearerToken()) {
            $this->respuesta['errors'] = "No autorizado";
            $this->error_code = 401;
            return response()->json($this->respuesta, $this->error_code);
        } 

        $ut = UsuarioToken::where('USE_TOKEN', $request->bearerToken())->first();
        if ($ut) {
            if ($ut->USE_ESTADO != "A") {
                $this->respuesta['errors'] = "Token expirado";
                $this->error_code = 401;
                return response()->json($this->respuesta, $this->error_code);
            }
        } else {
            $this->respuesta['errors'] = "Token inexistente";
            $this->error_code = 401;
            return response()->json($this->respuesta, $this->error_code);
        }

        $this->respuesta['meta'] = array("Borrado lógico de un registro existente");
        
        try {
            //validar obligatorios
            if (isset($id) && $id > 0) {
                if ($p = __MODEL_NAME__::find($id)) {
                    // Toogle
                    if ($p->__FIELD_BORRADO__ == true) {
                        $p->__FIELD_BORRADO__ = false;
                    } else {
                        $p->__FIELD_BORRADO__ = true;
                    }

                    if ($p->save()) {
                        $this->respuesta['data'] = __MODEL_NAME__::find($id);
                    } else {
                        $this->respuesta['errors'] = "No se logró borrar, intente nuevamente";
                    }
                } else {
                    $this->respuesta['errors'] = "No existe un rol con el id {$id}";
                }
            } else {

                $this->respuesta['errors'] = "El id es obligatorio";
            }
        } catch (\Exception $e) {
            $this->respuesta['errors'] = $e->getMessage();
            $this->error_code = 500;
        }

        return response()->json($this->respuesta, $this->error_code);
    }

    // INI:__FN_HABILITAR__
    public function habilitar(Request $request, $id){       
        if (!$request->bearerToken()) {
            $this->respuesta['errors'] = "No autorizado";
            $this->error_code = 401;
            return response()->json($this->respuesta, $this->error_code);
        } 

        $ut = UsuarioToken::where('USE_TOKEN', $request->bearerToken())->first();
        if ($ut) {
            if ($ut->USE_ESTADO != "A") {
                $this->respuesta['errors'] = "Token expirado";
                $this->error_code = 401;
                return response()->json($this->respuesta, $this->error_code);
            }
        } else {
            $this->respuesta['errors'] = "Token inexistente";
            $this->error_code = 401;
            return response()->json($this->respuesta, $this->error_code);
        }

        $this->respuesta['meta'] = array("Cambio de estado de un registro existente");

        try {
            //validar obligatorios
            if (isset($id) && $id > 0) {
                $this->respuesta['errors'] = "El id es obligatorio";
                $this->error_code = 400;
                return response()->json($this->respuesta, $this->error_code);
            }

            if ($p = __MODEL_NAME__::find($id)) {
                // Toogle
                if ($p->__FIELD_HABILITADO__ == true) {
                    $p->__FIELD_HABILITADO__ = false;
                } else {
                    $p->__FIELD_HABILITADO__ = true;
                }

                if ($p->save()) {

                    $this->respuesta['data'] = __MODEL_NAME__::find($id);
                } else {
                    $this->respuesta['errors'] = "No se logró cambiar el estado, intente nuevamente";
                }
            } else {
                $this->respuesta['errors'] = "No existe un proceso con el id {$id}";
            }
        
        } catch (Exception $e) {
            $this->respuesta['errors'] = $e->getMessage();
            $this->error_code = 500;
        }
        
        return response()->json($this->respuesta, $this->error_code);
    }
    // END:__FN_HABILITAR__

}
