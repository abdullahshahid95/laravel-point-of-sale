<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Item;
use App\RawInventory;
use App\RawWaste;
use App\Unit;
use App\Configuration;

class RawInventoryController extends Controller
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
        if(allowed(7, 'view'))
        {
            if(posConfigurations()->maintain_inventory != 1)
            {
                return redirect('/');
            }
    
            $inventory = RawInventory::orderBy('id', 'ASC')->get();
    
            return view('raw-inventory.index', compact('inventory'));
        }
        else
        {
            return redirect('/sale/create');
        }
    }

    public function update(RawInventory $rawInventory, $flag)
    {
        if(allowed(7, 'edit'))
        {
            if(posConfigurations()->maintain_inventory != 1)
            {
                return redirect('/');
            }
            
            $data = request()->validate([
                'quantity' => 'required'
            ]);
    
            $cost = 0;
            
            if($rawInventory->item->rate->purchase_price > 0)
            {
                $cost = $rawInventory->item->rate->purchase_price * $data['quantity'];
            }
            else
            {
                $cost = ($rawInventory->cost / $rawInventory->quantity) * $data['quantity'];
            }
    
            if($flag == 1)
            {
                $rawInventory->update(['quantity' => $rawInventory->quantity + $data['quantity'],
                                        'cost' => $rawInventory->cost + $cost]);
            }
            else
            {
                $rawInventory->update(['quantity' => $rawInventory->quantity - $data['quantity'],
                                        'cost' => $rawInventory->cost - $cost]);
    
                RawWaste::create(['item_id' => $rawInventory->item_id, 'quantity' => $data['quantity']]);
            }
            
            return 'edited';
        }
        else
        {
            return 0;
        }
    }

    public function checkInventory()
    {
        // $inventory = DB::table('raw_inventories')
        //                 ->join('items', 'raw_inventories.item_id', '=', 'items.id')
        //                 ->where('raw_inventories.quantity', '<=', 'items.reorder_level')
        //                 ->select('items.id', 'items.name', 'items.reorder_level',
        //                         'raw_inventories.quantity')
        //                 ->get();

        $inventory = DB::select("select `items`.`name` AS `name`, `raw_inventories`.`quantity` AS `quantity`, `items`.`id`, `items`.`unit_id` AS `unitId`, `items`.`reorder_level` from `raw_inventories` inner join `items` on `raw_inventories`.`item_id` = `items`.`id` where `raw_inventories`.`quantity` <= `items`.`reorder_level` AND `items`.`status` = 1");

        return $inventory;
    }
}
