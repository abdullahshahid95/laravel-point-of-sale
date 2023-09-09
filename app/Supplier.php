<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $guarded = [];

    public function orders()
    {
        return $this->hasMany(PurchaseOrder::class);
    }
}
