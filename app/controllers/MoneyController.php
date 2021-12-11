<?php

namespace simplerest\controllers;

use simplerest\controllers\MyController;
use simplerest\libs\Url;

class MoneyController extends MyController
{

    function swap(){

        // Build Swap
        $swap = (new \Swap\Builder())
            ->add('european_central_bank')
            ->add('national_bank_of_romania')
            ->add('central_bank_of_republic_turkey')
            ->add('central_bank_of_czech_republic')
            ->add('russian_central_bank')
            ->add('bulgarian_national_bank')
            ->add('webservicex')
        ->build();
            
        // Get the latest EUR/USD rate
        $rate = $swap->latest('EUR/USD');
        
        // 1.129
        d($rate->getValue(), 'EUR/USD');

        // 2016-08-26
        $rate->getDate()->format('Y-m-d');

        // Get the EUR/USD rate 15 days ago
        $rate = $swap->historical('EUR/USD', (new \DateTime())->modify('-15 days'));
    }
    
    /*
        Dolar TRM - 
        DataSource: API Banco de la República (de Colombia)
    */
    function dolar(){
        $res = Url::consume_api('https://totoro.banrep.gov.co/estadisticas-economicas/rest/consultaDatosService/consultaMercadoCambiario', 'GET');
        
        if ($res['http_code'] != 200){
            throw new \Exception("Error: ". $res['code'] . ' -code: '. $res['code']);
        }

        $data  = $res['data'];
        $final = $data[count($data)-1];
        dd($final[1], "DOLAR/COP (TRM) - VALOR FINAL ". date("Y-m-d H:i:s", substr($final[0], 0, 10)));
    }

    
    function euro(){
        $res = Url::consume_api('https://totoro.banrep.gov.co/estadisticas-economicas/rest/consultaDatosService/consultaMercadoCambiario', 'GET');
        
        if ($res['http_code'] != 200){
            throw new \Exception("Error: ". $res['code'] . ' -code: '. $res['code']);
        }

        $data    = $res['data'];
        $final  = $data[count($data)-1];
        $copusd = $final[1];
        
         // Build Swap
        $swap = (new \Swap\Builder())
        ->add('european_central_bank')
        ->add('national_bank_of_romania')
        ->add('central_bank_of_republic_turkey')
        ->add('central_bank_of_czech_republic')
        ->add('russian_central_bank')
        ->add('bulgarian_national_bank')
        ->add('webservicex')
        ->build();
            
        // Get the latest EUR/USD rate
        $rate = ($swap->latest('EUR/USD'))->getValue();

        $copeur = $copusd * $rate;

        dd($copeur, "EUR/COP - VALOR FINAL ". date("Y-m-d H:i:s", substr($final[0], 0, 10)));
    }
}

