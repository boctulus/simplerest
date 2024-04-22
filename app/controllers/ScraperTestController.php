<?php

namespace simplerest\controllers;

use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\DesiredCapabilities;

/*
    DE MOMENTO NO ME ESTA FUNCIONANDO CON PHP

    https://github.com/php-webdriver/php-webdriver

    Install:

    composer require php-webdriver/webdriver

    PODRIA requerir el driver para Java:

    java -jar selenium-server-standalone-3.141.59.jar

    Luego ver esto:

    https://stackoverflow.com/questions/10792403/how-do-i-get-chrome-working-with-selenium-using-php-webdriver
*/
class ScraperTestController 
{
    function __construct(){
        
        // Chromedriver (if started using --port=4444 as above)
        $host = 'http://localhost:4444/web/hub';

        $capabilities = DesiredCapabilities::chrome();

        // Add arguments via FirefoxOptions to start headless firefox
        $options = new ChromeOptions();
        $options->setExperimentalOption('w3c', false);

        $options->addArguments([
            '--no-sandbox',
            '--disable-dev-shm-usage',
            '--disable-extensions',
            '--disable-gpu',
            // '--headless'
        ]);

        $capabilities->setCapability(ChromeOptions::CAPABILITY, $options);
        $capabilities->setCapability('acceptSslCerts', false);


        // Chrome
        $driver = RemoteWebDriver::create($host, $capabilities);
    }
}

