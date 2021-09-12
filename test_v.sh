#!/bin/bash
echo Unit Test ] Validator 
vendor/bin/phpunit --bootstrap ./vendor/autoload.php ./tests/ValidatorTest.php