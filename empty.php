<?php

	var_dump(!'');  			// true
	var_dump(empty(''));		// true

	var_dump(!0);				// true
	var_dump(empty(0));			// true

	var_dump(!'0');				// true
	var_dump(empty('0'));		// true

	var_dump(!false);			// true
	var_dump(empty(false));		// true

	var_dump(!null);			// true
	var_dump(empty(null));		// true

	var_dump(!array()); 		// true
	var_dump(empty(array()));	// true
