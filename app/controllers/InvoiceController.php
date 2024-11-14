<?php

namespace simplerest\controllers;

use simplerest\core\controllers\Controller;
use simplerest\core\libs\DB;
use simplerest\core\libs\Strings;
use simplerest\core\traits\TimeExecutionTrait;
use simplerest\libs\InvoiceGenerator;

class InvoiceController extends Controller
{
    function __construct() { parent::__construct(); }

    function index()
    {
        /* 
            DATA
        */

        $hour_rate     = 20;
        $is_fully_paid = true;

        $header = [
            'sold_to' => 'Distribuidora Relmotor Ltda',
            'business_style' => 'Distributor',
            'tin' => '77234743-K (RUT CHILE)',
            'address' => 'Santiago. Chile',
            'date' => '2024-11-14',
            'terms' => null, // Ej: "Net 30"
            'osca_pwd_id' => null
        ];
        
        $rows = [
            [
                'qty'  => 185,
                'unit' => 'hours',
                'articles' => 'Custom Advanced Ultra-fast Advanced Search Engine',
                'unit_price' => $hour_rate
            ],
            [
                'qty'  => 26.5,
                'unit' => 'hours',
                'articles' => 'PDF Catalog Exporter',
                'unit_price' => $hour_rate
            ],
        ];

        /*
            CALCULATION
        */

        $total_qty = 0;
        $total     = 0;
        foreach ($rows as $ix => $row){
            $rows[$ix]['amount'] = $rows[$ix]['qty'] * $hour_rate;
            $total     += $rows[$ix]['amount'];
            $total_qty += $rows[$ix]['qty'];
        }

        $total = $total_qty * $hour_rate;

        $totals = [
            'total_sales'   => $total,
            'discount'      => 0,
            'total_due'     => $total,
            'is_fully_paid' => $is_fully_paid
        ];
    
        // Generate PDF
        $content = InvoiceGenerator::render($header, $rows, $totals);
        render($content);    
    }
}

