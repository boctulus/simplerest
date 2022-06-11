<?php

function is_cli(){
	return (php_sapi_name() == 'cli');
}

function is_unix(){
	return (DIRECTORY_SEPARATOR === '/');
}