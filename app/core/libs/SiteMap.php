<?php

namespace simplerest\core\libs;

use \ReflectionClass;
use simplerest\core\WebRouter;
use simplerest\core\libs\Config;
use simplerest\core\exceptions\NotImplementedException;

/*
    @author Pablo Bozzolo
*/
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
     * Array de URLs manuales añadidas al sitemap
     */
    protected $customUrls = [];

    /**
     * Fecha por defecto para lastmod si no se proporciona
     */
    protected $defaultDate;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->defaultDate = date('Y-m-d');
    }

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

        return $this;
    }

    /**
     * Añade URLs manuales al sitemap desde un array
     * 
     * @param array $urls Array de URLs a añadir
     * @return self
     */
    public function fromArray(array $urls): self
    {
        foreach ($urls as $url) {
            if (is_string($url)) {
                $this->add($url);
            } elseif (is_array($url) && isset($url['loc'])) {
                $lastmod = $url['lastmod'] ?? null;
                $this->add($url['loc'], $lastmod);
            }
        }
        return $this;
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
     * @param bool $includeXmlHeader Incluir cabecera XML
     * @return string XML formattato della sitemap
     */
    public function generateXML(bool $includeXmlHeader = true): string
    {
        // Creazione della struttura XML base per lo sitemap.
        $xmlHeader = $includeXmlHeader ? '<?xml version="1.0" encoding="UTF-8"?>' : '';
        $xml = new \SimpleXMLElement(
            $xmlHeader . '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"></urlset>'
        );

        // Ottiene la base URL dalla configurazione.
        $config  = Config::get();
        $baseUrl = isset($config['base_url']) ? rtrim($config['base_url'], '/') : '';

        // Se la base URL non è assoluta, la compongo utilizzando protocollo e host.
        if (!preg_match('/^https?:\/\//', $baseUrl)) {
            $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https" : "http";
            $host = $_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'] ?? '';
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
                $url->addChild('lastmod', $this->defaultDate);
            }
        }

        // Añadir URLs personalizadas
        foreach ($this->customUrls as $customUrl) {
            $url = $xml->addChild('url');
            $url->addChild('loc', htmlspecialchars($customUrl['loc']));
            
            if (!empty($customUrl['lastmod'])) {
                $url->addChild('lastmod', $customUrl['lastmod']);
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
     * Esclude dal sitemap le rotte che contengono placeholder, ad esempio {id} o {num}.
     *
     * @return self
     */
    public function excludePlaceholdedRoutes(): self
    {
        if (isset($this->routes['GET'])) {
            foreach ($this->routes['GET'] as $uri => $callback) {
                if (strpos($uri, '{') !== false || strpos($uri, '}') !== false) {
                    unset($this->routes['GET'][$uri]);
                }
            }
        }
        return $this;
    }

    /**
     * Aggiunge manualmente una URL al SiteMap.
     *
     * @param string $url URL completa para añadir al sitemap
     * @param string|null $lastmod Fecha de última modificación en formato YYYY-MM-DD
     * @return self
     */
    public function add(string $url, ?string $lastmod = null): self
    {
        $this->customUrls[] = [
            'loc' => $url,
            'lastmod' => $lastmod ?? $this->defaultDate
        ];
        
        return $this;
    }

    /**
     * Establece la fecha por defecto para lastmod
     * 
     * @param string $date Fecha en formato YYYY-MM-DD
     * @return self
     */
    public function setDefaultDate(string $date): self
    {
        $this->defaultDate = $date;
        return $this;
    }

    /**
     * Guarda el sitemap en un archivo
     * 
     * @param string $filePath Ruta completa donde guardar el archivo
     * @return bool Éxito o fracaso de la operación
     */
    public function saveToFile(string $filePath): bool
    {
        $xml = $this->generateXML();
        return file_put_contents($filePath, $xml) !== false;
    }

    /**
     * Envía cabeceras XML y el contenido del sitemap
     * 
     * @return void
     */
    public function output(): void
    {
        if (!headers_sent()) {
            header('Content-Type: application/xml; charset=utf-8');
        }
        
        echo $this->generateXML();
    }
}