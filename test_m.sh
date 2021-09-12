#!/bin/bash
echo Unit Test ] Model 
vendor/bin/phpunit --bootstrap ./vendor/autoload.php ./tests/ModelTest.php
