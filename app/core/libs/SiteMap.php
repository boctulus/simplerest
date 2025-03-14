<?php

namespace simplerest\core\libs;

use \ReflectionClass;
use simplerest\core\WebRouter;
use simplerest\core\libs\Config;

class SiteMap
{
    /*  
        Array delle rotte del sito. 
        Le rotte saranno organizzate per verbo HTTP (ad es. GET, POST, …)    
    */
    protected $routes = [];

    /**
     * Array di pattern per escludere rotte dal sitemap.
     *
     * Esempio: ['admin/*', 'api/*']
     */
    protected $exclusions = [];

    /**
     * Carica le rotte dal WebRouter utilizzando Reflection.
     */
    public function fromRouter($exclusions = [])
    {
        $routerReflection = new ReflectionClass(WebRouter::class);
        $prop = $routerReflection->getProperty('routes');
        $prop->setAccessible(true);
        // Recupera il valore della proprietà statica "routes"
        $this->routes = $prop->getValue();

        if (!empty($exclusions)){
            $this->exclusions = $exclusions;
        }
    }

    /**
     * Imposta i pattern di esclusione per le rotte.
     *
     * @param array $exclusions Array di pattern, ad esempio ['admin/*']
     * @return self
     */
    public function setExclusions(array $exclusions): self
    {
        $this->exclusions = $exclusions;
        return $this;
    }

    /**
     * Genera un XML conforme allo standard sitemap.
     * Considera solo le rotte GET.
     *
     * @return string XML formattato della sitemap
     */
    public function generateXML(): string
    {
        // Creazione della struttura XML base per lo sitemap.
        $xml = new \SimpleXMLElement(
            '<?xml version="1.0" encoding="UTF-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"></urlset>'
        );

        // Ottiene la base URL dalla configurazione.
        $config  = Config::get();
        $baseUrl = isset($config['base_url']) ? rtrim($config['base_url'], '/') : '';

        // Se la base URL non è assoluta, la compongo utilizzando protocollo e host.
        if (!preg_match('/^https?:\/\//', $baseUrl)) {
            $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https" : "http";
            $host = $_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'];
            $baseUrl = $protocol . '://' . $host . $baseUrl;
        }

        // Considera solo le rotte GET
        if (isset($this->routes['GET'])) {
            foreach ($this->routes['GET'] as $uri => $callback) {
                // Verifica se il percorso corrisponde a uno dei pattern di esclusione
                $exclude = false;
                if (!empty($this->exclusions)) {
                    foreach ($this->exclusions as $pattern) {
                        // Utilizzo di fnmatch per confrontare il pattern con il percorso
                        if (fnmatch($pattern, $uri)) {
                            $exclude = true;
                            break;
                        }
                    }
                }
                if ($exclude) {
                    continue;
                }

                $url = $xml->addChild('url');
                // Costruisce l'URL completo, gestendo eventuali slash duplicati.
                $loc = $baseUrl . '/' . ltrim($uri, '/');
                $url->addChild('loc', htmlspecialchars($loc));
            }
        }

        // Formatta l'XML per una migliore leggibilità.
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;
        $dom->loadXML($xml->asXML());
        return $dom->saveXML();
    }

    /**
     * Aggiunge manualmente delle rotte al SiteMap.
     *
     * Il formato dell'array deve essere:
     * [
     *    "VERBO:/uri" => callback,
     *    "/altra/uri" => callback // in questo caso si assume GET come verbo
     * ]
     *
     * @param array $routes Array di rotte da aggiungere
     * @return self
     */
    public function fromArray(array $routes): self
    {
        $supportedVerbs = ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'];

        foreach ($routes as $routeKey => $callback) {
            if (strpos($routeKey, ':') !== false) {
                list($verb, $uri) = explode(':', $routeKey, 2);
                $verb = strtoupper(trim($verb));
                $uri  = trim($uri, '/');
            } else {
                $verb = 'GET';
                $uri  = trim($routeKey, '/');
            }

            if (in_array($verb, $supportedVerbs)) {
                if (!isset($this->routes[$verb])) {
                    $this->routes[$verb] = [];
                }
                // Aggiunge la rotta solo se non esiste già.
                if (!isset($this->routes[$verb][$uri])) {
                    $this->routes[$verb][$uri] = $callback;
                }
            }
        }
        return $this;
    }
}
