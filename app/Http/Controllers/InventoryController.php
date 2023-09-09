<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Item;
use App\Unit;

class InventoryController extends Controller
{
    public function index()
    {
        $inventory = Inventory::orderBy('id', 'ASC')->get();

        return view('inventory.index', compact('inventory'));
    }

    public function update(Inventory $inventory)
    {
        $data = request()->validate([
            'quantity' => 'required'
        ]);

        $inventory->update(['quantity' => $inventory->quantity - $data['quantity']]);
        
        $unitName = '';

        if($inventory->product->unit == 'kg')
        {
            $unitName = 'kilo';
        }
        elseif($inventory->product->unit == 'gaddi')
        {
            $unitName = 'gaddi';
        }
        else
        {
            if($data['quantity'] % 12 == 0)
            {
                $unitName = 'darjan';

                $data['quantity'] = $data['quantity'] / 12;
            }
            else
            {
                $unitName = '';
            }
        }

        $unit = Unit::where([['name', '=', $data['quantity'] . ' ' . $unitName],
        ['type', '=', $inventory->product->unit]])->first();

        if(!$unit)
        {
            $unit = Unit::create(['name' => $data['quantity'],
            'value' => $data['quantity'], 'type' => $inventory->product->unit]); //getting quantity of deducted item
        }

        InventoryDeduction::create(['item_id' => $inventory->product_id, 'unit_id' => $unit->id]);
        
        return 'edited';
    }
}
