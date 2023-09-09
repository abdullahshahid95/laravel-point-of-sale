<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SpecificPrice extends Model
{
    protected $table = "specific_prices";
    
    protected $guarded = [];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
