<?php

declare(strict_types=1);
### Function: Polyfill array_key_first() for PHP < 7.3
if ( ! function_exists( 'array_key_first' ) ) {
	function array_key_first( $arr ) {
		foreach( $arr as $key => $unused ) {
			return $key;
		}
		return null;
	}
}

