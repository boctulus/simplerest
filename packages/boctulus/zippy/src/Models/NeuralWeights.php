<?php

namespace Boctulus\Zippy\Models;

use Boctulus\Simplerest\Core\Model;

/**
 * NeuralWeights Model
 *
 * Modelo para tabla neural_weights - almacena pesos de red neuronal
 * para clasificación de productos por palabras clave
 *
 * @author Pablo Bozzolo (boctulus)
 */
class NeuralWeights extends Model
{
    protected $connection   = 'zippy';
    protected $table_name   = 'neural_weights';
    protected $id_name      = 'id';
    protected $fillable     = [
        'word',
        'category_slug',
        'weight',
        'source',
        'usage_count',
        'last_used_at'
    ];
}
