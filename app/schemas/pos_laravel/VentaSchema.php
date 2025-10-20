<?php

namespace Boctulus\Simplerest\Schemas\pos_laravel;

use Boctulus\Simplerest\Core\Interfaces\ISchema;

### IMPORTS

class VentaSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'		=> 'venta',

			'id_name'			=> 'idVenta',

			'fields'			=> ['idVenta', 'idEmpresa', 'idDocumentoDte', 'folio', 'rutCliente', 'idCliente', 'idMetodo_pago', 'forma_de_pago', 'tipo_compra', 'idImpuesto', 'idCaja_venta', 'idEstado_pago', 'idVenta_estado', 'idDocumento_estado', 'total_bruto', 'total_neto', 'impuesto', 'exento', 'afecto', 'enviadaalsii', 'timbre', 'token_id', 'id_usuario', 'created_at', 'updated_at', 'deleted_at'],

			'attr_types'		=> [
				'idVenta' => 'INT',
				'idEmpresa' => 'INT',
				'idDocumentoDte' => 'INT',
				'folio' => 'STR',
				'rutCliente' => 'STR',
				'idCliente' => 'INT',
				'idMetodo_pago' => 'INT',
				'forma_de_pago' => 'INT',
				'tipo_compra' => 'INT',
				'idImpuesto' => 'INT',
				'idCaja_venta' => 'INT',
				'idEstado_pago' => 'INT',
				'idVenta_estado' => 'INT',
				'idDocumento_estado' => 'INT',
				'total_bruto' => 'INT',
				'total_neto' => 'INT',
				'impuesto' => 'INT',
				'exento' => 'INT',
				'afecto' => 'INT',
				'enviadaalsii' => 'INT',
				'timbre' => 'STR',
				'token_id' => 'STR',
				'id_usuario' => 'INT',
				'created_at' => 'STR',
				'updated_at' => 'STR',
				'deleted_at' => 'STR'
			],

			'attr_type_detail'	=> [

			],

			'primary'			=> ['idVenta', 'idEmpresa', 'idDocumentoDte', 'idCliente', 'idMetodo_pago', 'idImpuesto', 'idCaja_venta', 'idEstado_pago', 'idVenta_estado', 'idDocumento_estado'],

			'autoincrement' 	=> 'idVenta',

			'nullable'			=> ['idVenta', 'folio', 'rutCliente', 'forma_de_pago', 'tipo_compra', 'idDocumento_estado', 'total_bruto', 'total_neto', 'impuesto', 'exento', 'afecto', 'enviadaalsii', 'token_id', 'id_usuario', 'created_at', 'updated_at', 'deleted_at'],

			'required'			=> ['idEmpresa', 'idDocumentoDte', 'idCliente', 'idMetodo_pago', 'idImpuesto', 'idCaja_venta', 'idEstado_pago', 'idVenta_estado', 'timbre'],

			'uniques'			=> [],

			'rules' 			=> [
				'idVenta' => ['type' => 'int'],
				'idEmpresa' => ['type' => 'int', 'required' => true],
				'idDocumentoDte' => ['type' => 'int', 'required' => true],
				'folio' => ['type' => 'str', 'max' => 50],
				'rutCliente' => ['type' => 'str', 'max' => 15],
				'idCliente' => ['type' => 'int', 'required' => true],
				'idMetodo_pago' => ['type' => 'int', 'required' => true],
				'forma_de_pago' => ['type' => 'int'],
				'tipo_compra' => ['type' => 'int'],
				'idImpuesto' => ['type' => 'int', 'required' => true],
				'idCaja_venta' => ['type' => 'int', 'required' => true],
				'idEstado_pago' => ['type' => 'int', 'required' => true],
				'idVenta_estado' => ['type' => 'int', 'required' => true],
				'idDocumento_estado' => ['type' => 'int'],
				'total_bruto' => ['type' => 'int'],
				'total_neto' => ['type' => 'int'],
				'impuesto' => ['type' => 'int'],
				'exento' => ['type' => 'int'],
				'afecto' => ['type' => 'int'],
				'enviadaalsii' => ['type' => 'int'],
				'timbre' => ['type' => 'str', 'required' => true],
				'token_id' => ['type' => 'str', 'max' => 50],
				'id_usuario' => ['type' => 'int'],
				'created_at' => ['type' => 'timestamp'],
				'updated_at' => ['type' => 'timestamp'],
				'deleted_at' => ['type' => 'datetime']
			],

			'fks' 				=> ['idCliente', 'idDocumentoDte', 'idEstado_pago', 'idImpuesto', 'idMetodo_pago', 'idVenta_estado'],

			'relationships' => [
				'cliente' => [
					['cliente.idCliente','venta.idCliente']
				],
				'documentodte' => [
					['documentodte.idDocumentoDte','venta.idDocumentoDte']
				],
				'estado_pago' => [
					['estado_pago.idEstado_pago','venta.idEstado_pago']
				],
				'impuesto' => [
					['impuesto.idImpuesto','venta.idImpuesto']
				],
				'metodo_pago' => [
					['metodo_pago.idMetodo_pago','venta.idMetodo_pago']
				],
				'venta_estado' => [
					['venta_estado.idVenta_estado','venta.idVenta_estado']
				],
				'detalle_venta' => [
					['detalle_venta.idVenta','venta.idVenta']
				],
				'referencia' => [
					['referencia.idVenta','venta.idVenta']
				]
			],

			'expanded_relationships' => array (
  'cliente' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'cliente',
        1 => 'idCliente',
      ),
      1 => 
      array (
        0 => 'venta',
        1 => 'idCliente',
      ),
    ),
  ),
  'documentodte' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'documentodte',
        1 => 'idDocumentoDte',
      ),
      1 => 
      array (
        0 => 'venta',
        1 => 'idDocumentoDte',
      ),
    ),
  ),
  'estado_pago' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'estado_pago',
        1 => 'idEstado_pago',
      ),
      1 => 
      array (
        0 => 'venta',
        1 => 'idEstado_pago',
      ),
    ),
  ),
  'impuesto' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'impuesto',
        1 => 'idImpuesto',
      ),
      1 => 
      array (
        0 => 'venta',
        1 => 'idImpuesto',
      ),
    ),
  ),
  'metodo_pago' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'metodo_pago',
        1 => 'idMetodo_pago',
      ),
      1 => 
      array (
        0 => 'venta',
        1 => 'idMetodo_pago',
      ),
    ),
  ),
  'venta_estado' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'venta_estado',
        1 => 'idVenta_estado',
      ),
      1 => 
      array (
        0 => 'venta',
        1 => 'idVenta_estado',
      ),
    ),
  ),
  'detalle_venta' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'detalle_venta',
        1 => 'idVenta',
      ),
      1 => 
      array (
        0 => 'venta',
        1 => 'idVenta',
      ),
    ),
  ),
  'referencia' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'referencia',
        1 => 'idVenta',
      ),
      1 => 
      array (
        0 => 'venta',
        1 => 'idVenta',
      ),
    ),
  ),
),

			'relationships_from' => [
				'cliente' => [
					['cliente.idCliente','venta.idCliente']
				],
				'documentodte' => [
					['documentodte.idDocumentoDte','venta.idDocumentoDte']
				],
				'estado_pago' => [
					['estado_pago.idEstado_pago','venta.idEstado_pago']
				],
				'impuesto' => [
					['impuesto.idImpuesto','venta.idImpuesto']
				],
				'metodo_pago' => [
					['metodo_pago.idMetodo_pago','venta.idMetodo_pago']
				],
				'venta_estado' => [
					['venta_estado.idVenta_estado','venta.idVenta_estado']
				]
			],

			'expanded_relationships_from' => array (
  'cliente' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'cliente',
        1 => 'idCliente',
      ),
      1 => 
      array (
        0 => 'venta',
        1 => 'idCliente',
      ),
    ),
  ),
  'documentodte' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'documentodte',
        1 => 'idDocumentoDte',
      ),
      1 => 
      array (
        0 => 'venta',
        1 => 'idDocumentoDte',
      ),
    ),
  ),
  'estado_pago' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'estado_pago',
        1 => 'idEstado_pago',
      ),
      1 => 
      array (
        0 => 'venta',
        1 => 'idEstado_pago',
      ),
    ),
  ),
  'impuesto' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'impuesto',
        1 => 'idImpuesto',
      ),
      1 => 
      array (
        0 => 'venta',
        1 => 'idImpuesto',
      ),
    ),
  ),
  'metodo_pago' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'metodo_pago',
        1 => 'idMetodo_pago',
      ),
      1 => 
      array (
        0 => 'venta',
        1 => 'idMetodo_pago',
      ),
    ),
  ),
  'venta_estado' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'venta_estado',
        1 => 'idVenta_estado',
      ),
      1 => 
      array (
        0 => 'venta',
        1 => 'idVenta_estado',
      ),
    ),
  ),
)
		];
	}	
}

