<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Purchase;
use App\Sale;

use App\Item;


use App\RawInventory;

use App\Rate;
use App\PurchaseUnit;

class DepartmentsController extends Controller
{
    public function index()
    {
        $departments = Department::orderBy('id', 'ASC')->paginate(10);
        
        return view('departments.index', compact('departments'));
    }

    public function show(Department $department)
    {
        $productIds = $department->products()->select('products.id')->get();

        $sales = Sale::whereIn('product_id', $productIds)->latest()->paginate(50);
        
        $products = $department->products;

        $total = '';
        $individualTotal = '';

        $fromDate = '';
        $toDate = '';
        $selectedItems = [];
        $selectedCustomers = [];
        $return = '';

        return view('departments.show', compact('sales', 'products', 'department', 'fromDate', 'toDate', 'total', 'individualTotal', 'selectedItems', 'selectedCustomers', 'return'));
    }

    public function salesFilter(Department $department)
    {
        $data = request()->validate([
            'fromDate' => 'required',
            'toDate' => 'required',
            'selectedItems' => '',
            'return' => '',
        ]);

        $departmentProductIds = $department->products()->select('id')->get();

        $fromDate = date('Y-m-d', strtotime($data['fromDate']));
        $toDate = date('Y-m-d', strtotime($data['toDate']));

        $sales = '';
        $total = '';
        $individualTotal = '';
        $selectedItems = [];
        $return = '';

        if(array_key_exists('return', $data))
        {
            if(array_key_exists('selectedItems', $data))
            {
                $total = Sale::whereIn('product_id', $departmentProductIds)
                            ->where([[DB::raw("date(created_at)"), '>=', $fromDate],
                                [DB::raw("date(created_at)"), '<=', $toDate],
                                    ["status", '=', $data['return']]])
                            ->whereIn('product_id', $data['selectedItems'])
                            ->select(DB::raw("SUM(price) AS totalSale"))->first();

                $sales = Sale::whereIn('product_id', $departmentProductIds)
                            ->where([[DB::raw("date(created_at)"), '>=', $fromDate],
                                [DB::raw("date(created_at)"), '<=', $toDate],
                                ["status", '=', $data['return']]])
                                ->whereIn('product_id', $data['selectedItems'])
                                ->latest()->paginate(50);

                $individualTotal = Sale::whereIn('product_id', $departmentProductIds)
                                    ->where([[DB::raw("date(created_at)"), '>=', $fromDate],
                                                [DB::raw("date(created_at)"), '<=', $toDate],
                                                ["status", '=', $data['return']]])
                                    ->whereIn('product_id', $data['selectedItems'])
                                    ->select(DB::raw("SUM(price) AS totalSale"),
                                            'product_id')
                                    ->groupBy('product_id')->get();

                $selectedItems = $data['selectedItems'];
            }
            else
            {
                $total = Sale::whereIn('product_id', $departmentProductIds)
                            ->where([[DB::raw("date(created_at)"), '>=', $fromDate],
                                [DB::raw("date(created_at)"), '<=', $toDate],
                                    ["status", '=', $data['return']]])
                            ->select(DB::raw("SUM(price) AS totalSale"))->first();

                $sales = Sale::where([[DB::raw("date(created_at)"), '>=', $fromDate],
                                [DB::raw("date(created_at)"), '<=', $toDate],
                                ["status", '=', $data['return']]])
                                ->latest()->paginate(50);
            }

            $return = $data['return'];
        }
        else
        {
            if(array_key_exists('selectedItems', $data))
            {
                $total = Sale::whereIn('product_id', $departmentProductIds)
                            ->where([[DB::raw("date(created_at)"), '>=', $fromDate],
                                    [DB::raw("date(created_at)"), '<=', $toDate]])
                            ->whereIn('product_id', $data['selectedItems'])
                                ->select(DB::raw("SUM(price) AS totalSale"))
                                ->first();

                $sales = Sale::whereIn('product_id', $departmentProductIds)
                                ->where([[DB::raw("date(created_at)"), '>=', $fromDate],
                                    [DB::raw("date(created_at)"), '<=', $toDate]])
                                    ->whereIn('product_id', $data['selectedItems'])
                                    ->latest()->paginate(50);

                $individualTotal = Sale::whereIn('product_id', $departmentProductIds)
                                ->where([[DB::raw("date(created_at)"), '>=', $fromDate],
                                            [DB::raw("date(created_at)"), '<=', $toDate]])
                                ->whereIn('product_id', $data['selectedItems'])
                                ->select('product_id', DB::raw("SUM(price) AS totalSale"))
                                ->groupBy('product_id')->get();

                $selectedItems = $data['selectedItems'];
            }
            else
            {
                $total = Sale::whereIn('product_id', $departmentProductIds)
                            ->where([[DB::raw("date(created_at)"), '>=', $fromDate],
                                    [DB::raw("date(created_at)"), '<=', $toDate]])
                                ->select(DB::raw("SUM(price) AS totalSale"))
                                ->first();
                                
                $sales = Sale::whereIn('product_id', $departmentProductIds)
                                ->where([[DB::raw("date(created_at)"), '>=', $fromDate],
                                        [DB::raw("date(created_at)"), '<=', $toDate]])
                                        ->latest()->paginate(50);
            }

        }

        $products = $department->products;
                                        
        return view('departments.show', compact('sales', 'products', 'department', 'selectedItems', 'return', 'fromDate', 'toDate', 'total', 'individualTotal'));
    }

