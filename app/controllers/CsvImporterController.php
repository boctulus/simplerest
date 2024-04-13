<?php

namespace simplerest\controllers;

use simplerest\core\libs\Files;
use simplerest\core\libs\Logger;
use simplerest\core\libs\Paginator;
use simplerest\core\libs\FileUploader;

class CSVImporterController
{
    function __construct(){
        // Resuelvo CORS
        cors();
    }
    
    function upload()
    {
        $data = $_POST;

        $uploader = (new FileUploader())
        ->setFileHandler(function ($timestamp) {
            $prefix = ($timestamp) . '-';
            return uniqid($prefix, true);
        }, time());


        $files    = $uploader->doUpload()->getFileNames();
        $failures = $uploader->getErrors();

        if (count($files) == 0) {
            error('No files or file upload failed', 400);
        }

        $f = $files[0];

        $ori_filename = $f['ori_name'];
        $as_stored    = $f['as_stored'];

        Logger::log("$ori_filename stored as $as_stored");

        $row_cnt   = Files::countLines(UPLOADS_PATH . $as_stored);
        $page      = 0;
        $page_size = 5;
        $offset    = Paginator::calcOffset($page, $page_size);
        $paginator = Paginator::calc($page, $page_size, $row_cnt);
	    $last_page = $paginator['totalPages'];

        $this->do_process($as_stored, $offset, $page_size);

        // set_transient('bzz-import_completion', $page, 9999);

        return [
            'upload'   => [
                'data'     => $data,
                'file'     => $as_stored,
                'failures' => $failures,
            ],  

            'paginator' => [
                'current' => $page,
                'last'    => $last_page, 
                'count'   => $row_cnt
            ],
            
            'message'  => !empty($failures) ? 'Got errors during file upload' : null
        ];
    }
    
    // Hago la importacion de forma paginada
    protected function do_process($csv_filename, $offset, $limit)
    {
        try {
            $csv_path = UPLOADS_PATH . $csv_filename;

            Files::processCSV($csv_path , ';', true, function($row) { 

                // Aca ira la logica del importador
                Logger::dd($row, 'ROW (por procesar)');

            }, null, $offset, $limit);  
            
            
        } catch (\Exception $e){
            Logger::logError($e->getMessage());
        }
    }

    // Ajax -- ok
    function process_page()
    {
        // Obtener los datos del cuerpo de la solicitud
        $data = json_decode(file_get_contents('php://input'), true);

        // Verificar si se han proporcionado los parámetros necesarios
        if (!isset($data['page']) || !isset($data['page_size']) || !isset($data['csv_file']) ) {
            error('Missing required parameters', 400);
        }

        // Obtener los parámetros de paginación
        $page          = $data['page'];
        $page_size     = $data['page_size'];

        // Obtener el nombre del archivo CSV y otros datos necesarios
        $csv_filename = $data['csv_file'];

        $offset       = Paginator::calcOffset($page, $page_size);

        $this->do_process($csv_filename, $offset, $page_size);

        // Responder con un mensaje de éxito
        response()->send(['message' => 'Page processed successfully']);
    }

    // Ajax
    function get_completion()
    {
       $data = [
        'completion' => get_transient('bzz-import_completion', 0)
       ];

       response()->send($data);
    }

    function index()
    {
        dd("Index of ". __CLASS__);                   
    }
}

