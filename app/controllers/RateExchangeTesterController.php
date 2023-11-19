<?php

namespace simplerest\controllers;

use simplerest\controllers\MyController;
use boctulus\SW\libs\currency\VercelARS;
use boctulus\SW\libs\currency\ExchangeRate;

class RateExchangeTesterController extends MyController
{
      function index()
    {       
        try {
            dd(
            	VercelARS::convert(), 
            	VercelARS::updatedAt()
            );

            dd(
                ExchangeRate::convert('COP'), 
                ExchangeRate::updatedAt()
            );

            // dd(ExchangeRate::data());

        } catch (\Throwable $e){
            dd($e->getMessage());
        }
                
    }
}