    public function create()
    {
        return view('departments.create');
    }

    public function store()
    {
        $data = request()->validate([
            'name' => 'required',
        ]);
        
        $department = Department::firstOrCreate($data);

        return redirect('/departments');
    }

    public function destroy(Department $department)
    {
        foreach($department->products as $product) 
        {
            if($product->productions)
            {
                $product->productions()->delete();
            }
        }
        $department->products()->delete();
        $department->delete();

        return 'deleted';
    }


    //Below code is related to Department_Item table.

    public function usage()
    {
        $usages = DepartmentItem::latest()->paginate(50);

        $departments = Department::all();
        $items = Item::all();

        $fromDate = '';
        $toDate = '';

        $individualTotal = '';
        $selectedItems = [];
        $selectedDepartments = [];

        return view('usage.index', compact('usages', 'departments', 'items', 'selectedItems', 'selectedDepartments', 'fromDate', 'toDate', 'individualTotal'));
    }

    public function usageFilter()
    {
        $data = request()->validate([
            'fromDate' => 'required',
            'toDate' => 'required',
            'selectedItems' => '',
            'selectedDepartments' => '',
        ]);

        $fromDate = date('Y-m-d', strtotime($data['fromDate']));
        $toDate = date('Y-m-d', strtotime($data['toDate']));

        $usages = '';
        $individualTotal = '';
        $selectedItems = [];
        $selectedDepartments = [];
            
        if(array_key_exists('selectedDepartments', $data) && $data['selectedDepartments'])
        {
            if(array_key_exists('selectedItems', $data))
            {
                $usages = DepartmentItem::where([[DB::raw("date(created_at)"), '>=', $fromDate],
                                    [DB::raw("date(created_at)"), '<=', $toDate]])
                                    ->whereIn('item_id', $data['selectedItems'])
                                    ->whereIn('department_id', $data['selectedDepartments'])
                                    ->latest()->paginate(50);
    
                $individualTotal = DB::table('department_item')
    
                ->join('purchase_units', 'department_item.purchase_unit_id', '=', 'purchase_units.id')
                ->join('items', 'department_item.item_id', '=', 'items.id')
                ->join('departments', 'department_item.department_id', '=', 'departments.id')
                ->where([[DB::raw("date(department_item.created_at)"), '>=', $fromDate],
                                    [DB::raw("date(department_item.created_at)"), '<=', $toDate]])
                        ->whereIn('department_item.item_id', $data['selectedItems'])
                        ->whereIn('department_item.department_id', $data['selectedDepartments'])
                        ->select('department_item.item_id', 'items.name AS itemName',
                                'departments.name AS departmentName',
                                DB::raw("SUM(purchase_units.value) AS totalQuantity"))
                        ->groupBy('department_item.item_id')
                        ->groupBy('department_item.department_id')->get();
                $selectedItems = $data['selectedItems'];
            }
            else
            {
                $usages = DepartmentItem::where([[DB::raw("date(created_at)"), '>=', $fromDate],
                                                [DB::raw("date(created_at)"), '<=', $toDate]])
                                        ->whereIn('department_id', $data['selectedDepartments'])
                                        ->latest()->paginate(50);

                $individualTotal = DB::table('department_item')
    
                ->join('purchase_units', 'department_item.purchase_unit_id', '=', 'purchase_units.id')
                ->join('items', 'department_item.item_id', '=', 'items.id')
                ->join('departments', 'department_item.department_id', '=', 'departments.id')
                ->where([[DB::raw("date(department_item.created_at)"), '>=', $fromDate],
                                    [DB::raw("date(department_item.created_at)"), '<=', $toDate]])
                        ->whereIn('department_item.department_id', $data['selectedDepartments'])
                        ->select('department_item.item_id', 'items.name AS itemName',
                                'departments.name AS departmentName',
                                DB::raw("SUM(purchase_units.value) AS totalQuantity"))
                        ->groupBy('department_item.item_id')
                        ->groupBy('department_item.department_id')->get();    
            }

            $selectedDepartments = $data['selectedDepartments'];
        }
        else
        {
            if(array_key_exists('selectedItems', $data))
            {
                $usages = DepartmentItem::where([[DB::raw("date(created_at)"), '>=', $fromDate],
                                    [DB::raw("date(created_at)"), '<=', $toDate]])
                                    ->whereIn('item_id', $data['selectedItems'])
                                    ->latest()->paginate(50);
    
                $individualTotal = DB::table('department_item')
    
                ->join('purchase_units', 'department_item.purchase_unit_id', '=', 'purchase_units.id')
                ->join('items', 'department_item.item_id', '=', 'items.id')
                ->join('departments', 'department_item.department_id', '=', 'departments.id')
                ->where([[DB::raw("date(department_item.created_at)"), '>=', $fromDate],
                                    [DB::raw("date(department_item.created_at)"), '<=', $toDate]])
                        ->whereIn('department_item.item_id', $data['selectedItems'])
                        ->select('department_item.item_id', 'items.name AS itemName',
                                'departments.name AS departmentName',
                                DB::raw("SUM(purchase_units.value) AS totalQuantity"))
                        ->groupBy('department_item.item_id')
                        ->groupBy('department_item.department_id')->get();
                $selectedItems = $data['selectedItems'];
            }
            else
            {
                $usages = DepartmentItem::where([[DB::raw("date(created_at)"), '>=', $fromDate],
                [DB::raw("date(created_at)"), '<=', $toDate]])
                ->latest()->paginate(50);
            }
        }

        $products = Product::all();
        $departments = Department::all();
        $items = Item::all();
                                        
        return view('usage.index', compact('usages', 'departments', 'items', 'selectedItems', 'selectedDepartments', 'fromDate', 'toDate', 'individualTotal'));
    }

    public function createUsage()
    {
        $departments = Department::all();
        $items = Item::all();
        $units = PurchaseUnit::OrderBy('value', 'ASC')->get();

        return view('usage.create', compact('departments', 'items', 'units'));
    }

    public function storeUsage()
    {
        $data = request()->validate([
            'item_id' => 'required',
            'department_id' => 'required',
            'purchase_unit_id' => 'required',
        ]);
        
        $usage = DepartmentItem::create($data);

        $purchaseUnit = PurchaseUnit::find($data['purchase_unit_id']);

        $rawInventory = RawInventory::where('item_id', $data['item_id'])->first();

        $rawInventory->update(['quantity' => $rawInventory->quantity - $purchaseUnit->value]);

        return redirect('/usage');
    }

    public function destroyUsage(DepartmentItem $departmentItem)
    {
        $departmentItem->delete();

        return 'deleted';
    }
}
