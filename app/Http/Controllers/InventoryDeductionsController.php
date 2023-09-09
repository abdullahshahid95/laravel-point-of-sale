<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Purchase;
use App\Item;


use App\Rate;
use App\PurchaseUnit;

class InventoryDeductionsController extends Controller
{
    public function index()
    {
        $inventoryDeductions = InventoryDeduction::latest()->paginate(10);

        return view('inventory-deductions.index', compact('inventoryDeductions'));
    }

    public function update(InventoryDeduction $inventoryDeduction)
    {
        $unit = $inventoryDeduction->unit;

        $inventoryDeduction->delete();
        
        $inventoryDeduction->product->inventory->update(
                                    ['quantity' => $inventoryDeduction->product->inventory->quantity + $unit->value]);

        return 'changed';
    }
}
