<?php

namespace simplerest\controllers;

use simplerest\core\traits\PagesTrait;
use simplerest\controllers\MyController;

class AdminController extends MyController
{    
	use PagesTrait;

	// 'tabulator', 'main',...
	public $default_page  = 'tabulator';

	public $tpl           = 'templates/adminlte_tpl.php'; //
	public $tpl_params    = [
		'brand_name' => 'Planex',
		'logo'       => 'img/planex_logo.png',
		'logo_alt'   => 'Planex'
	];	
	
	// ..
}
