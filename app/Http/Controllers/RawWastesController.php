<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Purchase;
use App\Item;

use App\RawWaste;
use App\InventoryDeduction;
use App\Rate;
use App\Unit;
use App\Configuration;

class RawWastesController extends Controller
{
    public function __construct()
    {
        if(strtotime(date('Y-m-d')) >= strtotime(posConfigurations()->expiry_date)
        || posConfigurations()->status == 0)
        {
            Configuration::find(1)->update(['status' => 0]);
            dd('Product expired. Contact system administrator.');
        }
        
        return $this->middleware('auth');
    }
    
    public function index()
    {
        if(allowed(8, 'view'))
        {
            $wastage = RawWaste::latest()->paginate(10);

            return view('raw-wastes.index', compact('wastage'));
        }
        else
        {
            return redirect('/sale/create');
        }
    }

    public function update(RawWaste $rawWaste)
    {
        if(allowed(8, 'edit'))
        {
            $cost = 0;
        
            if($rawWaste->item->rate->purchase_price > 0)
            {
                $cost = $rawWaste->item->rate->purchase_price * $rawWaste->quantity;
            }
            else
            {
                $cost = ($rawWaste->item->inventory->cost / $rawWaste->item->inventory->quantity) * $rawWaste->quantity;
            }
    
            $rawWaste->item->inventory->update(
                ['quantity' => $rawWaste->item->inventory->quantity + 
                                $rawWaste->quantity,
                'cost' => $rawWaste->item->inventory->cost + $cost]);
    
            $rawWaste->delete();
    
            return 'changed';
        }
        else
        {
            return 0;
        }
    }
}
