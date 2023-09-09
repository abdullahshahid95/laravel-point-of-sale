<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use App\Sale;
use App\Item;
use App\Rate;
use App\Customer;

class CustomersController extends Controller
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
        if(allowed(1, 'view'))
        {
            $searchValue = null;
            $customers = null;
    
            if($request->input('searchValue'))
            {
                $searchValue = $request->input('searchValue');
    
                $customers = Customer::where(DB::raw("LOCATE('{$searchValue}', name)"), '>', 0)
                                ->orWhere(DB::raw("LOCATE('{$searchValue}', phone)"), '>', 0)
                                ->orWhere(DB::raw("LOCATE('{$searchValue}', address)"), '>', 0)
                                ->latest()->paginate(50);
            }
            else
            {
                $customers = Customer::Where('id', '!=', 1)->latest()->paginate(50);
            }
    
            $sales = null;
    
            return view('customers.index', compact('customers', 'sales', 'searchValue'));
        }
        else
        {
            return redirect('/sale/create');
        }
    }

    public function filterOrders(Customer $customer)
    {
        $data = request()->validate([
            'fromDate' => 'required',
            'toDate' => 'required'
        ]);

        $fromDate = date('Y-m-d', strtotime($data['fromDate']));
        $toDate = date('Y-m-d', strtotime($data['toDate']));

        $orders = $customer->orders()->where([[DB::raw("date(created_at)"), '>=', $fromDate],
                            [DB::raw("date(created_at)"), '<=', $toDate]])->latest()->paginate(25);

        $total = DB::table('orders')
                        ->join('sales', 'orders.id', '=', 'sales.order_id')
                        ->where([[DB::raw("date(orders.created_at)"), '>=', $fromDate],
                                [DB::raw("date(orders.created_at)"), '<=', $toDate],
                                ['customer_id', '=', $customer->id]])
                        ->select(DB::raw("SUM(orders.total) AS totalOrders"), DB::raw("SUM(orders.balance) AS totalBalance"), DB::raw("SUM(orders.discount) AS totalDiscount"))->first();

        $total = (array)$total;
                                        
        return view('customers.show', compact('customer', 'orders', 'fromDate', 'toDate', 'total'));
    }

    public function create()
    {
        if(allowed(1, 'make'))
        {
            return view('customers.create');
        }
        else
        {
            return redirect('/sale/create');
        }
    }

    public function store()
    {
        if(allowed(1, 'make'))
        {
            $data = request()->validate([
                'name' => 'required',
                'phone' => '',
                'address' => '',
                'fromAjax' => ''
            ]);
    
            $customer = Customer::create(['name' => $data['name'], 
                                        'phone' => $data['phone'],
                                        'address' => $data['address']]);
    
            if(array_key_exists('fromAjax', $data))
                return $customer->id;
                
            return redirect("/customers");
        }
        else
        {
            return redirect('/sale/create');
        }
    }

    public function update(Customer $customer, Request $request)
    {
        if(allowed(1, 'edit'))
        {
            $data = request()->validate([
                'name' => 'required',
                'phone' => 'required',
                'address' => 'required',
                'redirect_url' => ''
            ]);
    
            $customer->update(['name' => $data['name'],
                            'phone' => $data['phone'],
                            'address' => $data['address'],]);
    
            return redirect($data['redirect_url']);
        }
        else
        {
            return redirect('/sale/create');
        }
    }

    public function show(Customer $customer)
    {
        $orders = $customer->orders()->paginate(25);

        $fromDate = '';
        $toDate = '';
        $total = '';

        return view('customers.show', compact('orders', 'customer', 'fromDate', 'toDate', 'total'));
    }

    public function destroy(Customer $customer)
    {
        if(allowed(1, 'remove'))
        {
            foreach($customer->orders as $order) 
            {
                $order->update(['customer_id' => 0]);
            }
    
            $customer->delete();
    
            return 'deleted';
        }
        else
        {
            return 0;
        }
    }
}

