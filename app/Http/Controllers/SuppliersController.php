<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use App\Supplier;
use App\Purchase;

class SuppliersController extends Controller
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
        if(allowed(13, 'view'))
        {
            $searchValue = null;
            $suppliers = null;
    
            if($request->input('searchValue'))
            {
                $searchValue = $request->input('searchValue');
    
                $suppliers = Supplier::where(DB::raw("LOCATE('{$searchValue}', name)"), '>', 0)
                                ->orWhere(DB::raw("LOCATE('{$searchValue}', phone)"), '>', 0)
                                ->orWhere(DB::raw("LOCATE('{$searchValue}', address)"), '>', 0)
                                ->latest()->paginate(50);
            }
            else
            {
                $suppliers = Supplier::Where('id', '!=', 1)->latest()->paginate(50);
            }
    
            $purchases = null;
    
            return view('suppliers.index', compact('suppliers', 'purchases', 'searchValue'));
        }
        else
        {
            return redirect('/sale/create');
        }
    }

    public function filterOrders(Supplier $supplier)
    {
        $data = request()->validate([
            'fromDate' => 'required',
            'toDate' => 'required'
        ]);

        $fromDate = date('Y-m-d', strtotime($data['fromDate']));
        $toDate = date('Y-m-d', strtotime($data['toDate']));

        $orders = $supplier->orders()->where([[DB::raw("date(created_at)"), '>=', $fromDate],
                            [DB::raw("date(created_at)"), '<=', $toDate]])->latest()->paginate(25);

        $total = DB::table('purchase_orders')
                        ->join('purchases', 'purchase_orders.id', '=', 'purchases.purchase_order_id')
                        ->where([[DB::raw("date(purchase_orders.created_at)"), '>=', $fromDate],
                                [DB::raw("date(purchase_orders.created_at)"), '<=', $toDate],
                                ['supplier_id', '=', $supplier->id]])
                        ->select(DB::raw("SUM(purchase_orders.total) AS totalOrders"), DB::raw("SUM(purchase_orders.balance) AS totalBalance"))->first();

        $total = (array)$total;
                                        
        return view('suppliers.show', compact('supplier', 'orders', 'fromDate', 'toDate', 'total'));
    }

    public function create()
    {
        if(allowed(13, 'make'))
        {
            return view('suppliers.create');
        }
        else
        {
            return redirect('/sale/create');
        }
    }

    public function store()
    {
        if(allowed(13, 'make'))
        {
            $data = request()->validate([
                'name' => 'required',
                'phone' => '',
                'address' => '',
                'fromAjax' => ''
            ]);
    
            $supplier = Supplier::create(['name' => $data['name'], 
                                        'phone' => $data['phone'],
                                        'address' => $data['address']]);
    
            if(array_key_exists('fromAjax', $data))
                return $supplier->id;
    
            return redirect("/suppliers");
        }
        else
        {
            return redirect('/sale/create');
        }
    }

    public function update(Supplier $supplier, Request $request)
    {
        if(allowed(13, 'edit'))
        {
            $data = request()->validate([
                'name' => 'required',
                'phone' => 'required',
                'address' => 'required',
                'redirect_url' => ''
            ]);
    
            $supplier->update(['name' => $data['name'],
                            'phone' => $data['phone'],
                            'address' => $data['address'],]);
    
            return redirect($data['redirect_url']);
        }
        else
        {
            return redirect('/sale/create');
        }
    }

    public function show(Supplier $supplier)
    {
        $orders = $supplier->orders()->paginate(25);

        $fromDate = '';
        $toDate = '';
        $total = '';

        return view('suppliers.show', compact('orders', 'supplier', 'fromDate', 'toDate', 'total'));
    }

    public function destroy(Supplier $supplier)
    {
        if(allowed(13, 'remove'))
        {
            foreach($supplier->orders as $order) 
            {
                $order->update(['supplier_id' => 0]);
            }
    
            $supplier->delete();
    
            return 'deleted';
        }
        else
        {
            return 0;
        }
    }
}
