<?php

use Boctulus\Simplerest\Core\Libs\Paginator;

/*
	Falta crea mecanismo para inyectarlo
*/
return [
	
	'paginator' => [
		'max_limit' => 50,
		'default_limit' => 10,
		'position' => Paginator::TOP,
		'params'   => [
			'pageSize' => 'size',
			'page'	   => 'page_num' // redefinido para WordPress
		],
		'formatter' => function ($row_count, $count, $current_page, $page_count, $page_size, $nextUrl){
			return [
				"last_page" => $page_count,
				'paginator' => [
					"total"       => $row_count, 
					"count"       => $count,
					"currentPage" => $current_page,
					"totalPages"  => $page_count,
					"pageSize"    => $page_size,
					"nextUrl"	  => $nextUrl
				],
			];
		},
	],

	// ...
];