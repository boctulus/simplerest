<?php

namespace simplerest\libs;

use simplerest\core\libs\Strings;

class InvoiceGenerator {
    /**
     * Generates an invoice
     * @param array $header Invoice header data
     * @param array $rows Invoice line items
     * @return string Generated  content
     */
    public static function render(array $header, array $rows, $totals)
    {
        css_file(VIEWS_PATH . 'bir_half_size/css/styles.css');
   
        return get_view(VIEWS_PATH . 'bir_half_size/invoice_layout.php', compact([ 'header', 'rows', 'totals' ]));
    }
}