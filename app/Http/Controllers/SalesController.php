<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
// use Mike42\Escpos\PrintConnectors\FilePrintConnector;
use Mike42\Escpos\CapabilityProfile;
use Mike42\Escpos\Printer;
use Mike42\Escpos\EscposImage;
use App\CustomStuff\PrintCustomItem;

use App\Sale;
use App\Order;
use App\Item;
use App\Category;
use App\Product;
use App\RawInventory;
use App\RawWaste;
use App\Unit;
use App\Rate;
use App\Customer;
use App\Purchase;
use App\Production;
use App\Department;
use App\Expense;
use App\Configuration;
use App\SpecificPrice;
class SalesController extends Controller
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
        $sales = DB::table('sales')
                ->join('items', 'sales.item_id', '=', 'items.id')
                ->select('sales.*', 'items.name AS item_name', 'items.unit_id AS item_unit_id')
                ->latest()->paginate(50);

        $categories = Category::all();
        $items = Item::all();

        $customers = Customer::all();

        $total = '';
        $individualTotal = '';

        $fromDate = '';
        $toDate = '';
        $selectedCategories = [];
        $selectedItems = [];
        $selectedCustomers = [];
        $return = '1';

        return view('sales.index', compact('sales', 'categories', 'items', 'customers', 'fromDate', 'toDate', 'total', 'individualTotal', 'selectedCategories', 'selectedItems', 'selectedCustomers', 'return'));
    }

    public function salesReport()
    {
        if(allowed(12, 'view'))
        {
            // $sales = DB::table('sales')
            //         ->join('items', 'sales.item_id', '=', 'items.id')
            //         ->select('sales.*', 'items.name AS item_name', 'items.unit_id AS item_unit_id')
            //         ->latest()->paginate(50);


            $categories = Category::all();
            $items = Item::all();

            $customers = Customer::all();

            $individualTotal = '';

            $fromDate = date("Y-m-d");
            $toDate = date("Y-m-d");
            $selectedCategories = [];
            $selectedItems = [];
            $selectedCustomers = [];
            $status = 3;

            $total = Sale::where([[DB::raw("date(created_at)"), '>=', $fromDate],
            [DB::raw("date(created_at)"), '<=', $toDate]])
            ->select(DB::raw("SUM(price) AS totalSale"))
            ->first();
        
            $sales = DB::table('sales')
            ->join('items', 'sales.item_id', '=', 'items.id')
            ->where([[DB::raw("date(sales.created_at)"), '>=', $fromDate],
                [DB::raw("date(sales.created_at)"), '<=', $toDate]])
            ->select('sales.*', 'items.name AS item_name', 'items.unit_id AS item_unit_id')
            ->latest()->paginate(50);

            return view('reports.sales', compact('sales', 'categories', 'items', 'customers', 'fromDate', 'toDate', 'total', 'individualTotal', 'selectedCategories', 'selectedItems', 'selectedCustomers', 'status'));
        }
        else
        {
            return redirect('/sale/create');
        }
    }

    public function filter()
    {
        $data = request()->validate([
            'fromDate' => 'required',
            'toDate' => 'required',
            'selectedCategories' => '',
            'selectedItems' => '',
            'return' => '',
        ]);

        $fromDate = date('Y-m-d', strtotime($data['fromDate']));
        $toDate = date('Y-m-d', strtotime($data['toDate']));

        $sales = '';
        $total = '';
        $individualTotal = '';
        $selectedCategories = [];
        $selectedItems = [];
        $return = '';

        if(array_key_exists('return', $data))
        {
            if(array_key_exists('selectedCategories', $data))
            {
                $total = DB::table('sales')
                                ->join('items', 'sales.item_id', '=', 'items.id')
                                ->join('categories', 'items.category_id', '=', 'categories.id')
                                ->where([[DB::raw("date(sales.created_at)"), '>=', $fromDate],
                                    [DB::raw("date(sales.created_at)"), '<=', $toDate],
                                        ["sales.status", '=', $data['return']]])
                                ->whereIn('categories.id', $data['selectedCategories'])
                                ->select(DB::raw("SUM(sales.price) AS totalSale"))->first();

                $sales = DB::table('sales')
                                ->join('items', 'sales.item_id', '=', 'items.id')
                                ->join('categories', 'items.category_id', '=', 'categories.id')
                                ->where([[DB::raw("date(sales.created_at)"), '>=', $fromDate],
                                    [DB::raw("date(sales.created_at)"), '<=', $toDate],
                                    ["sales.status", '=', $data['return']]])
                                    ->whereIn('categories.id', $data['selectedCategories'])
                                    ->select('sales.*', 'items.name AS item_name', 'items.unit_id AS item_unit_id')
                                    ->latest()->paginate(50);

                $individualTotal = DB::table('sales')
                                ->join('items', 'sales.item_id', '=', 'items.id')
                                ->join('categories', 'items.category_id', '=', 'categories.id')
                                ->where([[DB::raw("date(sales.created_at)"), '>=', $fromDate],
                                                [DB::raw("date(sales.created_at)"), '<=', $toDate],
                                                ["sales.status", '=', $data['return']]])
                                    ->whereIn('categories.id', $data['selectedCategories'])
                                    ->select(DB::raw("SUM(sales.price) AS totalSale"),
                                            'categories.name AS category_name', 'categories.id AS category_id')
                                    ->groupBy('category_id')->get();

                $selectedCategories = $data['selectedCategories'];
            }
            else if(array_key_exists('selectedItems', $data))
            {
                $total = Sale::where([[DB::raw("date(created_at)"), '>=', $fromDate],
                                [DB::raw("date(created_at)"), '<=', $toDate],
                                    ["status", '=', $data['return']]])
                            ->whereIn('item_id', $data['selectedItems'])
                            ->select(DB::raw("SUM(price) AS totalSale"))->first();

                $sales = DB::table('sales')
                            ->join('items', 'sales.item_id', '=', 'items.id')
                            ->where([[DB::raw("date(sales.created_at)"), '>=', $fromDate],
                                [DB::raw("date(sales.created_at)"), '<=', $toDate],
                                ["sales.status", '=', $data['return']]])
                                ->whereIn('sales.item_id', $data['selectedItems'])
                                ->select('sales.*', 'items.name AS item_name', 'items.unit_id AS item_unit_id')
                                ->latest()->paginate(50);

                $individualTotal = Sale::where([[DB::raw("date(created_at)"), '>=', $fromDate],
                                                [DB::raw("date(created_at)"), '<=', $toDate],
                                                ["status", '=', $data['return']]])
                                    ->whereIn('item_id', $data['selectedItems'])
                                    ->select(DB::raw("SUM(price) AS totalSale"),
                                            'item_id')
                                    ->groupBy('item_id')->get();

                $selectedItems = $data['selectedItems'];
            }
            else
            {
                $total = Sale::where([[DB::raw("date(created_at)"), '>=', $fromDate],
                                [DB::raw("date(created_at)"), '<=', $toDate],
                                    ["status", '=', $data['return']]])
                            ->select(DB::raw("SUM(price) AS totalSale"))->first();

                $sales = DB::table('sales')
                            ->join('items', 'sales.item_id', '=', 'items.id')
                            ->where([[DB::raw("date(sales.created_at)"), '>=', $fromDate],
                                [DB::raw("date(sales.created_at)"), '<=', $toDate],
                                ["sales.status", '=', $data['return']]])
                                ->select('sales.*', 'items.name AS item_name', 'items.unit_id AS item_unit_id')
                                ->latest()->paginate(50);
            }

            $return = $data['return'];
        }
        else
        {
            if(array_key_exists('selectedCategories', $data))
            {
                $total = DB::table('sales')
                                ->join('items', 'sales.item_id', '=', 'items.id')
                                ->join('categories', 'items.category_id', '=', 'categories.id')
                                ->where([[DB::raw("date(sales.created_at)"), '>=', $fromDate],
                                    [DB::raw("date(sales.created_at)"), '<=', $toDate]])
                                ->whereIn('categories.id', $data['selectedCategories'])
                                ->select(DB::raw("SUM(sales.price) AS totalSale"))->first();

                $sales = DB::table('sales')
                                ->join('items', 'sales.item_id', '=', 'items.id')
                                ->join('categories', 'items.category_id', '=', 'categories.id')
                                ->where([[DB::raw("date(sales.created_at)"), '>=', $fromDate],
                                    [DB::raw("date(sales.created_at)"), '<=', $toDate]])
                                    ->whereIn('categories.id', $data['selectedCategories'])
                                    ->select('sales.*', 'items.name AS item_name', 'items.unit_id AS item_unit_id')
                                    ->latest()->paginate(50);

                $individualTotal = DB::table('sales')
                                ->join('items', 'sales.item_id', '=', 'items.id')
                                ->join('categories', 'items.category_id', '=', 'categories.id')
                                ->where([[DB::raw("date(sales.created_at)"), '>=', $fromDate],
                                                [DB::raw("date(sales.created_at)"), '<=', $toDate]])
                                    ->whereIn('categories.id', $data['selectedCategories'])
                                    ->select(DB::raw("SUM(sales.price) AS totalSale"),
                                            'categories.name AS category_name', 'categories.id')
                                    ->groupBy('categories.id')->get();

                $selectedCategories = $data['selectedCategories'];
            }
            else if(array_key_exists('selectedItems', $data))
            {
                $total = Sale::where([[DB::raw("date(created_at)"), '>=', $fromDate],
                                    [DB::raw("date(created_at)"), '<=', $toDate]])
                            ->whereIn('item_id', $data['selectedItems'])
                                ->select(DB::raw("SUM(price) AS totalSale"))
                                ->first();

                $sales = DB::table('sales')
                                ->join('items', 'sales.item_id', '=', 'items.id')
                                ->where([[DB::raw("date(sales.created_at)"), '>=', $fromDate],
                                    [DB::raw("date(sales.created_at)"), '<=', $toDate]])
                                    ->whereIn('sales.item_id', $data['selectedItems'])
                                    ->select('sales.*', 'items.name AS item_name', 'items.unit_id AS item_unit_id')
                                    ->latest()->paginate(50);

                $individualTotal = Sale::where([[DB::raw("date(created_at)"), '>=', $fromDate],
                                            [DB::raw("date(created_at)"), '<=', $toDate]])
                                ->whereIn('item_id', $data['selectedItems'])
                                ->select('item_id', DB::raw("SUM(price) AS totalSale"))
                                ->groupBy('item_id')->get();

                $selectedItems = $data['selectedItems'];
            }
            else
            {
                $total = Sale::where([[DB::raw("date(created_at)"), '>=', $fromDate],
                                    [DB::raw("date(created_at)"), '<=', $toDate]])
                                ->select(DB::raw("SUM(price) AS totalSale"))
                                ->first();
                                
                $sales = $sales = DB::table('sales')
                                    ->join('items', 'sales.item_id', '=', 'items.id')
                                    ->where([[DB::raw("date(sales.created_at)"), '>=', $fromDate],
                                        [DB::raw("date(sales.created_at)"), '<=', $toDate]])
                                    ->select('sales.*', 'items.name AS item_name', 'items.unit_id AS item_unit_id')
                                    ->latest()->paginate(50);
            }

        }

        $categories = Category::all();
        $items = Item::all();
                                        
        return view('sales.index', compact('sales', 'categories', 'items', 'selectedCategories', 'selectedItems', 'return', 'fromDate', 'toDate', 'total', 'individualTotal'));
    }

    public function filterSalesReport()
    {
        $data = request()->validate([
            'fromDate' => 'required',
            'toDate' => 'required',
            'selectedCategories' => '',
            'selectedItems' => '',
            'status' => ''
        ]);

        $fromDate = date('Y-m-d', strtotime($data['fromDate']));
        $toDate = date('Y-m-d', strtotime($data['toDate']));

        $sales = '';
        $total = '';
        $individualTotal = '';
        $selectedCategories = [];
        $selectedItems = [];
        $status = $data['status'];

        if($data['status'] == 1 || $data['status'] == 2)
        {
            if(array_key_exists('selectedCategories', $data))
            {
                $total = DB::table('sales')
                                ->join('items', 'sales.item_id', '=', 'items.id')
                                ->join('categories', 'items.category_id', '=', 'categories.id')
                                ->join('orders', 'sales.order_id', '=', 'orders.id')
                                ->where([[DB::raw("date(sales.created_at)"), '>=', $fromDate],
                                    [DB::raw("date(sales.created_at)"), '<=', $toDate],
                                        ["orders.status", '=', $data['status']]])
                                ->whereIn('categories.id', $data['selectedCategories'])
                                ->select(DB::raw("SUM(sales.price) AS totalSale"))->first();

                $sales = DB::table('sales')
                                ->join('items', 'sales.item_id', '=', 'items.id')
                                ->join('categories', 'items.category_id', '=', 'categories.id')
                                ->join('orders', 'sales.order_id', '=', 'orders.id')
                                ->where([[DB::raw("date(sales.created_at)"), '>=', $fromDate],
                                    [DB::raw("date(sales.created_at)"), '<=', $toDate],
                                    ["orders.status", '=', $data['status']]])
                                    ->whereIn('categories.id', $data['selectedCategories'])
                                    ->select('sales.*', 'items.name AS item_name', 'items.unit_id AS item_unit_id')
                                    ->latest()->paginate(50);

                $individualTotal = DB::table('sales')
                                ->join('items', 'sales.item_id', '=', 'items.id')
                                ->join('categories', 'items.category_id', '=', 'categories.id')
                                ->join('orders', 'sales.order_id', '=', 'orders.id')
                                ->where([[DB::raw("date(sales.created_at)"), '>=', $fromDate],
                                                [DB::raw("date(sales.created_at)"), '<=', $toDate],
                                                ["orders.status", '=', $data['status']]])
                                    ->whereIn('categories.id', $data['selectedCategories'])
                                    ->select(DB::raw("SUM(sales.price) AS totalSale"),
                                            'categories.name AS category_name', 'categories.id AS category_id')
                                    ->groupBy('category_id')->get();

                $selectedCategories = $data['selectedCategories'];
            }
            else if(array_key_exists('selectedItems', $data))
            {
                $total = DB::table('sales')
                                ->join('orders', 'sales.order_id', '=', 'orders.id')
                                ->where([[DB::raw("date(sales.created_at)"), '>=', $fromDate],
                                [DB::raw("date(sales.created_at)"), '<=', $toDate],
                                    ["orders.status", '=', $data['status']]])
                            ->whereIn('sales.item_id', $data['selectedItems'])
                            ->select(DB::raw("SUM(sales.price) AS totalSale"))->first();

                $sales = DB::table('sales')
                            ->join('items', 'sales.item_id', '=', 'items.id')
                            ->join('orders', 'sales.order_id', '=', 'orders.id')
                            ->where([[DB::raw("date(sales.created_at)"), '>=', $fromDate],
                                [DB::raw("date(sales.created_at)"), '<=', $toDate],
                                ["orders.status", '=', $data['status']]])
                                ->whereIn('sales.item_id', $data['selectedItems'])
                                ->select('sales.*', 'items.name AS item_name', 'items.unit_id AS item_unit_id')
                                ->latest()->paginate(50);

                $individualTotal = DB::table('sales')
                                    ->join('orders', 'sales.order_id', '=', 'orders.id')
                                    ->join('items', 'sales.item_id', '=', 'items.id')
                                    ->where([[DB::raw("date(sales.created_at)"), '>=', $fromDate],
                                                [DB::raw("date(sales.created_at)"), '<=', $toDate],
                                                ["orders.status", '=', $data['status']]])
                                    ->whereIn('sales.item_id', $data['selectedItems'])
                                    ->select(DB::raw("SUM(sales.price) AS totalSale"),
                                            'sales.item_id', 'items.name As item_name')
                                    ->groupBy('sales.item_id')->get();

                $selectedItems = $data['selectedItems'];
            }
            else
            {
                $total = DB::table('sales')
                                ->join('orders', 'sales.order_id', '=', 'orders.id')
                                ->where([[DB::raw("date(sales.created_at)"), '>=', $fromDate],
                                [DB::raw("date(sales.created_at)"), '<=', $toDate],
                                    ["orders.status", '=', $data['status']]])
                            ->select(DB::raw("SUM(sales.price) AS totalSale"))->first();

                $sales = DB::table('sales')
                            ->join('orders', 'sales.order_id', '=', 'orders.id')
                            ->join('items', 'sales.item_id', '=', 'items.id')
                            ->where([[DB::raw("date(sales.created_at)"), '>=', $fromDate],
                                [DB::raw("date(sales.created_at)"), '<=', $toDate],
                                ["orders.status", '=', $data['status']]])
                                ->select('sales.*', 'items.name AS item_name', 'items.unit_id AS item_unit_id')
                                ->latest()->paginate(50);
            }
        }
        else
        {
            if(array_key_exists('selectedCategories', $data))
            {
                $total = DB::table('sales')
                                ->join('items', 'sales.item_id', '=', 'items.id')
                                ->join('categories', 'items.category_id', '=', 'categories.id')
                                ->where([[DB::raw("date(sales.created_at)"), '>=', $fromDate],
                                    [DB::raw("date(sales.created_at)"), '<=', $toDate]])
                                ->whereIn('categories.id', $data['selectedCategories'])
                                ->select(DB::raw("SUM(sales.price) AS totalSale"))->first();

                $sales = DB::table('sales')
                                ->join('items', 'sales.item_id', '=', 'items.id')
                                ->join('categories', 'items.category_id', '=', 'categories.id')
                                ->where([[DB::raw("date(sales.created_at)"), '>=', $fromDate],
                                    [DB::raw("date(sales.created_at)"), '<=', $toDate]])
                                    ->whereIn('categories.id', $data['selectedCategories'])
                                    ->select('sales.*', 'items.name AS item_name', 'items.unit_id AS item_unit_id')
                                    ->latest()->paginate(50);

                $individualTotal = DB::table('sales')
                                ->join('items', 'sales.item_id', '=', 'items.id')
                                ->join('categories', 'items.category_id', '=', 'categories.id')
                                ->where([[DB::raw("date(sales.created_at)"), '>=', $fromDate],
                                                [DB::raw("date(sales.created_at)"), '<=', $toDate]])
                                    ->whereIn('categories.id', $data['selectedCategories'])
                                    ->select(DB::raw("SUM(sales.price) AS totalSale"),
                                            'categories.name AS category_name', 'categories.id')
                                    ->groupBy('categories.id')->get();

                $selectedCategories = $data['selectedCategories'];
            }
            else if(array_key_exists('selectedItems', $data))
            {
                $total = Sale::where([[DB::raw("date(created_at)"), '>=', $fromDate],
                                    [DB::raw("date(created_at)"), '<=', $toDate]])
                            ->whereIn('item_id', $data['selectedItems'])
                                ->select(DB::raw("SUM(price) AS totalSale"))
                                ->first();

                $sales = DB::table('sales')
                                ->join('items', 'sales.item_id', '=', 'items.id')
                                ->where([[DB::raw("date(sales.created_at)"), '>=', $fromDate],
                                    [DB::raw("date(sales.created_at)"), '<=', $toDate]])
                                    ->whereIn('sales.item_id', $data['selectedItems'])
                                    ->select('sales.*', 'items.name AS item_name', 'items.unit_id AS item_unit_id')
                                    ->latest()->paginate(50);

                // $individualTotal = Sale::where([[DB::raw("date(created_at)"), '>=', $fromDate],
                //                             [DB::raw("date(created_at)"), '<=', $toDate]])
                //                 ->whereIn('item_id', $data['selectedItems'])
                //                 ->select('item_id', DB::raw("SUM(price) AS totalSale"))
                //                 ->groupBy('item_id')->get();

                $individualTotal = DB::table('sales')
                                    ->join('orders', 'sales.order_id', '=', 'orders.id')
                                    ->join('items', 'sales.item_id', '=', 'items.id')
                                    ->where([[DB::raw("date(sales.created_at)"), '>=', $fromDate],
                                                [DB::raw("date(sales.created_at)"), '<=', $toDate]])
                                    ->whereIn('sales.item_id', $data['selectedItems'])
                                    ->select(DB::raw("SUM(sales.price) AS totalSale"),
                                            'sales.item_id', 'items.name As item_name')
                                    ->groupBy('sales.item_id')->get();

                $selectedItems = $data['selectedItems'];
            }
            else
            {
                $total = Sale::where([[DB::raw("date(created_at)"), '>=', $fromDate],
                                    [DB::raw("date(created_at)"), '<=', $toDate]])
                                ->select(DB::raw("SUM(price) AS totalSale"))
                                ->first();
                                
                $sales = DB::table('sales')
                            ->join('items', 'sales.item_id', '=', 'items.id')
                            ->where([[DB::raw("date(sales.created_at)"), '>=', $fromDate],
                                [DB::raw("date(sales.created_at)"), '<=', $toDate]])
                            ->select('sales.*', 'items.name AS item_name', 'items.unit_id AS item_unit_id')
                            ->latest()->paginate(50);
            }
        }

        $categories = Category::all();
        $items = Item::all();
                                        
        return view('reports.sales', compact('sales', 'categories', 'items', 'selectedCategories', 'selectedItems', 'status', 'fromDate', 'toDate', 'total', 'individualTotal'));
    }

    public function create()
    {
        if(allowed(10, 'make'))
        {
            return view('sales.create');
        }
        else
        {
            return redirect('/sale/create');
        }
    }

    public function getPOSData($edit = 0)
    {
        DB::statement("SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));");

        $items = DB::table('items')
                    ->leftJoin('sales', 'items.id', '=', 'sales.item_id')
                    ->join('raw_inventories', 'raw_inventories.item_id', '=', 'items.id')
                    ->join('units', 'items.unit_id', '=', 'units.id')
                    ->join('rates', 'rates.item_id', '=', 'items.id')
                    ->select('items.*', 'rates.sale_price AS price', 'raw_inventories.quantity AS quantity',
                            DB::raw("raw_inventories.cost/raw_inventories.quantity AS average_unit_cost"), 
                            'units.id AS unit_id',
                            'units.name AS unit_name', 'units.symbol AS unit_symbol',
                            'units.fraction_name AS unit_fraction_name',
                            'units.fraction_value AS unit_fraction_value', DB::raw("COUNT(sales.item_id) AS itemsCount"))
                    ->where('items.status', '=', 1)
                    ->where('raw_inventories.quantity', '>', (posConfigurations()->maintain_inventory == 1 && $edit == 0)? 0: -5000)
                    ->groupBy('items.id')
                    ->orderBy('itemsCount', 'DESC')
                    ->orderBy('items.name', 'ASC')
                    ->get();

        DB::statement("SET sql_mode=(SELECT CONCAT(@@sql_mode, ',ONLY_FULL_GROUP_BY'));");

        $categories = Category::all();
        $customers = Customer::where('id', '!=', 1)->get();

        $csps = SpecificPrice::where('customer_id', 1)->get();

        if(sizeof($csps) > 0)
        {
            foreach ($csps as $cspKey => $csp) 
            {
                foreach($items as $key => $item)
                {
                    if($item->id == $csp->item_id)
                    {
                        $items[$key]->price = $csp->sale_price;
                    }
                }
            }
        }

        return json_encode([
            'items' => $items,
            'categories' => $categories,
            'customers' => $customers
        ]);
        // return view('sales.create', compact('items', 'categories', 'customers'));
    }

    public function getCSP($customerId)
    {
        $csps = SpecificPrice::where('customer_id', $customerId)->get();
        $excludedItemIds = $csps->pluck('item_id');

        $rates = Rate::whereNotIn('item_id', $excludedItemIds)->get();

        $csps = $csps->toArray();

        if(sizeof($csps) > 0)
        {
            for($i = 0; $i < sizeof($rates); $i++)
            {
                array_push($csps, ['customer_id' => $csps[0]['customer_id'], 
                                    'item_id' => $rates[$i]->item_id,
                                    'sale_price' => $rates[$i]->sale_price]);
            }

            return json_encode(['sale_prices' => $csps]);
        }
        else
        {
            $rates = Rate::all();
            return json_encode(['sale_prices' => $rates]);
        }
    }

    public function getCustomersHistory($searchText)
    {
        $customers = DB::select("select customers.name, customers.phone, 
                                orders.receipt_number, orders.sub_total,
                                DATE_FORMAT(orders.created_at, '%d %b %Y') 
                                AS order_date from customers 
                                inner join orders on customers.id = orders.customer_id
                                where customers.name LIKE '%{$searchText}%' OR customers.phone LIKE '%{$searchText}%'
                                ORDER BY orders.created_at DESC");

        return json_encode($customers);
    }

    public function getDraftOrder($orderId = '')
    {
        $orders = DB::select("select id, total, discount_amount, sub_total, payment, (sub_total-payment) AS receivable, DATE_FORMAT(orders.created_at, '%d %b %Y') FROM orders
                                where status = 1 AND id LIKE '%{$orderId}%'
                                ORDER BY orders.created_at DESC");

        return json_encode($orders);
    }

    public function store(Request $request, $print = null)
    {
        if(allowed(10, 'make'))
        {
            $data = json_decode($request->getContent(), true);

            //Generating receipt number below
            $lastOrderId = DB::table('orders')
                        ->select(DB::raw("MAX(id) AS lastId"))
                        ->first()->lastId;
    
            $data['receiptNumber'] = $lastOrderId + 1;
            //Generating receipt number above

            $connector = new WindowsPrintConnector("MyThermalPrinter");
            $printer = new Printer($connector);
            if($data['togglePrint'])
            {
                for($copy = 1; $copy <= ($data['numberOfCopies'] >= 5? 5: $data['numberOfCopies']); $copy++)
                {
                    $subtotal = new PrintCustomItem('Subtotal', '', '', $data['total']);
                    $total = new PrintCustomItem('Total', '', '', $data['subTotal']);
                    $paidAmount = new PrintCustomItem('Paid amount', '', '', $data['cash']);
                    $changeAmount = new PrintCustomItem('Change', '', '', $data['change']);
                    
                    $printer -> setJustification(Printer::JUSTIFY_CENTER);
                    $savedAmount = 0;
            
                    /* Name of shop */
                    $printer -> selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
                    $printer -> text(posConfigurations()->title . "\n");
                    $printer -> selectPrintMode();
                    if(posConfigurations()->subtitle && strlen(posConfigurations()->subtitle) > 0)
                        $printer -> text(posConfigurations()->subtitle . "\n");
                    
                    $printer -> text(posConfigurations()->address . "\n");
                    $printer -> text(posConfigurations()->contact . "\n");
                    $printer -> feed();
                    $printer -> setJustification(Printer::JUSTIFY_LEFT);
                    $printer -> text(date('Y-m-d') . ' ' . date('h:i A') . '            Invoice No: ' . $data['receiptNumber']. "\n");
                    if($data['status'] == 1)
                        $printer -> text('Collect at: ' . $data['receivingDate'] . '                 ' . date('h:i A', strtotime($data['receivingTime'])). "\n");
            
                    if($data['customerName'])
                        $printer -> text(new PrintCustomItem($data['customerName'], '', '', '') . "\n");
                        
                    $printer -> feed();
            
                    /* Items */
                    $printer -> setEmphasis(true);
                    $printer -> text("________________________________________________");
                    $printer -> text(new PrintCustomItem('Item', 'Qty', 'Price', 'Total'));
                    $printer -> text("________________________________________________");
                    $printer -> setEmphasis(false);
            
                    for($i = 0; $i < sizeof($data['sales']); $i++)
                    {
                        if(strlen($data['sales'][$i]['name']) > 15)
                        {
                            // $line = sprintf('%-15.40s %-13.40s %3.40s %13.40s', substr($data['sales'][$i]['name'], 0, 15) , '', '', ''); 
                            $printer -> text(substr($data['sales'][$i]['name'], 0, 15) . "\n");
            
                            $line = sprintf('%-15.40s %-13.40s %3.40s %10.40s', substr($data['sales'][$i]['name'], 15, strlen($data['sales'][$i]['name'])) , ($data['sales'][$i]['quantity'] + 0), ($data['sales'][$i]['originalPrice'] + 0), ($data['sales'][$i]['totalPrice'] + 0)); 
                            $printer -> text("$line\n");
                        }
                        else
                        {
                            $line = sprintf('%-15.40s %-13.40s %3.40s %10.40s', $data['sales'][$i]['name'] , ($data['sales'][$i]['quantity'] + 0), ($data['sales'][$i]['originalPrice'] + 0), ($data['sales'][$i]['totalPrice'] + 0));
                            $printer -> text("$line\n");
                        }
        
                        if($data['sales'][$i]['discount'] > 0)
                        {
                            $printer -> text(new PrintCustomItem('Discount', '', '', ($data['sales'][$i]['actualDiscountAmount'] + 0)));
    
                            $savedAmount = $savedAmount + $data['sales'][$i]['actualDiscountAmount'];
                        }
            
                        $printer -> text("________________________________________________");
                    }
            
                    $printer -> feed();
            
                    $printer -> setEmphasis(true);
    
                    $printer -> text("\n");
    
                    $printer -> text(new PrintCustomItem('Total Items', sizeof($data['sales']), '', ''));
    
                    $printer -> text("________________________________________________");
    
                    $printer -> text($subtotal);
            
                    if($data['discount'] > 0)
                    {
                        $printer -> text(new PrintCustomItem('Discount', '', '', $data['actualDiscountAmount']));
                        $savedAmount = $savedAmount + $data['actualDiscountAmount'];
                    }
            
                    $printer -> text($total);
            
                    $balance = 0;
                    if($data['payment'] < $data['subTotal'])
                    {
                        $balance = $data['subTotal'] - $data['payment'];
            
                        $printer -> text("\n" . new PrintCustomItem('Payment', '', '', $data['payment']));
                        $printer -> text(new PrintCustomItem('Balance', '', '', $balance));
                    }
    
                    $printer -> text("________________________________________________");
                    $printer -> text($paidAmount);
                    $printer -> text($changeAmount);
    
                    $printer -> setJustification(Printer::JUSTIFY_CENTER);
    
                    //Saved Amount Text below
                    if($savedAmount > 0 )
                    {
                        $printer -> setEmphasis(true);
                        $printer -> selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
                        $printer -> text("YOU SAVED: Rs " . $savedAmount);
                        $printer -> selectPrintMode();
                    }
                    //Saved Amount Text above
            
                    $printer -> setEmphasis(false);
            
                    $printer -> selectPrintMode();
            
                    /* Footer */
                    $printer -> feed();
                    if(posConfigurations()->thank_note)
                    {
                        $printer -> text(posConfigurations()->thank_note . "\n");
                    }
                    
                    //Terms and conditions below
                    if(posConfigurations()->terms_conditions && strlen((posConfigurations()->terms_conditions)) > 0)
                    {
                        $printer -> setEmphasis(true);
                        $printer -> text("________________________________________________");
                        $printer -> setJustification(Printer::JUSTIFY_CENTER);
                        $printer -> setUnderline(2);
                        $printer -> text("Terms and Conditions");
                        $printer -> setUnderline(0);
                        $printer -> feed();
                        $printer -> setJustification(Printer::JUSTIFY_LEFT);
                        $printer -> setEmphasis(false);
                        $printer -> selectPrintMode(Printer::MODE_FONT_B);
                        $printer -> text(posConfigurations()->terms_conditions);
                        $printer->feed();
                        $printer -> selectPrintMode(Printer::MODE_FONT_A);
                        $printer -> text("________________________________________________");
                        // $printer -> feed();
                    }
                    //Terms and conditions above
    
                    $printer -> selectPrintMode(Printer::MODE_FONT_B);
            
                    $printer -> setJustification(Printer::JUSTIFY_LEFT);
                    $printer -> text("User: " . auth()->user()->name . "\n");
            
                    $printer -> setJustification(Printer::JUSTIFY_CENTER);
                    if(posConfigurations()->footer_text)
                        $printer -> text(posConfigurations()->footer_text . " " . (posConfigurations()->footer_number ?? '') . "\n");
                    // if(posConfigurations()->footer_number)
                    //     $printer -> text(posConfigurations()->footer_number . "\n");
        
                    $printer->cut();
                }
            }

            if($data['toggleDrawer'])
            {
                $printer->pulse();
                // $printer->pulse(0, 2, 2);
            }

            $printer->close();
    
            $balance = 0;
            if($data['payment'] < $data['subTotal'])
            {
                $balance = $data['subTotal'] - $data['payment'];
            }
    
            DB::insert("insert into orders (receipt_number, customer_id, total, discount, discount_type, discount_amount, tax, tax_type, tax_amount, sub_total, 
                                            payment, balance, user_id, status, type, created_at, receiving_date, updated_at, cash_amount, change_amount) values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)", 
                                            [$data['receiptNumber'],
                                            $data['customerId'],
                                            $data['total'],
                                            $data['discount'],
                                            $data['discountType'],
                                            $data['actualDiscountAmount'],
                                            0,
                                            2,
                                            0,
                                            $data['subTotal'],
                                            $data['payment'],
                                            $balance,
                                            auth()->user()->id,
                                            $data['status'],  // status 2 collected by customer, 1 not collected
                                            $data['type'],  // type 2 take away, 1 home delivery, 3 dine-in
                                            date("Y-m-d H:i:s"),
                                            $data['status'] == 1? $data['receivingDate'] . ' ' . $data['receivingTime']: date("Y-m-d H:i:s"),
                                            date("Y-m-d H:i:s"),
                                            $data['cash'],
                                            $data['change']]);
                                            
            $orderId = DB::select("SELECT LAST_INSERT_ID() AS orderId")[0]->orderId;
    
            for($i = 0; $i < sizeof($data['sales']); $i++)
            {
                DB::insert("insert into sales (order_id, item_id, quantity, price, discount, discount_type, discount_amount, tax, tax_type, tax_amount, unit_price, created_at, updated_at, average_unit_cost, average_total_cost) values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)", 
                                            [$orderId,
                                            $data['sales'][$i]['id'],
                                            $data['sales'][$i]['quantity'],
                                            $data['sales'][$i]['totalPrice'],
                                            $data['sales'][$i]['discount'],
                                            $data['sales'][$i]['discountType'],
                                            $data['sales'][$i]['actualDiscountAmount'],
                                            $data['sales'][$i]['tax'],
                                            $data['sales'][$i]['taxType'],
                                            0,
                                            $data['sales'][$i]['originalPrice'],
                                            date("Y-m-d H:i:s"),
                                            date("Y-m-d H:i:s"),
                                            $data['sales'][$i]['averageUnitCost'],
                                            $data['sales'][$i]['averageUnitCost'] * $data['sales'][$i]['quantity']]);
    
                if(posConfigurations()->maintain_inventory == 1 && $data['status'] == 2)
                {
                    $q = $data['sales'][$i]['quantity'];
                    DB::update("update raw_inventories set quantity = quantity-{$q}, cost = cost - " . ($data['sales'][$i]['averageUnitCost'] * $q) . " where item_id = ?", [$data['sales'][$i]['id']]);
                }
            }
    
            return json_encode([
                'message' => 1
            ]);
        }
        else
        {
            return json_encode([
                'message' => 0
            ]);
        }
    }

    public function update(Request $request, $print = null)
    {
        if(allowed(10, 'edit'))
        {
            $data = json_decode($request->getContent(), true);

            $connector = new WindowsPrintConnector("MyThermalPrinter");
            $printer = new Printer($connector);
            if($data['togglePrint'])
            {
                for($copy = 1; $copy <= ($data['numberOfCopies'] >= 5? 5: $data['numberOfCopies']); $copy++)
                {
                    $subtotal = new PrintCustomItem('Subtotal', '', '', $data['total']);
                    $total = new PrintCustomItem('Total', '', '', $data['subTotal']);
                    $paidAmount = new PrintCustomItem('Paid amount', '', '', $data['cash']);
                    $changeAmount = new PrintCustomItem('Change', '', '', $data['change']);
                    
                    $printer -> setJustification(Printer::JUSTIFY_CENTER);
                    $savedAmount = 0;
            
                    /* Name of shop */
                    $printer -> selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
                    $printer -> text(posConfigurations()->title . "\n");
                    $printer -> selectPrintMode();
                    if(posConfigurations()->subtitle && strlen(posConfigurations()->subtitle) > 0)
                        $printer -> text(posConfigurations()->subtitle . "\n");
                    
                    $printer -> text(posConfigurations()->address . "\n");
                    $printer -> text(posConfigurations()->contact . "\n");
                    $printer -> feed();
                    $printer -> setJustification(Printer::JUSTIFY_LEFT);
                    $printer -> text(date('Y-m-d') . ' ' . date('h:i A') . '            ' . $data['receiptNumber']. "\n");
                    if($data['status'] == 1)
                        $printer -> text('Collect at: ' . $data['receivingDate'] . '                 ' . date('h:i A', strtotime($data['receivingTime'])). "\n");
            
                    if($data['customerName'])
                        $printer -> text(new PrintCustomItem($data['customerName'], '', '', '') . "\n");
                        
                    $printer -> feed();
            
                    /* Items */
                    $printer -> setEmphasis(true);
                    $printer -> text("________________________________________________");
                    $printer -> text(new PrintCustomItem('Item', 'Qty', 'Price', 'Total'));
                    $printer -> text("________________________________________________");
                    $printer -> setEmphasis(false);
            
                    for($i = 0; $i < sizeof($data['sales']); $i++)
                    {
                        if(strlen($data['sales'][$i]['name']) > 15)
                        {
                            // $line = sprintf('%-15.40s %-13.40s %3.40s %13.40s', substr($data['sales'][$i]['name'], 0, 15) , '', '', ''); 
                            $printer -> text(substr($data['sales'][$i]['name'], 0, 15) . "\n");
            
                            $line = sprintf('%-15.40s %-13.40s %3.40s %10.40s', substr($data['sales'][$i]['name'], 15, strlen($data['sales'][$i]['name'])) , ($data['sales'][$i]['quantity'] + 0), ($data['sales'][$i]['originalPrice'] + 0), ($data['sales'][$i]['totalPrice'] + 0)); 
                            $printer -> text("$line\n");
                        }
                        else
                        {
                            $line = sprintf('%-15.40s %-13.40s %3.40s %10.40s', $data['sales'][$i]['name'] , ($data['sales'][$i]['quantity'] + 0), ($data['sales'][$i]['originalPrice'] + 0), ($data['sales'][$i]['totalPrice'] + 0));
                            $printer -> text("$line\n");
                        }
        
                        if($data['sales'][$i]['discount'] > 0)
                        {
                            $printer -> text(new PrintCustomItem('Discount', '', '', ($data['sales'][$i]['actualDiscountAmount'] + 0)));
    
                            $savedAmount = $savedAmount + $data['sales'][$i]['actualDiscountAmount'];
                        }
            
                        $printer -> text("________________________________________________");
                    }
            
                    $printer -> feed();
            
                    $printer -> setEmphasis(true);
    
                    $printer -> text("\n");
    
                    $printer -> text(new PrintCustomItem('Total Items', sizeof($data['sales']), '', ''));
    
                    $printer -> text("________________________________________________");
    
                    $printer -> text($subtotal);
            
                    if($data['discount'] > 0)
                    {
                        $printer -> text(new PrintCustomItem('Discount', '', '', $data['actualDiscountAmount']));
                        $savedAmount = $savedAmount + $data['actualDiscountAmount'];
                    }
            
                    $printer -> text($total);
            
                    $balance = 0;
                    if($data['payment'] < $data['subTotal'])
                    {
                        $balance = $data['subTotal'] - $data['payment'];
            
                        $printer -> text("\n" . new PrintCustomItem('Payment', '', '', $data['payment']));
                        $printer -> text(new PrintCustomItem('Balance', '', '', $balance));
                    }
    
                    $printer -> text("________________________________________________");
                    $printer -> text($paidAmount);
                    $printer -> text($changeAmount);
    
                    $printer -> setJustification(Printer::JUSTIFY_CENTER);
    
                    //Saved Amount Text below
                    if($savedAmount > 0 )
                    {
                        $printer -> setEmphasis(true);
                        $printer -> selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
                        $printer -> text("YOU SAVED: Rs " . $savedAmount);
                        $printer -> selectPrintMode();
                    }
                    //Saved Amount Text above
            
                    $printer -> setEmphasis(false);
            
                    $printer -> selectPrintMode();
            
                    /* Footer */
                    $printer -> feed();
                    if(posConfigurations()->thank_note)
                    {
                        $printer -> text(posConfigurations()->thank_note . "\n");
                    }
                    
                    //Terms and conditions below
                    if(posConfigurations()->terms_conditions && strlen((posConfigurations()->terms_conditions)) > 0)
                    {
                        $printer -> setEmphasis(true);
                        $printer -> text("________________________________________________");
                        $printer -> setJustification(Printer::JUSTIFY_CENTER);
                        $printer -> setUnderline(2);
                        $printer -> text("Terms and Conditions");
                        $printer -> setUnderline(0);
                        $printer -> feed();
                        $printer -> setJustification(Printer::JUSTIFY_LEFT);
                        $printer -> setEmphasis(false);
                        $printer -> selectPrintMode(Printer::MODE_FONT_B);
                        $printer -> text(posConfigurations()->terms_conditions);
                        $printer->feed();
                        $printer -> selectPrintMode(Printer::MODE_FONT_A);
                        $printer -> text("________________________________________________");
                        // $printer -> feed();
                    }
                    //Terms and conditions above
    
                    $printer -> selectPrintMode(Printer::MODE_FONT_B);
            
                    $printer -> setJustification(Printer::JUSTIFY_LEFT);
                    $printer -> text("User: " . auth()->user()->name . "\n");
            
                    $printer -> setJustification(Printer::JUSTIFY_CENTER);
                    if(posConfigurations()->footer_text)
                        $printer -> text(posConfigurations()->footer_text . " " . (posConfigurations()->footer_number ?? '') . "\n");
                    // if(posConfigurations()->footer_number)
                    //     $printer -> text(posConfigurations()->footer_number . "\n");
        
                    $printer->cut();
                }
            }

            if($data['toggleDrawer'])
            {
                $printer->pulse();
                // $printer->pulse(0, 2, 2);
            }

            $printer->close();

            $balance = 0;
            if($data['payment'] < $data['subTotal'])
            {
                $balance = $data['subTotal'] - $data['payment'];
            }
    
            $orderToUpdate = DB::select("SELECT id AS orderId, created_at FROM orders WHERE receipt_number = '" . $data['receiptNumber'] . "'")[0];
            $orderId = $orderToUpdate->orderId;
            $orderCreatedDate = $orderToUpdate->created_at;
    
            DB::update("UPDATE orders SET customer_id = ?, total = ?, discount = ?, discount_type = ?, discount_amount = ?, 
                        tax = ?, tax_type = ?, tax_amount = ?, sub_total = ?, payment = ?, balance = ?, `user_id` = ?, 
                        `status` = ?, `type` = ?, receiving_date = ?, updated_at = ?, 
                        cash_amount = ?, change_amount = ? WHERE id = ?", [$data['customerId'],
                        $data['total'],
                        $data['discount'],
                        $data['discountType'],
                        $data['actualDiscountAmount'],
                        0,
                        2,
                        0,
                        $data['subTotal'],
                        $data['payment'],
                        $balance,
                        auth()->user()->id,
                        $data['status'],
                        $data['type'],
                        $data['status'] == 1? $data['receivingDate'] . ' ' . $data['receivingTime']: $orderCreatedDate,
                        date("Y-m-d H:i:s"),
                        $data['cash'],
                        $data['change'],
                        $orderId]);
    
            $sales = Sale::where('order_id', $orderId)->get();
            foreach ($sales as $key => $sale) 
            {
                if(posConfigurations()->maintain_inventory == 1)
                {
                    $inventory = $sale->item->inventory;
        
                    $inventory->update(['quantity' => $inventory->quantity + $sale->quantity, 
                                        'cost' => $inventory->cost + $sale->average_total_cost]);
                }
        
                $sale->delete();
            }
    
            for($i = 0; $i < sizeof($data['sales']); $i++)
            {
                DB::insert("insert into sales (order_id, item_id, quantity, price, discount, discount_type, discount_amount, 
                                            tax, tax_type, tax_amount, unit_price, created_at, updated_at, 
                                            average_unit_cost, average_total_cost) values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)", 
                                            [$orderId,
                                            $data['sales'][$i]['id'],
                                            $data['sales'][$i]['quantity'],
                                            $data['sales'][$i]['totalPrice'],
                                            $data['sales'][$i]['discount'],
                                            $data['sales'][$i]['discountType'],
                                            $data['sales'][$i]['actualDiscountAmount'],
                                            $data['sales'][$i]['tax'],
                                            $data['sales'][$i]['taxType'],
                                            0,
                                            $data['sales'][$i]['originalPrice'],
                                            $orderCreatedDate,
                                            date("Y-m-d H:i:s"),
                                            $data['sales'][$i]['averageUnitCost'],
                                            $data['sales'][$i]['averageUnitCost'] * $data['sales'][$i]['quantity']]);
    
                if(posConfigurations()->maintain_inventory == 1)
                {
                    $q = $data['sales'][$i]['quantity'];
                    DB::update("update raw_inventories set quantity = quantity-{$q}, cost = cost - " . ($data['sales'][$i]['averageUnitCost'] * $q) . " where item_id = ?", [$data['sales'][$i]['id']]);
                }
            }
    
            return json_encode([
                'message' => 1
            ]);
        }
        else
        {
            return json_encode([
                'message' => 0
            ]);
        }
    }

    public function edit()
    {
        if(allowed(10, 'edit'))
        {
            return view('sales.edit');
        }
        else
        {
            return redirect('/sale/create');
        }
    }

    public function return(Sale $sale)
    {
        $data = request()->validate([
            'deduct' => ''
        ]);

        $inventory = $sale->item->inventory;

        $order = $sale->order;
        
        if($data['deduct'] == 0)
        {
            $inventory->update(['quantity' => $inventory->quantity + $sale->quantity]);
        }
        else
        {
            RawWaste::create(['item_id' => $sale->item_id, 'quantity' => $sale->quantity]);
        }

        $sale->update(['status' => 2]);

        if(!$order->sales->contains(['status' => 1]))
        {
            $order->update(['status' => 2]);
        }
        
        return 'updated';
    }

    public function destroy(Sale $sale)
    {
        if(allowed(10, 'remove'))
        {
            if(posConfigurations()->maintain_inventory == 1)
            {
                $inventory = $sale->item->inventory;
    
                $inventory->update(['quantity' => $inventory->quantity + $sale->quantity]);
            }
    
            $order = $sale->order;
    
            $sale->delete();
    
            if(!$order->sales)
            {
                $order->delete();
            }
            else
            {
                
            }
    
            return 1;
        }
        else
        {
            return 0;
        }
    }

    public function earnings()
    { 
        if(allowed(12, 'view'))
        {
            // $purchaseBetweenDates = null;
            // $saleBetweenDates = null;
            // $profitBetweenDates = null;
            // $expenseBetweenDates = null;

            // return view('sales.earnings.earnings', compact('purchaseBetweenDates', 
            //                                         'saleBetweenDates', 'profitBetweenDates', 
            //                                         'expenseBetweenDates'));

            $fromDate = null;
            $toDate = null;
            $fixedIntervals = 8;

            $totalSaleOrders = null;

            $totalPurchaseOrders = null;

            $expenses = null;

            return view('reports.profit-loss', compact('totalSaleOrders', 
                                'totalPurchaseOrders', 'expenses', 'fromDate', 'toDate', 'fixedIntervals'));
        }
        else
        {
            return redirect('/sale/create');
        }
    }

    public function betweenDates()
    {
        if(allowed(12, 'view'))
        {
            $interval = request()->validate([
                'fromDate' => '',
                'toDate' => '',
                'fixed_intervals' => ''
            ]);
    
            $fromDate = date('Y-m-d', strtotime($interval['fromDate']));
            $toDate = date('Y-m-d', strtotime($interval['toDate']));
    
            DB::statement("SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));");
    
            $totalSaleOrders = DB::table('orders')
                    ->where([[DB::raw("date(orders.created_at)"), '>=', $fromDate],
                    [DB::raw("date(orders.created_at)"), '<=', $toDate]])
                    ->select(DB::raw("SUM(orders.total) AS totalOrders"),
                            DB::raw("SUM(orders.sub_total) AS subTotalOrders"), 
                            DB::raw("SUM(orders.discount_amount) AS totalDiscount"),
                            DB::raw("SUM(orders.balance) AS totalBalance"))->first();
    
            /*DB::raw("SUM(sales.price)/SUM(sales.quantity) AS average_unit_price")  line number 1095*/
    
            $itemsTotalSale = DB::table('sales')
                    ->where([[DB::raw("date(sales.created_at)"), '>=', $fromDate],
                            [DB::raw("date(sales.created_at)"), '<=', $toDate]])
                    ->select(DB::raw("AVG(sales.unit_price) AS average_unit_price"),
                            DB::raw("SUM(sales.price) AS totalSale"), 
                            // DB::raw("SUM(sales.average_total_cost)/SUM(sales.quantity) AS average_unit_cost"),
                            DB::raw("AVG(sales.average_unit_cost) AS average_unit_cost"),
                            DB::raw("SUM(sales.average_total_cost) AS totalPurchase"),
                            DB::raw("SUM(sales.quantity) AS totalQuantity"),
                            DB::raw("AVG(sales.discount_amount) AS average_discount"),
                            // DB::raw("SUM(sales.discount)/SUM(sales.quantity) AS average_discount"),
                            DB::raw("SUM(sales.discount_amount) AS totalDiscount"))
                    ->orderBy('totalSale', 'DESC')
                    ->first();
    
            $categoriesindividualTotalSale = DB::table('sales')
                    ->join('items', 'sales.item_id', '=', 'items.id')
                    ->join('categories', 'items.category_id', '=', 'categories.id')
                    ->where([[DB::raw("date(sales.created_at)"), '>=', $fromDate],
                                    [DB::raw("date(sales.created_at)"), '<=', $toDate]])
                        ->select(DB::raw("SUM(sales.price) AS totalSale"),
                                DB::raw("SUM(sales.average_total_cost) AS totalPurchase"),
                                'items.id AS item_id',
                                'categories.name AS category_name', 'categories.id')
                        ->groupBy('categories.id')
                        ->orderBy('totalSale', 'DESC')
                        ->get();
    
            $itemsindividualTotalSale = DB::table('sales')
                                        ->join('items', 'sales.item_id', '=', 'items.id')
                                        ->where([[DB::raw("date(sales.created_at)"), '>=', $fromDate],
                                            [DB::raw("date(sales.created_at)"), '<=', $toDate]])
                                ->select('items.name AS item_name', 'items.category_id AS category_id', 'sales.item_id AS item_id', 
                                        'items.unit_id AS unit_id',
                                        // DB::raw("SUM(sales.price)/SUM(sales.quantity) AS average_unit_price"),
                                        DB::raw("AVG(sales.unit_price) AS average_unit_price"),
                                        DB::raw("SUM(sales.price) AS totalSale"),
                                        // DB::raw("SUM(sales.average_total_cost)/SUM(sales.quantity) AS average_unit_cost"),
                                        DB::raw("AVG(sales.average_unit_cost) AS average_unit_cost"),
                                        DB::raw("SUM(sales.average_total_cost) AS totalPurchase"),
                                        DB::raw("SUM(sales.quantity) AS totalQuantity"),
                                        DB::raw("AVG(sales.discount_amount) AS average_discount"),
                                        // DB::raw("SUM(sales.discount)/SUM(sales.quantity) AS average_discount"),
                                        DB::raw("SUM(sales.discount_amount) AS totalDiscount"))
                                ->groupBy('sales.item_id')
                                ->orderBy('totalSale', 'DESC')
                                ->get();
    
            $totalPurchaseOrders = DB::table('purchase_orders')
                                ->where([[DB::raw("date(purchase_orders.created_at)"), '>=', $fromDate],
                                [DB::raw("date(purchase_orders.created_at)"), '<=', $toDate]])
                                ->select(DB::raw("SUM(purchase_orders.total) AS totalOrders"), 
                                        DB::raw("SUM(purchase_orders.balance) AS totalBalance"))->first();
    
            $expenses = Expense::where([[DB::raw("DATE(date)") , '>=', $fromDate], 
                                        [DB::raw("DATE(date)") , '<=', $toDate]])
                                ->select(DB::raw("SUM(cost) AS totalExpense"))
                                ->first();
    
            DB::statement("SET sql_mode=(SELECT CONCAT(@@sql_mode, ',ONLY_FULL_GROUP_BY'));");
    
            $fixedIntervals = $interval['fixed_intervals'];
    
            return view('reports.profit-loss', compact('totalSaleOrders', 
                                'totalPurchaseOrders', 'expenses', 'categoriesindividualTotalSale', 'itemsindividualTotalSale', 'itemsTotalSale', 'fromDate', 'toDate', 'fixedIntervals'));
    
        }
        else
        {
            return redirect('/sale/create');
        }
    }
}
