<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\PurchaseOrder;
use App\Purchase;
use App\Item;

use App\RawInventory;
use App\RawWaste;
use App\Unit;
use App\Rate;
use App\Customer;
use App\Configuration;
use App\SpecificPrice;

class SpecificPricesController extends Controller
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

    public function index(Request $request)
    {
        if(allowed(15, 'view'))
        {
            $customers = Customer::all();
            $items = Item::all();
    
            if($request->input('searchValue'))
            {
                $searchValue = $request->input('searchValue');
    
                $specificPrices = DB::table('specific_prices')
                                ->join('items', 'specific_prices.item_id', '=', 'items.id')
                                ->join('customers', 'specific_prices.customer_id', '=', 'customers.id')
                                ->join('rates', 'rates.item_id', '=', 'items.id')
                                ->where(DB::raw("LOCATE('{$searchValue}', items.name)"), '>', 0)
                                ->orWhere(DB::raw("LOCATE('{$searchValue}', customers.name)"), '>', 0)
                                ->select('specific_prices.id AS csp_id', 'items.id AS item_id', 'items.unit_id AS unit_id', 'items.name AS item_name', 'customers.id AS customer_id', 'customers.name AS customer_name', 'rates.sale_price AS normal_price',
                                'specific_prices.sale_price AS customer_price')
                                ->orderBy('specific_prices.id', 'ASC')
                                ->paginate(10);
    
                return view('csp.index', compact('specificPrices', 'items', 'customers', 'searchValue'));
            }
    
            $searchValue = null;
    
            $specificPrices = DB::table('specific_prices')
                            ->join('items', 'specific_prices.item_id', '=', 'items.id')
                            ->join('customers', 'specific_prices.customer_id', '=', 'customers.id')
                            ->join('rates', 'rates.item_id', '=', 'items.id')
                            ->select('specific_prices.id AS csp_id', 'items.id AS item_id', 'items.unit_id AS unit_id', 'items.name AS item_name', 'customers.id AS customer_id', 'customers.name AS customer_name', 'rates.sale_price AS normal_price',
                            'specific_prices.sale_price AS customer_price')
                            ->orderBy('specific_prices.id', 'ASC')
                            ->paginate(10);
            
            return view('csp.index', compact('specificPrices', 'items', 'customers', 'searchValue'));    
        }
        else
        {
            return redirect('/sale/create');
        }
    }

    public function create()
    {
        if(allowed(15, 'make'))
        {
            return view('csp.create');
        }
        else
        {
            return redirect('/sale/create');
        }
    }

    public function getCSPData()
    {
        $customers = Customer::all();
        $items = DB::table('items')
                    ->join('rates', 'items.id', '=', 'rates.item_id')
                    ->select('items.id AS item_id', 'items.name AS item_name', 'items.unit_id AS unit_id',
                            'rates.sale_price AS normal_price')
                    ->where('items.status', 1)
                    ->get();

        return json_encode(['customers' => $customers,
                            'items' => $items]);
    }

    public function store(Request $request)
    {
        if(allowed(15, 'make'))
        {
            $data = json_decode($request->getContent(), true);

            $csp = $data['selectedCustomers'];
    
            $alreadyExist = '';
    
            for($i = 0; $i < sizeof($csp); $i++)
            {
                $alreadyExist = SpecificPrice::where([['customer_id', '=', $csp[$i]['itemId']], 
                                                ['item_id', '=', $csp[$i]['customerId']]])->first();
                                                
                if($alreadyExist)
                {
                    break;
                }
            }
    
            if($alreadyExist)
            {
                return json_encode([
                    'message' => 110
                ]);
            }
    
            for($i = 0; $i < sizeof($csp); $i++)
            {
                SpecificPrice::create(['item_id' => $csp[$i]['itemId'],
                                        'customer_id' => $csp[$i]['customerId'],
                                        'sale_price' => $csp[$i]['specialPrice']]);
            }
    
            return json_encode([
                'message' => 1
            ]);
        }
        else
        {
            return 0;
        }
    }

    public function update(SpecificPrice $specificPrice, Request $request)
    {
        if(allowed(15, 'edit'))
        {
            $data = request()->validate([
                'customer_id' => 'required',
                'item_id' => 'required',
                'sale_price' => 'required',
                'redirect_url' => ''
            ]);
    
            $exists = SpecificPrice::where([['id', '!=', $specificPrice->id], 
                                            ['customer_id', '=', $data['customer_id']], 
                                            ['item_id', '=', $data['item_id']]])->first();
            if($exists)
            {
                return redirect($data['redirect_url'])->withErrors(['message' => 'This customer is already assigned special price.']); 
            }
    
            $specificPrice->update(['customer_id' => $data['customer_id'],
                                    'item_id' => $data['item_id'],
                                    'sale_price' => $data['sale_price'],
                                ]);
    
            return redirect($data['redirect_url']);
        }
        else
        {
            return redirect('/sale/create');
        }
    }

    public function destroy(SpecificPrice $specificPrice)
    {
        if(allowed(15, 'remove'))
        {
            $specificPrice->delete();

            return 'deleted';
        }
        else
        {
            return 0;
        }
    }
}
