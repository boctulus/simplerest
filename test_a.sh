#!/bin/bash
echo Unit Test ] Auth
vendor/bin/phpunit --bootstrap ./vendor/autoload.php ./tests/AuthTest.php