<?php

namespace simplerest\controllers\demos;

use simplerest\core\libs\DB;
use simplerest\core\Request;
use simplerest\core\Response;
use simplerest\core\libs\Factory;
use simplerest\core\libs\Validator;
use simplerest\core\controllers\Controller;
use simplerest\core\libs\ValidationRules;

class ValidatorController extends Controller
{
   
    function test_validator(){
        $data = [
            'nombre'=>'Pablo1',
            'apellido'=>'Bz',
            'segundo_apellido'=>'San Martín',
            'usuario'=>'',
            'celular'=>'321530', 
            'correo'=>'a@b',
            'calle'=>'0',
            'numero_de_casa'=>'',
            'observaciones'=>'la vida es complicada y bla bla bla bla bla bla bla',
            'fecha'=>'32-09-2019',
            'hora'=>'24:00:17',
            'rol'=>'',
            'fuerza'=>'100.xxx', //
            'estrato'=>'3',
            'felicidad'=>'0.25',
            'energia'=>'.25',
            'hora_almuerzo'=>'13:30:00',
            'hora_cena'=>'18:00:00',
            'fecha_nac'=>'10-12-1902',
            'frutas_favoritas'=>['bananas','manzanas']  // podria provenir de un grupo de checkboxes
            
        ];

        $rules = [
            'nombre' 			=> ['type'=>'alpha','required'=>true],
            'apellido' 			=> ['type'=>'alpha','required'=>true,'min'=>3,'max'=>30],
            'segundo_apellido'	=> ['type'=>'alpha','required'=>true,'min'=>3,'max'=>30],
            'usuario' 			=> ['required'=>true,'min'=>2,'max'=>15],
            'celular' 			=> ['type'=>'regex:/^[0-9]{10}$/','required'=>true],
            'correo' 			=> ['type'=>'email','required'=>true], 
            'calle' 			=> ['type'=>'int','required'=>false, 'min'=>1],
            'numero_de_casa'    => ['type'=>'numeric','required'=>false],
            'observaciones' 	=> ['type'=>'string','max'=>40],
            'fecha' 			=> ['type'=>'date'], 
            'hora' 				=> ['type'=>'time'], 
            'rol' 				=> ['type'=>'int','required'=>false], 
            'fuerza' 			=> ['type'=>'decimal','required'=>false],
            'estrato' 			=> ['type'=>'int','required'=>false, 'min'=>1, 'max'=>6],
            'felicidad' 		=> ['type'=>'int','required'=>false, 'min'=>0, 'max'=>100],
            'energia' 			=> ['type'=>'decimal','required'=>false, 'min'=>0, 'max'=>100],
            'hora_almuerzo' 	=> ['type'=>'time','min'=>'11:00:00','max'=>'10:15:00'],
            'hora_cena' 		=> ['type'=>'time','min'=>'19:00:00','max'=>'22:30:00'],
            'fecha_nac' 		=> ['type'=>'date','min'=>'01-01-1980','max'=>'12-12-2018'],
            'frutas_favoritas' 	=> ['type'=>'array','min'=>3]      
        ];

        $v = new Validator;

        if ($v->validate($data, $rules /*, $fillables */)){
            dd('Valido');
        } else {
           dd($v->getErrors(), 'Errores de validacion');
        } 
    }

    function test_in_array_validation(){
        $rules = [
            'rol' => ['type' => 'int', 'not_in' => [2, 4, 5]], //
            'poder' => ['not_between' => [4, 7]],
            'edad' => ['between' => [18, 100]],
            'magia' => ['in' => [3, 21, 81]], //
            'is_active' => ['type' => 'bool', 'messages' => ['type' => 'Value should be 0 or 1']]
        ];
    
        $data = [
            'nombre' => 'Juan Español',
            'username' => 'juan_el_mejor',
            'rol' => 5,
            'poder' => 6,
            'edad' => 101,
            'magia' => 22,
            'is_active' => 3
        ];
    
        $v = new Validator;
        
        if ($v->validate($data, $rules)){
            dd('Valido');
        } else {
            dd($v->getErrors(), 'Errores de validacion');
        }      
    }

