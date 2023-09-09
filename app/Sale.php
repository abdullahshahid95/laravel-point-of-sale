<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    // protected $fillable = ["order_id", "unit_id", "price", "unit_price", "status", "item_id"];
    protected $guarded = [];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
