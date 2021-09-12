#!/bin/bash
echo Unit Test ] ApiController 
vendor/bin/phpunit --bootstrap ./vendor/autoload.php ./tests/ApiTest.php