<?php

/*
    Archivo para probar el parser
*/

function sayHello() {
    echo "Hello!";
}

function sayBye(string $to) {
    echo "Hello $to";
}

function tb_prefix() {
    return "tb_";
}

function in_schema(array $props, string $table_name, ?string $tenant_id = null)
{  
    $sc = get_schema($table_name, $tenant_id);
    $attributes = array_keys($sc['attr_types']);

	if (empty($props))
		throw new \InvalidArgumentException("Attributes not found!");

	foreach ($props as $prop)
		if (!in_array($prop, $attributes)){
			return false; 
		}	
	
	return true;
}

class MyClass {
    public function doSomething() {
        echo "Doing something...";
    }

    public function doSomethingElse() {
        echo "Doing something...";
    }
}