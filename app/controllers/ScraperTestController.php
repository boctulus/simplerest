<?php

namespace simplerest\controllers;

use simplerest\controllers\MyController;
use Symfony\Component\DomCrawler\Crawler;

/*
    Dependencia:

    composer require symfony/dom-crawler
    composer require symfony/css-selector
*/
class ScraperTestController extends MyController
{
    function index(){
        $this->scrape();
    }

    function scrape()
    {
        // Tu HTML de ejemplo
        $html = '
        <tr class="woocommerce-orders-table__row woocommerce-orders-table__row--status-processing order">
            <td class="woocommerce-orders-table__cell woocommerce-orders-table__cell-order-number" data-title="Pedido">
                <a href="http://woo5.lan/my-account/view-order/923/">#923</a>
            </td>
            <td class="woocommerce-orders-table__cell woocommerce-orders-table__cell-order-number" data-title="Pedido">
                <a href="http://woo5.lan/my-account/view-order/924/">#924</a>
            </td>
            <td class="woocommerce-orders-table__cell woocommerce-orders-table__cell-order-number" data-title="Pedido">
                <a href="http://woo5.lan/my-account/view-order/925/">#925</a>
            </td>
        </tr>';

        // Crear una instancia de Crawler
        $crawler = new Crawler($html);

        // Obtener todos los números de orden
        $orders = $crawler->filter('td.woocommerce-orders-table__cell-order-number a')->each(function (Crawler $node, $i) {
            return $node->text();
        });

        // Imprimir los números de orden
        foreach ($orders as $order) {
           dd($order);
        }           
    }
}

