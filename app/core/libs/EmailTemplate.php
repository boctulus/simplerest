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

    /**
     * Process a template view with data
     * @param string $template_name Template name (e.g., 'email/notification')
     * @param array $vars Variables to pass to template
     * @return string|false Rendered HTML content or false on error
     */
    static function renderTemplate($template_name, $vars = []) {
        try {
            // Render the template using get_view
            $content = get_view($template_name, $vars);
            return $content;
        } catch (\Exception $e) {
            Logger::log("Error rendering email template '$template_name': " . $e->getMessage());
            return false;
        }
    }

    /**
     * Generate a simple HTML list from array data
     * @param array $data Data array
     * @param string $title Optional title for the content
     * @return string HTML content
     */
    static function generateSimpleList($data, $title = 'Data') {
        $content = "<h2>$title</h2>\n\n";
        $content .= "<ul>\n";

        foreach ($data as $key => $value) {
            $label = ucfirst(str_replace('_', ' ', $key));

            if (is_array($value)) {
                $value = implode(', ', $value);
            }

            $content .= "<li><strong>$label:</strong> " . htmlspecialchars($value) . "</li>\n";
        }

        $content .= "</ul>\n\n";
        return $content;
    }
}

