<?php

namespace simplerest\core\libs;

use simplerest\core\libs\XML;

class HTML extends XML
{
    /*
		Puede usarse para remover <head>, <footer>, <style> y <script>
	*/
	public static function removeTags(string $page, array|string $tag) {
        if (is_string($tag)) {
            // Si se proporciona un solo tag como string, convertirlo a un array de un solo elemento
            $tag = [$tag];
        }

        foreach ($tag as $t) {
            $pattern = "/<$t\b[^>]*>(.*?)<\/$t>/si";
            $page = preg_replace($pattern, '', $page);
        }

        return $page;
    }

	static function removeCSS(string $page, bool $remove_style_sections = true, bool $remove_css_inline = true, bool $remove_css_in_class_attributes = true) {
		// Eliminar CSS entre etiquetas <style></style>
		if ($remove_style_sections) {
			$page = preg_replace('/<style\b[^>]*>(.*?)<\/style>/is', '', $page);
		}
		
		// Eliminar CSS inline dentro del atributo style=""
		if ($remove_css_inline) {
			$page = preg_replace('/style="[^"]*"/i', '', $page);
		}
		
		// Eliminar las clases de CSS de Boostrap y otros frameworks de CSS dentro de atributos class=""
		if ($remove_css_in_class_attributes) {
			// FALTA IMPLEMENTAR DADA UNA LISTA DE CLASES CSS
		}
		
		return $page;
	}

	/*
		Util para eliminar eventos de JS y atributos como style y class
	*/
	public static function removeHTMLAttributes(string $page, array|string $attr): string {
        /*
            Eliminar todas las ocurrencias del atributo o atributos especificados.

            Ej:

            Strings::removeHTMLAttributes($html, 'onclick');
            Strings::removeHTMLAttributes($html, ['style', 'class']);

            Nota:

            Debe ser insensible a mayúsculas y minúsculas. Ej: "onClick" y "onclick"
        */

        // Convertir el atributo o atributos a un array si es un string
        if (!is_array($attr)) {
            $attr = [$attr];
        }

        // Recorrer los atributos y eliminar todas las ocurrencias en la página
        foreach ($attr as $attribute) {
            $page = preg_replace("/$attribute=\"[^\"]*\"/i", '', $page);
            $page = preg_replace("/$attribute='[^']*'/i", '', $page);
        }

        return $page;
    }

}

