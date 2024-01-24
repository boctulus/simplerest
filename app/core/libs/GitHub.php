<?php

namespace simplerest\core\libs;

use simplerest\core\libs\Strings;

class GitHub
{
    /*
        La idea es comparar un commit (el ultimo en principio) de un repositorio vs de otro con estructura similar

        Asumiendo que $path_repo_1 es el PATH del repo donde queremos tener todo actualizado
        y que podria haber funciones o clases que nos hayamos olvidado en otro repo con PATH $path_repo_2,
        se efectua un diff

        COMANDOS

        git show -s --format=%ci HEAD~1
        git diff HEAD~1 HEAD

        En general

        git show -s --format=%ci c482f0a6492a1d9ebf13bc3fd9f86b5e84875bae
        git diff c482f0a6492a1d9ebf13bc3fd9f86b5e84875bae~1 c482f0a6492a1d9ebf13bc3fd9f86b5e84875bae

        NOTA: solo funciona en modo "cli"

    */
    static function diff($path_repo_1, $path_repo_2)
    {
        /*
            IDEA

            - En cada respositorio ir al directorio y hacer

                $ git log

            Almacenar cada hash y su date
            
                commit 2bd6a7d51953161ae6dd0d4c173fda5151e8459e
                Author: Pablo Bozzolo <boctulus@gmail.com>
                Date:   Sun Jan 21 09:02:57 2024 +0800

            -  Dado un commit (restringido a los posteriores a cierta fecha), obtener los archivos que han sufrido cambios:

                $ git diff --name-only {hash}~ {hash}

            Por defecto es para HEAD asi que:

                $ git diff --name-only HEAD~ HEAD

            Otengo asi los nombres de los archivos que han cambiado para ese commit con rutas relativas

            Ej:

                etc/BIG_FILE.txt

            - Si el archivo no existe mas mas en $path_repo_1 o $path_repo_2, nada que hacer.

            - Listar funciones / metodos en cada archivo que han sufrido cambios y almacena esa lista de funciones por archivo.  ~

                Usar PHPLexicalAnalyzer::getFunctionNames($code) 

            - Si el archivo en $path_repo_2 contiene funciones inexistentes en $path_repo_1 
            
            => chequear si esa funcion no fue eliminada de $path_repo_1 o sea si ha existido en previos commits
                       
                $ git diff  HEAD HEAD~{N} <-- ir hasta el commit mas antiguo 

            o ... ir iterando aumentando el N hasta que aparezca esa funcion en ese file

            En caso de ser una funcion nueva en path_repo_2 entonces debe reportarse:

                - PATH
                - FILE
                - FUNCION(ES)  <--- porque podrian listarse juntas separadas por ","

        */

        $git_installed = FileMemoization::memoize('git executable', function() {
            return System::inPATH('git');
        }, 2 );


        if (!$git_installed){
            throw new \Exception("git not found");
        }

        $git_log_repo_1 = FileMemoizationV2::memoize('git log', function() use ($path_repo_1) {
            return System::execAt("git log", $path_repo_1);
        }, 3600 );

        // $git_log_repo_2 = FileMemoizationV2::memoize('git log', function() use ($path_repo_2) {
        //     return System::execAt("git log", $path_repo_2);
        // }, 3600 );


        $lines = $git_log_repo_1;

        $ix = 0;
        $commit = Strings::match($lines[0], '/commmit ([a-f0-9]+)/', 0);

        dd($commit, 'COMMIT');
        exit;

        while (!$commit){
            $ix++;
            $commit = Strings::startsWith('commmit', $lines[0]);
            

        }

        // CONTINUAR (...)
    }

    /*
        Rastrea cambios que involicren un string entre distintos commits
    */
    static function search(string $str, $path_repo = null){

    }


}

