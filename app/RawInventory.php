<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RawInventory extends Model
{
    protected $guarded = [];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
