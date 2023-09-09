<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $guarded = [];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    public function inventory()
    {
        return $this->hasOne(RawInventory::class);
    }

    public function wastage()
    {
        return $this->hasMany(RawWaste::class);
    }

    public function rate()
    {
        return $this->hasOne(Rate::class);
    }

    public function specificPrices()
    {
        return $this->hasMany(SpecificPrices::class);
    }
}
