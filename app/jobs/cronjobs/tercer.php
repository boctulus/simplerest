<?php

use simplerest\core\libs\CronJob;
use simplerest\core\libs\Files;

/*
    Tocaría correr un "php com cronos update" 

    Con eso se matarían todos los cronos actuales
    y se volvería a hacer un "scan" e iniciar todos los cronos
    incluyendo los nuevos y ya no los que se hubieran borrado / inactivado
*/
class TercerJob extends CronJob
{
	static protected $month;
    static protected $monthday;
	static protected $weekday;
	static protected $hour   = 22;
	static protected $minute;
	static protected $second = 5;

    // usar. Por defecto, al crear el workersh vendría en true
    static protected $is_active = true;

	function run(){
		// your logic here
		Files::logger(get_class());
	}
}