    function test_array_validation(){
        $data = [
            'frutas_favoritas'  => ['bananas','manzanas'],  // podria provenir de un grupo de checkboxes
            'frutas_todas'      => ['bananas', 'manzanas', 'peras', 'mandarinas', 'uvas'],
            'colecciones'       => 'XYZ',
            'magia'             => 22,            
        ];

        $rules = [
            'frutas_favoritas' 	=> ['type'=>'array','len'=>3, 'min' => 50],    
            'frutas_todas'      => ['type'=>'array','min_len'=> 5],   
            'colecciones'       => ['type' => 'array'],
            'magia'             => ['in' => [3, 21, 81]], //
        ];

        $v = new Validator;

        if ($v->validate($data, $rules /*, $fillables */)){
            dd('Valido');
        } else {
           dd($v->getErrors(), 'Errores de validacion');
        } 
    }

    function test_validator_rules(){
        $data = [
            "fname" => "Tomas Juan",
            "lname" => "Cruz",
            "email" => "cruz_t@gmail.com",
            "input_channel_id" => 8,
            "is_active" => 2,
            "extra_fields" => [
                "rango_de_presupuesto" => "2M-3M"
            ]            
        ];
    
        $rules = (new ValidationRules())
            ->field('fname')->type('alpha')->required()->min(4)
            ->field('lname')->type('alpha')->required()->min(2)->max(100)
            ->field('email')->type('email')->required()
            ->field('input_channel_id')->type('int')->required()
            // ...
            ->field('is_active')->type('bool', 'Value should be 0 or 1')

        ->getRules();
    
        $v = new Validator;
    
        if ($v->validate($data, $rules /*, $fillables */)){
            dd('Valido');
        } else {
           dd($v->getErrors(), 'Errores de validacion');
        } 
    }


    function validation_test()
    {
        $rules = [
            // 'nombre'    => ['type' => 'alpha_spaces_utf8', 'min' => 3, 'max' => 40],
            // 'username'  => ['type' => 'alpha_dash', 'min' => 3, 'max' => '15'],
            'rol' 	    => ['type' => 'str', 'set' => ['buyer', 'seller']],
            // 'poder' => ['not_between' => [4, 7]],
            // 'edad' => ['between' => [18, 100]],
            // 'magia' => ['in' => [3, 21, 81]],
            // 'is_active' => ['type' => 'bool', 'messages' => ['type' => 'Value should be 0 or 1']]
        ];

        $data = [
            'nombre'        => 'Juan Español',
            'username'      => 'juan_el_mejor',
            'rol' 	        => 'registered',     
            'poder' => 6,
            'edad' => 101,
            'magia' => 22,
            'is_active' => 3
        ];

        $fillables = [
            'nombre',
            'username',
            'edad'
        ];

        $v = new Validator();
        
        $ok = $v->validate($data, $rules /*, $fillables */);

        if ($ok){
            dd('Valido');
        } else {
            dd($v->getErrors(), 'Errores de validacion');
        }
    }

    function validation_test2()
    {
        $rules = [
            'nombre' => ['type' => 'alpha_spaces_utf8', 'min' => 3, 'max' => 40],
            'username' => ['type' => 'alpha_dash', 'min' => 3, 'max' => '15'],
            'rol' => ['type' => 'int', 'not_in' => [2, 4, 5]],
            'poder' => ['not_between' => [4, 7]],
            'superpoder' => ['required' => true],
            'edad' => ['between' => [18, 100]],
            'magia' => ['in' => [3, 21, 81]],
            'is_active' => ['type' => 'bool', 'messages' => ['type' => 'Value should be 0 or 1']]
        ];

        $data = [
            'nombre' => 'Juan Español',
            'username' => 'juan_el_mejor',
            'rol' => 'fuerte',
            'poder' => 6,
            'edad' => 101,
            'magia' => 22,
            'is_active' => 3
        ];

        $fillables = [
            'nombre',
            'username',
            'edad'
        ];

        $v = new Validator();
        
        $ok = $v->validate($data, $rules /*, $fillables */);

        if ($ok){
            dd('Valido');
        } else {
            dd($v->getErrors(), 'Errores de validacion');
        }
    }

