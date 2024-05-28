<?php

namespace simplerest\controllers\demos;

use simplerest\core\libs\DB;
use simplerest\core\Request;
use simplerest\core\Response;
use simplerest\core\libs\Factory;
use simplerest\core\libs\Strings;
use simplerest\core\controllers\Controller;
use simplerest\libs\LaravelApiGenerator;

class LaravelApiGenController extends Controller
{
        // Mini-endpoint
        function gen_laravel_mp_proyectos(){
            LaravelApiGenerator::setConnId('mpp');
    
            LaravelApiGenerator::capitalizeTableNames();
            LaravelApiGenerator::setControllerTemplatePath(ETC_PATH . "templates/laravel_resource_controller_2.php");
    
            LaravelApiGenerator::setProjectPath('D:/www/medellin-participa/seguridad');
            LaravelApiGenerator::setResourceDestPath('D:/www/medellin-participa/seguridad' . '/app/Http/Resources/');
            LaravelApiGenerator::setControllerDestPath('D:/www/medellin-participa/seguridad' . '/app/Http/Controllers/');
            // LaravelApiGenerator::setFactoryDestPath('D:/www/medellin-participa/seguridad' . '/database/factories/');
            // LaravelApiGenerator::setSeederDestPath('D:/www/medellin-participa/seguridad' . '/database/seeders/');
    
            LaravelApiGenerator::setControllerWhitelist([
                'InformacionContacto'
                // ...
            ]);
    
            LaravelApiGenerator::setValidator("SimpleRest");
    
            LaravelApiGenerator::registerCallback(function($fields){
                $softdelete_fieldname = null;
                foreach($fields as $field){
                    if (Strings::endsWith('BORRADO', $field)){
                        $softdelete_fieldname = $field;
                    }
                }
    
                $habilitado_fieldname = null;
                foreach($fields as $field){
                    if (Strings::endsWith('HABILITADO', $field)){
                        $habilitado_fieldname = $field;
                    }
                }
    
                if ($softdelete_fieldname == null){
                    die("Campo _BORRADO es obligatorio en el template");
                }
    
                return [
                    'eval' => [
                        "\$campo_borrado    = '$softdelete_fieldname';",
                        "\$campo_habilitado = '$habilitado_fieldname';",
                        "if (isset(\$campo_borrado)){
                            \$ctrl_file = str_replace('__FIELD_BORRADO__', \$campo_borrado, \$ctrl_file);
                        };",
                        "if (!isset(\$campo_habilitado) || empty(\$campo_habilitado)){
                            \$ctrl_file = \simplerest\core\libs\Strings::removeSubstring('// INI:__FN_HABILITAR__', '// END:__FN_HABILITAR__', \$ctrl_file);
                        };"
                    ]
                ];
            });
    
           
            LaravelApiGenerator::run();
        }
    
    
        
        /*
            No usar con este proyecto porque no cumple las convenciones de Laravel
        */
        function gen_laravel_mp_org_base(){
            LaravelApiGenerator::setConnId('mpo');
            LaravelApiGenerator::setProjectPath('D:/www/org_no_docker');
            LaravelApiGenerator::setResourceDestPath('D:/www/org_no_docker' . '/app/Http/Resources/');
            LaravelApiGenerator::setControllerDestPath('D:/www/org_no_docker' . '/app/Http/Controllers/');
            LaravelApiGenerator::setFactoryDestPath('D:/www/org_no_docker' . '/database/factories/');
            LaravelApiGenerator::setSeederDestPath('D:/www/org_no_docker' . '/database/seeders/');
    
            LaravelApiGenerator::run();
        }
    
        /*
            
        
            User ESTA funcion para "Organizaciones"
    
    
        */
        function gen_laravel_mp_org(){
            LaravelApiGenerator::setConnId('mpp'); // <----------------------- se comparte DB con produccion
            LaravelApiGenerator::setProjectPath('D:/www/org_no_docker');
            LaravelApiGenerator::setResourceDestPath('D:/www/org_no_docker' . '/app/Http/Resources/');
            LaravelApiGenerator::setControllerDestPath('D:/www/org_no_docker' . '/app/Http/Controllers/');
            LaravelApiGenerator::setFactoryDestPath('D:/www/org_no_docker' . '/database/factories/');
            LaravelApiGenerator::setSeederDestPath('D:/www/org_no_docker' . '/database/seeders/');
    
            LaravelApiGenerator::setControllerWhitelist([
                // 'TipoVinculoOER'
                // 'orgComunalEntidadRegController',
                // 'OrgComunal'
                //'ProyectoEjecutadoRecursosPropios'
            ]);
    
            LaravelApiGenerator::setControllerBlacklist([
                'UsuarioToken', // 
                'EstPersJur',  // pierde el campo de borrado
                'GrupoInteres' // pierde el campo de borrado
                // ...
            ]);
    
            // 
    
            LaravelApiGenerator::setSeederBlacklist([
                // ...
            ]);
    
            LaravelApiGenerator::addSeedersForHardcodedNonRandomData([
                // 'TipoVinculoOER',
                // 'Genero',
                // 'EstadoLaboral',
                // 'EstadoCivil',
                // 'Comuna',               // quitar luego
                // 'Municipio',            // quitar luego
                // 'Departamento',         // quitar luego
                // 'GrupoPoblacional',     // quitar luego
                // 'Barrio',               // quitar luego
                // 'EscalaTerritorial',
                // 'NivelEscolaridad',
                // 'Nivel',
                // 'SectorActividad',
                // 'Subregion',
                // 'TipoDoc',
                // 'TipoOrganismo',
                // 'InstrumentoPlaneacion',
                // 'CertificacionOrgComunal',
                // 'EstPersJur'
                // 'UsuarioToken',
                // 'GrupoInteres',
                // 'EstadoSeguimiento',
            ]);
    
            LaravelApiGenerator::addSeedersForRandomData([
                'ProyectoEjecutadoCooperacion',
                'ProyectoEjecutadoRecursosPropios',
                'ProyectoEjecutadoRecursosPublicos',
                'RepresentanteLegal',  // depende de TipoDoc, Departamento, Municipio, Genero, EstadoCivil, EstadoLaboral, NivelEscolaridad
                'EntidadReg', // depende de GrupoPoblacional
                'EntidadRegGrupoPoblacional', // tabla puente            
                'OrgComunal', // cantidad de dependencias
                'OrgComunalEntidadReg',  // sobre tabla puente
            ]);
    
            LaravelApiGenerator::setControllerTemplatePath(ETC_PATH . "templates/laravel_resource_controller_2.php");
    
            LaravelApiGenerator::setValidator("SimpleRest");
    
            LaravelApiGenerator::registerCallback(function($fields){
                $softdelete_fieldname = null;
                foreach($fields as $field){
                    if (Strings::endsWith('BORRADO', $field)){
                        $softdelete_fieldname = $field;
                    }
                }
    
                if ($softdelete_fieldname == null){
                    // die("Campo _BORRADO es obligatorio en el template");
                }
    
                $habilitado_fieldname = null;
                foreach($fields as $field){
                    if (Strings::endsWith('HABILITADO', $field)){
                        $habilitado_fieldname = $field;
                    }
                }
    
                return [
                    'eval' => [
                        "\$campo_borrado    = '$softdelete_fieldname';",
                        "\$campo_habilitado = '$habilitado_fieldname';",
                        "if (isset(\$campo_borrado)){
                            \$ctrl_file = str_replace('__FIELD_BORRADO__', \$campo_borrado, \$ctrl_file);
                        };",
                        "if (!isset(\$campo_habilitado) || empty(\$campo_habilitado)){
                            \$ctrl_file = \simplerest\core\libs\Strings::removeSubstring('// INI:__FN_HABILITAR__', '// END:__FN_HABILITAR__', \$ctrl_file);
                        };"
                    ]
                ];
            });
    
    
            #LaravelApiGenerator::writeModels(false);
            LaravelApiGenerator::writeControllers(true);
            LaravelApiGenerator::writeResources(false);
            LaravelApiGenerator::writeRoutes(false);
            LaravelApiGenerator::writeSeeders(false);
            LaravelApiGenerator::writeFactories(false);  // factories o seeders de random data
    
            LaravelApiGenerator::run();    
    }

}
