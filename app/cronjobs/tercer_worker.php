<?php

use simplerest\core\libs\BackgroundService;
use simplerest\core\libs\Files;

/*
    Tocaría correr un "php com cronos update" 

    Con eso se matarían todos los cronos actuales
    y se volvería a hacer un "scan" e iniciar todos los cronos
    incluyendo los nuevos y ya no los que se hubieran borrado / inactivado
*/
class TercerWorker extends BackgroundService 
{
	static protected $month;
    static protected $monthday;
	static protected $weekday;
	static protected $hour   = 21;
	static protected $minute = 5;
	static protected $second;

    // usar. Por defecto, al crear el worker vendría en true
    static protected $is_active = true;

	function start(){
		// your logic here
		Files::logger(get_class());
	}

	function stop(){
		// your logic here
	}
}
