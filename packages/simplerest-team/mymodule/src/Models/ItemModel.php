<?php

namespace SimplerestTeam\Mymodule\Models;

use Boctulus\Simplerest\Core\Model;

/**
 * Modelo para la tabla mymodule_items
 */
class ItemModel extends Model
{
    protected $table = 'mymodule_items';

    protected $fillable = [
        'name',
        'description',
        'active',
        'price',
        'quantity'
    ];

    protected $hidden = [];

    /**
     * Obtener todos los items activos
     */
    public function getActive()
    {
        return $this->where(['active' => true])->get();
    }

    /**
     * Obtener items por precio
     */
    public function getByPriceRange($min, $max)
    {
        return $this->whereBetween('price', [$min, $max])->get();
    }

    /**
     * Incrementar cantidad
     */
    public function incrementQuantity($id, $amount = 1)
    {
        $item = $this->find($id);
        if ($item) {
            $item->quantity += $amount;
            return $item->save();
        }
        return false;
    }

    /**
     * Decrementar cantidad
     */
    public function decrementQuantity($id, $amount = 1)
    {
        $item = $this->find($id);
        if ($item && $item->quantity >= $amount) {
            $item->quantity -= $amount;
            return $item->save();
        }
        return false;
    }
}
