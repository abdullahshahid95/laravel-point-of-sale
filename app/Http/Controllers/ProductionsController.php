<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Purchase;
;

use App\Item;


use App\Rate;
use App\PurchaseUnit;

class ProductionsController extends Controller
{
    public function index()
    {
        $productions = Production::orderBy('id', 'ASC')->paginate(50);
        $products = Product::all();
        $departments = Department::all();

        $fromDate = '';
        $toDate = '';

        $selectedItems = [];
        $selectedDepartments = [];

        $individualTotal = '';
        
        return view('productions.index', compact('productions', 'products', 'departments', 'fromDate', 'toDate', 'individualTotal', 'selectedItems', 'selectedDepartments'));
    }

    public function filter()
    {
        $data = request()->validate([
            'fromDate' => 'required',
            'toDate' => 'required',
            'selectedItems' => '',
            'selectedDepartments' => '',
        ]);

        $fromDate = date('Y-m-d', strtotime($data['fromDate']));
        $toDate = date('Y-m-d', strtotime($data['toDate']));

        $productions = '';
        $individualTotal = '';
        $selectedItems = [];
        $selectedDepartments = [];
            
        if(array_key_exists('selectedDepartments', $data) && $data['selectedDepartments'])
        {
            $productDepartmentsIds = Product::whereIn('department_id', $data['selectedDepartments'])
                                                ->select('id')
                                                ->get();

            if(array_key_exists('selectedItems', $data))
            {
                $productions = Production::where([[DB::raw("date(created_at)"), '>=', $fromDate],
                                    [DB::raw("date(created_at)"), '<=', $toDate]])
                                    ->whereIn('product_id', $data['selectedItems'])
                                    ->whereIn('product_id', $productDepartmentsIds)
                                    ->latest()->paginate(50);
    
                $individualTotal = DB::table('productions')
    
                ->join('purchase_units', 'productions.purchase_unit_id', '=', 'purchase_units.id')
                ->join('products', 'productions.product_id', '=', 'products.id')
                ->join('departments', 'products.department_id', '=', 'departments.id')
                ->where([[DB::raw("date(productions.created_at)"), '>=', $fromDate],
                                    [DB::raw("date(productions.created_at)"), '<=', $toDate]])
                        ->whereIn('productions.product_id', $data['selectedItems'])
                        ->whereIn('products.department_id', $data['selectedDepartments'])
                        ->select('productions.product_id', 'products.name AS productName',
                                'departments.name AS departmentName',
                                DB::raw("SUM(purchase_units.value) AS totalQuantity"))
                        ->groupBy('productions.product_id')
                        ->groupBy('products.department_id')->get();

                $selectedItems = $data['selectedItems'];
            }
            else
            {
                $productions = Production::where([[DB::raw("date(created_at)"), '>=', $fromDate],
                                                [DB::raw("date(created_at)"), '<=', $toDate]])
                                        ->whereIn('product_id', $productDepartmentsIds)
                                        ->latest()->paginate(50);

                $individualTotal = DB::table('productions')
    
                ->join('purchase_units', 'productions.purchase_unit_id', '=', 'purchase_units.id')
                ->join('products', 'productions.product_id', '=', 'products.id')
                ->join('departments', 'products.department_id', '=', 'departments.id')
                ->where([[DB::raw("date(productions.created_at)"), '>=', $fromDate],
                                    [DB::raw("date(productions.created_at)"), '<=', $toDate]])
                        ->whereIn('products.department_id', $data['selectedDepartments'])
                        ->select('productions.product_id', 'products.name AS productName',
                                'departments.name AS departmentName',
                                DB::raw("SUM(purchase_units.value) AS totalQuantity"))
                        ->groupBy('productions.product_id')
                        ->groupBy('products.department_id')->get();
            }

            $selectedDepartments = $data['selectedDepartments'];
        }
        else
        {
            if(array_key_exists('selectedItems', $data))
            {
                $productions = Production::where([[DB::raw("date(created_at)"), '>=', $fromDate],
                                    [DB::raw("date(created_at)"), '<=', $toDate]])
                                    ->whereIn('product_id', $data['selectedItems'])
                                    ->latest()->paginate(50);
    
                $individualTotal = DB::table('productions')
    
                ->join('purchase_units', 'productions.purchase_unit_id', '=', 'purchase_units.id')
                ->join('products', 'productions.product_id', '=', 'products.id')
                ->join('departments', 'products.department_id', '=', 'departments.id')
                ->where([[DB::raw("date(productions.created_at)"), '>=', $fromDate],
                                    [DB::raw("date(productions.created_at)"), '<=', $toDate]])
                        ->whereIn('productions.product_id', $data['selectedItems'])
                        ->select('productions.product_id', 'products.name AS productName',
                                'departments.name AS departmentName',
                                DB::raw("SUM(purchase_units.value) AS totalQuantity"))
                        ->groupBy('productions.product_id')
                        ->groupBy('products.department_id')->get();
                $selectedItems = $data['selectedItems'];
            }
            else
            {
                $productions = Production::where([[DB::raw("date(created_at)"), '>=', $fromDate],
                [DB::raw("date(created_at)"), '<=', $toDate]])
                ->latest()->paginate(50);
            }
        }

        $products = Product::all();
        $departments = Department::all();
                                        
        return view('productions.index', compact('productions', 'departments', 'products', 'selectedItems', 'selectedDepartments', 'fromDate', 'toDate', 'individualTotal'));
    }

    public function create()
    {
        $products = Product::all();
        $units = PurchaseUnit::OrderBy('value', 'ASC')->get();

        return view('productions.create', compact('units', 'products'));
    }

    public function store()
    {
        $data = request()->validate([
            'product_id' => 'required',
            'purchase_unit_id' => 'required',
        ]);

        $data['status'] = 1;

        $inventory = Inventory::where('product_id', $data['product_id'])->first();
        
        Production::create($data);

        $quantity = PurchaseUnit::where('id', $data['purchase_unit_id'])->first()->value;

        $inventory->update(['quantity' => $inventory->quantity + $quantity]);

        return redirect('/production/create');
    }

    public function destroy(Production $production)
    {
        $inventory = Inventory::where('product_id', $production['product_id'])->first();

        if($inventory->quantity > 0)
            $inventory->update(['quantity' => $inventory->quantity - $production->purchaseUnit->value]);

        $production->delete();

        return 'deleted';
    }
}
