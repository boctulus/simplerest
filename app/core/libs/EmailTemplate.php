<?php

namespace Boctulus\Simplerest\Core\Libs;

use Boctulus\Simplerest\Core\Libs\Strings;

class EmailTemplate
{
    /*
        Recibe un texto y le aplica algunos tags de HTML para
        ser utilizado como body en un correo.

        Ej:

        email    = 'xxxxx@domain.com';
        $subject = 'Bla bla';
        $content = 'Hola! 

        Este es un contenido de <b>prueba en negrita</b> #'.rand(9999,999999) . '

        Un saludo!';

        $logo     = 'assets/img/logo.png';
        $logo_url = "/sales-agent-coupons-1/$logo";
        
        $content  = EmailTemplate::formatContentWithHeader($content);

        $body     = get_view('email/simple_with_logo', compact('email', 'subject', 'content', 'logo_url'));

        $res      = Mail::send($email, $subject, $body);
    */
    static function formatContentWithHeader($content) {
        // Separar las líneas del contenido
        $lines = explode("\n", $content);

        // Tomar la primera línea y convertirla en un <h2>
        $header = '<h2 style="color: #333;">' . array_shift($lines) . '</h2>';
    
        // Aplicar estilos al resto de las líneas
        $formattedLines = array_map(function ($line) {
            return empty(trim($line)) ? '' : '<p style="color: #555;">' . $line . '</p>';
        }, $lines);
    
        // Combinar la línea del encabezado y las líneas formateadas
        $formattedContent = $header . implode("\n", $formattedLines);
    
        return $formattedContent;
    }
}

