<?php

namespace simplerest\controllers;

use simplerest\core\libs\Files;
use simplerest\core\libs\Logger;
use simplerest\core\libs\Paginator;
use simplerest\core\libs\FileUploader;

/*
    Deberia encapsularse con el "shortcode" csv_importer

    Realiza el procesamiento paginado de en CSV

    El proceso de importacion se realiza en __process()

    La clase del controlador CSVImporterController deberia *extenderse* 
    para reemplazar el row() por un codigo personalizado

    # Vista "file_uploader.php"

    https://chatgpt.com/c/613ec8b2-6803-4b77-97cf-d6f24eb6d564
*/
class CsvImporterController
{
    protected $separator = ',';

    /*
        Aca va la logica del importador
    */
    protected function row($data){
        Logger::dd($data, 'ROW (por procesar)');
    }

    function __construct(){
        cors();
    }

    // Procesamiento paginado -- ok
    protected function __process($csv_filename, $offset, $limit)
    {
        try {
            $csv_path = UPLOADS_PATH . $csv_filename;

            Files::processCSV($csv_path , $this->separator, true, function($data) { 
                $this->row($data);
            }, null, $offset, $limit);  
            
        } catch (\Exception $e){
            Logger::logError($e->getMessage());
        }
    }

    // -- ok
    function upload()
    {
        $data = $_POST;

        try {
            delete_transient('bzz-importer_rows');
            delete_transient('bzz-importer_file');
            delete_transient('bzz-importer_completion');
            delete_transient('bzz-importer_current');

            $uploader = (new FileUploader())
            ->setFileHandler(function ($timestamp) {
                $prefix = ($timestamp) . '-';
                return uniqid($prefix, true);
            }, time());
    
    
            $files    = $uploader->doUpload()->getFileNames();
            $failures = $uploader->getErrors();

            // dd($files, 'FILES');
            // dd($failures, 'FAILURES');

    
            if (count($files) == 0) {
                error('No files or file upload failed', 400);
            }
    
            $f = $files[0];
    
            $ori_filename = $f['ori_name'];
            $as_stored    = $f['as_stored'];
    
            // Logger::log("$ori_filename stored as $as_stored");
    
            $row_cnt    = Files::countLines(UPLOADS_PATH . $as_stored);

            // dd($row_cnt, 'ROW COUNT'); //

            $page       = 1;
            $page_size  = 10;
            $paginator  = Paginator::calc($page, $page_size, $row_cnt);
            $last_page  = $paginator['totalPages'];
    
            $completion = 0;
    
            set_transient('bzz-importer_rows', $row_cnt,   9999);
            set_transient('bzz-importer_file', $as_stored, 9999);
            set_transient('bzz-importer_completion', $completion, 9999);
            set_transient('bzz-importer_current', $page, 9999);
                    
            response()->sendJson([
                'upload'   => [
                    'data'     => $data,
                    'file'     => $as_stored,
                    'failures' => $failures,
                ],  
    
                'paginator' => [
                    'current' => $page,
                    'next'    => ($last_page > 1) ? ($page+1) : null,
                    'last'    => $last_page, 
                    'count'   => $row_cnt
                ],
                
                'message'    => !empty($failures) ? 'Got errors during file upload' : null,
                'completion' => $completion
            ]);
        } catch (\Exception $e) {
            Logger::logError($e->getMessage());
        }      
    }

    /*
        /csv_importer/cancel
    */
    function cancel(){
        delete_transient('bzz-importer_rows');
        delete_transient('bzz-importer_file');
        delete_transient('bzz-importer_completion');
        delete_transient('bzz-importer_current');

        response()->sendJson([
            'paginator' => [
                'current' => null,
                'next'    => null,
                'last'    => null, 
                'count'   => null
            ],
            
            'message'    => 'Aborted.',
            'completion' => null
        ]);
    }
    
    // Ajax -- ok
    function process_page()
    {
        // Obtener los datos del cuerpo de la solicitud
        $data = json_decode(file_get_contents('php://input'), true);

        // Verificar si se han proporcionado los parámetros necesarios
        if (!isset($data['page'])) {
            error('Missing required parameters', 400);
        }

        // Obtener los parámetros
        $page          = $data['page'] ?? null;
        $page_size     = $data['page_size'] ?? 10;
        $row_cnt       = get_transient('bzz-importer_rows');
        $csv_filename  = get_transient('bzz-importer_file');

        if (empty($csv_filename) || empty($row_cnt)){
            response()->sendJson([
                'message'    => 'Nothing to do. Aborting.',
                'completion' => null,
                'paginator' => [
                    'current' => null,
                    'next'    => null,
                    'last'    => null, 
                    'count'   => null
                ],
            ]);

            return;
        }

        $offset       = Paginator::calcOffset($page, $page_size);
        $paginator    = Paginator::calc($page, $page_size, $row_cnt);
	    $last_page    = $paginator['totalPages'];

        $this->__process($csv_filename, $offset, $page_size);
        
        $completion = intval($page * 100 / $last_page);
        set_transient('bzz-importer_completion', $completion, 9999);
        set_transient('bzz-importer_current',    $page, 9999);

        // Verificar si es la última página procesada y limpiar transientes
        if ($completion == 100) {
            delete_transient('bzz-importer_rows');
            delete_transient('bzz-importer_file');
        }

        // sleep(2);

        // Responder con un mensaje de éxito
        response()->sendJson([
            'message'    => 'Page processed successfully',
            'completion' => $completion,
            'paginator' => [
                'current' => $page,
                'next'    => ($last_page > 1) ? ($page+1) : null,
                'last'    => $last_page, 
                'count'   => $row_cnt
            ],
        ]);
    }

    // Ajax -- ok
    function get_completion()
    {
       $data = [
            'completion'   => get_transient('bzz-importer_completion'),
            'current_page' => get_transient('bzz-importer_current')
       ];

       response()->sendJson($data);
    }

    function index()
    {
        dd("Index of ". __CLASS__);                   
    }
}