    function validation_test3()
    {
        $rules = [
            'nombre' => ['type' => 'alpha_spaces_utf8', 'min' => 3, 'max' => 40],
            'username' => ['type' => 'alpha_dash', 'min' => 3, 'max' => '15'],
            'rol' => ['type' => 'int', 'not_in' => [2, 4, 5]],
            'poder' => ['not_between' => [4, 7]],
            'superpoder' => ['required' => true],
            'edad' => ['between' => [18, 100]],
            'magia' => ['in' => [3, 21, 81]],
            'is_active' => ['type' => 'bool', 'messages' => ['type' => 'Value should be 0 or 1']]
        ];

        $data = [
            'nombre' => 'Juan Español',
            'username' => 'juan_el_mejor',
            'rol' => 5,
            'poder' => 6,
            'edad' => 101,
            'magia' => 22,
            'is_active' => 3
        ];

        $fillables = [
            'nombre',
            'username',
            'edad'
        ];

        $v = new Validator();
        dd($v->validate($data, $rules /*, $fillables */), 'AL CREAR');

        /*
            Al actualizar no necesito la regla de campos requeridos
        */

        $ok = $v
        ->setRequired(false)
        ->validate($data, $rules /*, $fillables */);
            
        if ($ok){
            dd('Valido');
        } else {
            dd($v->getErrors(), 'Errores de validacion (al MODIFICAR)');
        }
    }

    function validation_test4()
    {
        DB::getConnection('eb');

        $rules = [
            'vch_clienombre' => ['type' => 'alpha_spaces_utf8', 'min' => 3, 'max' => 40],
            'chr_cliedni' => ['type' => 'int', 'min' => 0],
        ];

        $data = [
            'vch_clienombre' => 'Juan Español',
            'chr_cliedni' => '10762367',
            'vch_clietelefono' => '924-7834',
        ];

        $fillables = [
            'vch_clienombre',
            'chr_cliedni',
            'vch_clietelefono'
        ];

        $uniques = [
            'chr_cliedni',
            'vch_clietelefono'
        ];


        $v = new Validator();
        $v->setUniques($uniques, 'cliente');

        if ($v->validate($data, $rules /*, $fillables */)){
            dd('Valido');
        } else {
            dd($v->getErrors(), 'Errores de validacion');
        }
    }

    function validacion()
    {
        $u = DB::table('users');
        dd($u->where(['username' => 'nano_'])->get());
    }

    function validacion1()
    {
        $u = DB::table('users')->setValidator(new Validator());
        $affected = $u->where(['email' => 'nano@'])->update(['firstname' => 'NA']);
    }

    function validacion2()
    {
        $u = DB::table('users')->setValidator(new Validator());
        $affected = $u->where(['email' => 'nano@'])->update(['firstname' => 'NA']);
    }

    function validacion3()
    {
        $p = DB::table('products')->setValidator(new Validator());
        $rows = $p->where(['cost' => '100X', 'belongs_to' => 90])->get();

        dd($rows);
    }

    function validacion4()
    {
        DB::getConnection('az');

        $p = DB::table('products')->setValidator(new Validator());
        $affected = $p->where(['cost' => '100X', 'belongs_to' => 90])->delete();

        dd($affected, 'Affected rows');
    }

    /*
        Revisar

        Como se hace realmente con los errores de validacion?
    */
    function test_error_response(){
        return error('Validation error', 400, [
            'Validation error 1',
            'Validation error 2',
            'Validation error N',
        ]);
    }

}

