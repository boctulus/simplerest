<?php

namespace simplerest\controllers;

use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Firefox\FirefoxOptions;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\DesiredCapabilities;

/*
    DE MOMENTO NO ME ESTA FUNCIONANDO CON PHP

    https://github.com/php-webdriver/php-webdriver

    Install:

    composer require php-webdriver/webdriver

    y correr el driver:

    D:\selenium> java -jar .\selenium-server-standalone-3.9.1.jar
    
*/
class ScraperTestController
{

    function firefox_init()
    {
        $host = 'http://localhost:4444/web/hub';

        $capabilities = DesiredCapabilities::firefox();

        $options = new FirefoxOptions();

        $options->addArguments([
            '--no-sandbox',
            '--disable-dev-shm-usage',
            '--disable-extensions',
            '--disable-gpu',
            // '--headless'
        ]);

        $capabilities->setCapability(FirefoxOptions::CAPABILITY, $options);
        $capabilities->setCapability('acceptSslCerts', false);


        // Firefox
        $driver = RemoteWebDriver::create($host, $capabilities);
    }
    
    function chrome_init()
    {
        $host = 'http://localhost:4444/web/hub';

        $capabilities = DesiredCapabilities::chrome();

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

