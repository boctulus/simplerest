<?php

/**
 * PSR-7 Helper Functions
 *
 * Convenience functions for creating PSR-7 compatible objects
 *
 * @author Pablo Bozzolo (boctulus)
 */

use Boctulus\Simplerest\Core\Psr7\ServerRequestAdapter;
use Boctulus\Simplerest\Core\Psr7\ResponseAdapter;
use Boctulus\Simplerest\Core\Psr7\StreamAdapter;
use Boctulus\Simplerest\Core\Psr7\UriAdapter;
use Boctulus\Simplerest\Core\Request;
use Boctulus\Simplerest\Core\Response;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

/**
 * Get a PSR-7 ServerRequest adapter from the current Request
 *
 * This creates a PSR-7 compliant ServerRequestInterface that wraps
 * the current SimpleRest Request instance
 *
 * @return ServerRequestInterface
 */
function psr7_request(): ServerRequestInterface
{
    return new ServerRequestAdapter(Request::getInstance());
}

/**
 * Get a PSR-7 Response adapter from the current Response
 *
 * This creates a PSR-7 compliant ResponseInterface that wraps
 * the current SimpleRest Response instance
 *
 * @param Response|null $response Optional Response instance to wrap
 * @return ResponseInterface
 */
function psr7_response(?Response $response = null): ResponseInterface
{
    return new ResponseAdapter($response);
}

/**
 * Create a PSR-7 Stream from string, array, or resource
 *
 * @param string|array|resource $body
 * @return StreamInterface
 */
function psr7_stream($body = ''): StreamInterface
{
    return new StreamAdapter($body);
}

/**
 * Create a PSR-7 URI from string
 *
 * @param string $uri
 * @return UriInterface
 */
function psr7_uri(string $uri = ''): UriInterface
{
    return new UriAdapter($uri);
}

/**
 * Create a PSR-7 JSON response
 *
 * Convenience function for creating JSON responses
 *
 * @param mixed $data Data to encode as JSON
 * @param int $status HTTP status code
 * @param array $headers Additional headers
 * @return ResponseInterface
 */
function psr7_json($data, int $status = 200, array $headers = []): ResponseInterface
{
    $response = psr7_response();
    $response = $response->withJson($data, $status);

    foreach ($headers as $name => $value) {
        $response = $response->withHeader($name, $value);
    }

    return $response;
}

/**
 * Create a PSR-7 redirect response
 *
 * @param string $url URL to redirect to
 * @param int $status HTTP status code (301, 302, 307, 308)
 * @return ResponseInterface
 */
function psr7_redirect(string $url, int $status = 302): ResponseInterface
{
    return psr7_response()
        ->withStatus($status)
        ->withHeader('Location', $url);
}

/**
 * Create a PSR-7 HTML response
 *
 * @param string $html HTML content
 * @param int $status HTTP status code
 * @return ResponseInterface
 */
function psr7_html(string $html, int $status = 200): ResponseInterface
{
    return psr7_response()
        ->withStatus($status)
        ->withHeader('Content-Type', 'text/html; charset=utf-8')
        ->withBody(psr7_stream($html));
}

/**
 * Create a PSR-7 text response
 *
 * @param string $text Plain text content
 * @param int $status HTTP status code
 * @return ResponseInterface
 */
function psr7_text(string $text, int $status = 200): ResponseInterface
{
    return psr7_response()
        ->withStatus($status)
        ->withHeader('Content-Type', 'text/plain; charset=utf-8')
        ->withBody(psr7_stream($text));
}
