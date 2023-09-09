<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Item;
use App\Rate;
use App\Configuration;

class RatesController extends Controller
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
        $rates = Rate::orderBy('id', 'ASC')->get();
        
        return view('rates.index', compact('rates'));
    }

    public function edit()
    {
        $rates = Rate::orderBy('id', 'ASC')->get();

        return view('rates.edit', compact('rates'));
    }

    public function update()
    {
        $data = request()->validate([
            'item_id' => '',
            'sale_price' => ''
        ]);

        for($i = 0; $i < sizeof($data['sale_price']); $i++)
        {
            DB::table('rates')
                ->where('item_id', $data['item_id'][$i])
                ->update(['sale_price' => $data['sale_price'][$i]]);
        }

        return redirect('/rates');
    }
}
