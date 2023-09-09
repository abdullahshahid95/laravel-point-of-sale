<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\PurchaseOrder;
use App\Purchase;
use App\Category;
use App\Supplier;
use App\Item;

use App\RawInventory;

use App\Rate;
use App\unit;
use App\Configuration;

class PurchasesController extends Controller
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
        $purchases = DB::table('purchases')
                ->join('items', 'purchases.item_id', '=', 'items.id')
                ->select('purchases.*', 'items.name AS item_name', 'items.unit_id AS item_unit_id')
                ->latest()->paginate(50);

        $categories = Category::all();
        $items = Item::all();

        $suppliers = Supplier::all();

        $total = '';
        $individualTotal = '';

        $fromDate = '';
        $toDate = '';
        $selectedCategories = [];
        $selectedItems = [];
        $selectedSuppliers = [];

        return view('purchases.index', compact('purchases', 'categories', 'items', 'fromDate', 'toDate', 'total', 'individualTotal', 'selectedCategories', 'selectedItems'));
    }

    public function purchasesReport()
    {
        if(allowed(12, 'view'))
        {
            // $purchases = DB::table('purchases')
            //   ->join('items', 'purchases.item_id', '=', 'items.id')
            //   ->select('purchases.*', 'items.name AS item_name', 'items.unit_id AS item_unit_id')
            //   ->latest()->paginate(50);

            $purchases = [];

            $categories = Category::all();
            $items = Item::all();

            $suppliers = Supplier::all();

            $total = '';
            $individualTotal = '';

            $status = 3;

            $fromDate = date("Y-m-d");
            $toDate = date("Y-m-d");
            $selectedCategories = [];
            $selectedItems = [];
            $selectedSuppliers = [];

            $total = DB::table('purchases')
            ->where([[DB::raw("date(purchases.created_at)"), '>=', $fromDate],
                [DB::raw("date(purchases.created_at)"), '<=', $toDate]])
            ->select(DB::raw("SUM(purchases.price) AS totalPurchase"))->first();

            $purchases = DB::table('purchases')
                    ->join('items', 'purchases.item_id', '=', 'items.id')
                    ->where([[DB::raw("date(purchases.created_at)"), '>=', $fromDate],
                        [DB::raw("date(purchases.created_at)"), '<=', $toDate]])
                        ->select('purchases.*', 'items.name AS item_name', 'items.unit_id AS item_unit_id')
                        ->latest()->paginate(50);

            return view('reports.purchases', compact('purchases', 'categories', 'items', 'fromDate', 'toDate', 'total', 'individualTotal', 'selectedCategories', 'selectedItems', 'status'));
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
            'selectedItems' => ''
        ]);

        $fromDate = date('Y-m-d', strtotime($data['fromDate']));
        $toDate = date('Y-m-d', strtotime($data['toDate']));

        $purchases = '';
        $total = '';
        $individualTotal = '';
        $selectedCategories = [];
        $selectedItems = [];
        $return = '';


        if(array_key_exists('selectedCategories', $data))
        {
            $total = DB::table('purchases')
                            ->join('items', 'purchases.item_id', '=', 'items.id')
                            ->join('categories', 'items.category_id', '=', 'categories.id')
                            ->where([[DB::raw("date(purchases.created_at)"), '>=', $fromDate],
                                [DB::raw("date(purchases.created_at)"), '<=', $toDate]])
                            ->whereIn('categories.id', $data['selectedCategories'])
                            ->select(DB::raw("SUM(purchases.price) AS totalPurchase"))->first();

            $purchases = DB::table('purchases')
                            ->join('items', 'purchases.item_id', '=', 'items.id')
                            ->join('categories', 'items.category_id', '=', 'categories.id')
                            ->where([[DB::raw("date(purchases.created_at)"), '>=', $fromDate],
                                [DB::raw("date(purchases.created_at)"), '<=', $toDate]])
                                ->whereIn('categories.id', $data['selectedCategories'])
                                ->select('purchases.*', 'items.name AS item_name', 'items.unit_id AS item_unit_id')
                                ->latest()->paginate(50);

            $individualTotal = DB::table('purchases')
                            ->join('items', 'purchases.item_id', '=', 'items.id')
                            ->join('categories', 'items.category_id', '=', 'categories.id')
                            ->where([[DB::raw("date(purchases.created_at)"), '>=', $fromDate],
                                            [DB::raw("date(purchases.created_at)"), '<=', $toDate]])
                                ->whereIn('categories.id', $data['selectedCategories'])
                                ->select(DB::raw("SUM(purchases.price) AS totalPurchase"),
                                        'categories.name AS category_name', 'categories.id AS category_id')
                                ->groupBy('category_id')->get();

            $selectedCategories = $data['selectedCategories'];
        }
        else if(array_key_exists('selectedItems', $data))
        {
            $total = Purchase::where([[DB::raw("date(created_at)"), '>=', $fromDate],
                            [DB::raw("date(created_at)"), '<=', $toDate]])
                        ->whereIn('item_id', $data['selectedItems'])
                        ->select(DB::raw("SUM(price) AS totalPurchase"))->first();

            $purchases = DB::table('purchases')
                        ->join('items', 'purchases.item_id', '=', 'items.id')
                        ->where([[DB::raw("date(purchases.created_at)"), '>=', $fromDate],
                            [DB::raw("date(purchases.created_at)"), '<=', $toDate]])
                            ->whereIn('purchases.item_id', $data['selectedItems'])
                            ->select('purchases.*', 'items.name AS item_name', 'items.unit_id AS item_unit_id')
                            ->latest()->paginate(50);

            $individualTotal = Purchase::where([[DB::raw("date(created_at)"), '>=', $fromDate],
                                            [DB::raw("date(created_at)"), '<=', $toDate]])
                                ->whereIn('item_id', $data['selectedItems'])
                                ->select(DB::raw("SUM(price) AS totalPurchase"),
                                        'item_id')
                                ->groupBy('item_id')->get();

            $selectedItems = $data['selectedItems'];
        }
        else
        {
            $total = Purchase::where([[DB::raw("date(created_at)"), '>=', $fromDate],
                            [DB::raw("date(created_at)"), '<=', $toDate]])
                        ->select(DB::raw("SUM(price) AS totalPurchase"))->first();

            $purchases = DB::table('purchases')
                        ->join('items', 'purchases.item_id', '=', 'items.id')
                        ->where([[DB::raw("date(purchases.created_at)"), '>=', $fromDate],
                            [DB::raw("date(purchases.created_at)"), '<=', $toDate]])
                            ->select('purchases.*', 'items.name AS item_name', 'items.unit_id AS item_unit_id')
                            ->latest()->paginate(50);
        }

        $categories = Category::all();
        $items = Item::all();
                                        
        return view('purchases.index', compact('purchases', 'categories', 'items', 'selectedCategories', 'selectedItems', 'fromDate', 'toDate', 'total', 'individualTotal'));
    }

    public function filterPurchasesReport()
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

        $purchases = '';
        $total = '';
        $individualTotal = '';
        $selectedCategories = [];
        $selectedItems = [];
        $status = $data['status'];

        if($data['status'] == 1 || $data['status'] == 2)
        {
            if(array_key_exists('selectedCategories', $data))
            {
                $total = DB::table('purchases')
                                ->join('items', 'purchases.item_id', '=', 'items.id')
                                ->join('categories', 'items.category_id', '=', 'categories.id')
                                ->join('purchase_orders', 'purchases.purchase_order_id', '=', 'purchase_orders.id')
                                ->where([[DB::raw("date(purchases.created_at)"), '>=', $fromDate],
                                    [DB::raw("date(purchases.created_at)"), '<=', $toDate],
                                    ["purchase_orders.status", '=', $data['status']]])
                                ->whereIn('categories.id', $data['selectedCategories'])
                                ->select(DB::raw("SUM(purchases.price) AS totalPurchase"))->first();
    
                $purchases = DB::table('purchases')
                                ->join('items', 'purchases.item_id', '=', 'items.id')
                                ->join('categories', 'items.category_id', '=', 'categories.id')
                                ->join('purchase_orders', 'purchases.purchase_order_id', '=', 'purchase_orders.id')
                                ->where([[DB::raw("date(purchases.created_at)"), '>=', $fromDate],
                                    [DB::raw("date(purchases.created_at)"), '<=', $toDate],
                                    ["purchase_orders.status", '=', $data['status']]])
                                    ->whereIn('categories.id', $data['selectedCategories'])
                                    ->select('purchases.*', 'items.name AS item_name', 'items.unit_id AS item_unit_id')
                                    ->latest()->paginate(50);
    
                $individualTotal = DB::table('purchases')
                                ->join('items', 'purchases.item_id', '=', 'items.id')
                                ->join('categories', 'items.category_id', '=', 'categories.id')
                                ->join('purchase_orders', 'purchases.purchase_order_id', '=', 'purchase_orders.id')
                                ->where([[DB::raw("date(purchases.created_at)"), '>=', $fromDate],
                                                [DB::raw("date(purchases.created_at)"), '<=', $toDate],
                                                ["purchase_orders.status", '=', $data['status']]])
                                    ->whereIn('categories.id', $data['selectedCategories'])
                                    ->select(DB::raw("SUM(purchases.price) AS totalPurchase"),
                                            'categories.name AS category_name', 'categories.id AS category_id')
                                    ->groupBy('category_id')->get();
    
                $selectedCategories = $data['selectedCategories'];
            }
            else if(array_key_exists('selectedItems', $data))
            {
                $total = DB::table('purchases')
                            ->join('purchase_orders', 'purchases.purchase_order_id', '=', 'purchase_orders.id')
                            ->where([[DB::raw("date(purchases.created_at)"), '>=', $fromDate],
                                [DB::raw("date(purchases.created_at)"), '<=', $toDate],
                                ["purchase_orders.status", '=', $data['status']]])
                            ->whereIn('purchases.item_id', $data['selectedItems'])
                            ->select(DB::raw("SUM(purchases.price) AS totalPurchase"))->first();
    
                $purchases = DB::table('purchases')
                            ->join('items', 'purchases.item_id', '=', 'items.id')
                            ->join('purchase_orders', 'purchases.purchase_order_id', '=', 'purchase_orders.id')
                            ->where([[DB::raw("date(purchases.created_at)"), '>=', $fromDate],
                                [DB::raw("date(purchases.created_at)"), '<=', $toDate],
                                ["purchase_orders.status", '=', $data['status']]])
                                ->whereIn('purchases.item_id', $data['selectedItems'])
                                ->select('purchases.*', 'items.name AS item_name', 'items.unit_id AS item_unit_id')
                                ->latest()->paginate(50);
    
                $individualTotal = DB::table('purchases')
                                ->join('items', 'purchases.item_id', '=', 'items.id')
                                ->join('purchase_orders', 'purchases.purchase_order_id', '=', 'purchase_orders.id')
                                ->where([[DB::raw("date(purchases.created_at)"), '>=', $fromDate],
                                        [DB::raw("date(purchases.created_at)"), '<=', $toDate],
                                        ["purchase_orders.status", '=', $data['status']]])
                                    ->whereIn('purchases.item_id', $data['selectedItems'])
                                    ->select(DB::raw("SUM(purchases.price) AS totalPurchase"),
                                                'purchases.item_id', 'items.name As item_name')
                                    ->groupBy('purchases.item_id')->get();
    
                $selectedItems = $data['selectedItems'];
            }
            else
            {
                $total = DB::table('purchases')
                            ->join('purchase_orders', 'purchases.purchase_order_id', '=', 'purchase_orders.id')
                            ->where([[DB::raw("date(purchases.created_at)"), '>=', $fromDate],
                                [DB::raw("date(purchases.created_at)"), '<=', $toDate],
                                ["purchase_orders.status", '=', $data['status']]])
                            ->select(DB::raw("SUM(purchases.price) AS totalPurchase"))->first();

                $purchases = DB::table('purchases')
                            ->join('items', 'purchases.item_id', '=', 'items.id')
                            ->join('purchase_orders', 'purchases.purchase_order_id', '=', 'purchase_orders.id')
                            ->where([[DB::raw("date(purchases.created_at)"), '>=', $fromDate],
                                [DB::raw("date(purchases.created_at)"), '<=', $toDate],
                                ["purchase_orders.status", '=', $data['status']]])
                                ->select('purchases.*', 'items.name AS item_name', 'items.unit_id AS item_unit_id')
                                ->latest()->paginate(50);
            }
        }
        else
        {
            if(array_key_exists('selectedCategories', $data))
            {
                $total = DB::table('purchases')
                                ->join('items', 'purchases.item_id', '=', 'items.id')
                                ->join('categories', 'items.category_id', '=', 'categories.id')
                                ->where([[DB::raw("date(purchases.created_at)"), '>=', $fromDate],
                                    [DB::raw("date(purchases.created_at)"), '<=', $toDate]])
                                ->whereIn('categories.id', $data['selectedCategories'])
                                ->select(DB::raw("SUM(purchases.price) AS totalPurchase"))->first();
    
                $purchases = DB::table('purchases')
                                ->join('items', 'purchases.item_id', '=', 'items.id')
                                ->join('categories', 'items.category_id', '=', 'categories.id')
                                ->where([[DB::raw("date(purchases.created_at)"), '>=', $fromDate],
                                    [DB::raw("date(purchases.created_at)"), '<=', $toDate]])
                                    ->whereIn('categories.id', $data['selectedCategories'])
                                    ->select('purchases.*', 'items.name AS item_name', 'items.unit_id AS item_unit_id')
                                    ->latest()->paginate(50);
    
                $individualTotal = DB::table('purchases')
                                ->join('items', 'purchases.item_id', '=', 'items.id')
                                ->join('categories', 'items.category_id', '=', 'categories.id')
                                ->where([[DB::raw("date(purchases.created_at)"), '>=', $fromDate],
                                        [DB::raw("date(purchases.created_at)"), '<=', $toDate]])
                                    ->whereIn('categories.id', $data['selectedCategories'])
                                    ->select(DB::raw("SUM(purchases.price) AS totalPurchase"),
                                            'categories.name AS category_name', 'categories.id AS category_id')
                                    ->groupBy('category_id')->get();
    
                $selectedCategories = $data['selectedCategories'];
            }
            else if(array_key_exists('selectedItems', $data))
            {
                $total = DB::table('purchases')
                            ->where([[DB::raw("date(purchases.created_at)"), '>=', $fromDate],
                                [DB::raw("date(purchases.created_at)"), '<=', $toDate]])
                            ->whereIn('purchases.item_id', $data['selectedItems'])
                            ->select(DB::raw("SUM(purchases.price) AS totalPurchase"))->first();
    
                $purchases = DB::table('purchases')
                            ->join('items', 'purchases.item_id', '=', 'items.id')
                            ->where([[DB::raw("date(purchases.created_at)"), '>=', $fromDate],
                                [DB::raw("date(purchases.created_at)"), '<=', $toDate]])
                                ->whereIn('purchases.item_id', $data['selectedItems'])
                                ->select('purchases.*', 'items.name AS item_name', 'items.unit_id AS item_unit_id')
                                ->latest()->paginate(50);
    
                $individualTotal = DB::table('purchases')
                                ->join('items', 'purchases.item_id', '=', 'items.id')
                                ->where([[DB::raw("date(purchases.created_at)"), '>=', $fromDate],
                                        [DB::raw("date(purchases.created_at)"), '<=', $toDate]])
                                    ->whereIn('purchases.item_id', $data['selectedItems'])
                                    ->select(DB::raw("SUM(purchases.price) AS totalPurchase"),
                                            'purchases.item_id', 'items.name AS item_name')
                                    ->groupBy('purchases.item_id')->get();
                $selectedItems = $data['selectedItems'];
            }
            else
            {
                $total = DB::table('purchases')
                            ->where([[DB::raw("date(purchases.created_at)"), '>=', $fromDate],
                                [DB::raw("date(purchases.created_at)"), '<=', $toDate]])
                            ->select(DB::raw("SUM(purchases.price) AS totalPurchase"))->first();
    
                $purchases = DB::table('purchases')
                            ->join('items', 'purchases.item_id', '=', 'items.id')
                            ->where([[DB::raw("date(purchases.created_at)"), '>=', $fromDate],
                                [DB::raw("date(purchases.created_at)"), '<=', $toDate]])
                                ->select('purchases.*', 'items.name AS item_name', 'items.unit_id AS item_unit_id')
                                ->latest()->paginate(50);
            }
        }

        $categories = Category::all();
        $items = Item::all();
                                        
        return view('reports.purchases', compact('purchases', 'categories', 'items', 'selectedCategories', 'selectedItems', 'fromDate', 'toDate', 'total', 'individualTotal', 'status'));
    }

    public function create()
    {
        if(allowed(6, 'make'))
        {
            return view('purchases.create');
        }
        else
        {
            return redirect('/sale/create');
        }
    }

    public function getPOPData($edit = 0)
    {
        DB::statement("SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));");

        $items = DB::table('items')
                    ->leftJoin('purchases', 'items.id', '=', 'purchases.item_id')
                    ->join('raw_inventories', 'raw_inventories.item_id', '=', 'items.id')
                    ->join('units', 'items.unit_id', '=', 'units.id')
                    ->join('rates', 'rates.item_id', '=', 'items.id')
                    ->select('items.*', 'rates.purchase_price AS price', 'raw_inventories.quantity AS quantity',
                            DB::raw("raw_inventories.cost/raw_inventories.quantity AS average_unit_cost"), 
                            'units.id AS unit_id',
                            'units.name AS unit_name', 'units.symbol AS unit_symbol',
                            'units.fraction_name AS unit_fraction_name',
                            'units.fraction_value AS unit_fraction_value', DB::raw("COUNT(purchases.item_id) AS itemsCount"))
                    ->where('items.status', '=', 1)
                    ->groupBy('items.id')
                    ->orderBy('itemsCount', 'DESC')
                    ->orderBy('items.name', 'ASC')
                    ->get();

        DB::statement("SET sql_mode=(SELECT CONCAT(@@sql_mode, ',ONLY_FULL_GROUP_BY'));");

        $categories = Category::all();
        $suppliers = Supplier::where('id', '!=', 1)->get();

        return json_encode([
            'items' => $items,
            'categories' => $categories,
            'suppliers' => $suppliers
        ]);
    }

    /*public function create()
    {
        DB::statement("SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));");

        $items = DB::table('items')
                    ->leftJoin('purchases', 'items.id', '=', 'purchases.item_id')
                    ->join('raw_inventories', 'raw_inventories.item_id', '=', 'items.id')
                    ->join('units', 'items.unit_id', '=', 'units.id')
                    ->join('rates', 'rates.item_id', '=', 'items.id')
                    ->select('items.*', 'rates.purchase_price AS price', 'rates.sale_price', 'raw_inventories.quantity AS quantity', 
                            'units.id AS unit_id',
                            'units.name AS unit_name', 'units.symbol AS unit_symbol',
                            'units.fraction_name AS unit_fraction_name',
                            'units.fraction_value AS unit_fraction_value', DB::raw("COUNT(purchases.item_id) AS itemsCount"))
                    ->groupBy('items.id')
                    ->orderBy('itemsCount', 'DESC')
                    ->get();

        DB::statement("SET sql_mode=(SELECT CONCAT(@@sql_mode, ',ONLY_FULL_GROUP_BY'));");

        $categories = Category::all();
        $suppliers = Supplier::where('id', '!=', 1)->get();

        return view('purchases.create', compact('items', 'categories', 'suppliers'));
    }*/

    public function store(Request $request, $print = null)
    {
        if(allowed(6, 'make'))
        {
            $data = json_decode($request->getContent(), true);

            $order = PurchaseOrder::create(['receipt_number' => $data['receiptNumber'], 
                            'supplier_id' => $data['supplierId'],
                            'total' => $data['subTotal'],
                            'payment' => $data['payment'],
                            'balance' => $data['subTotal'] - $data['payment'],
                            'user_id' => auth()->user()->id,
                            'status' => $data['status'], // status 2 received, 1 not received
                            'receiving_date' => $data['status'] == 1? $data['receivingDate'] . ' ' . $data['receivingTime']: date("Y-m-d H:i:s")]);
    
            for($i = 0; $i < sizeof($data['purchases']); $i++)
            {
                $sale = Purchase::create(["purchase_order_id" => $order->id,
                    "item_id" => $data['purchases'][$i]['id'],
                    "quantity" => $data['purchases'][$i]['quantity'],
                    "price" => $data['purchases'][$i]['totalPrice'],
                    "unit_cost" => $data['purchases'][$i]['price']
                ]);
    
                if($data['status'] == 2)
                {
                    $rawInventory = RawInventory::where('item_id', $data['purchases'][$i]['id'])->first();
                    $rawInventory->update(['quantity' => $rawInventory->quantity + $data['purchases'][$i]['quantity'],
                                            'cost' => $rawInventory->cost + $data['purchases'][$i]['totalPrice']]);    
                }
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

    public function destroy(Purchase $purchase)
    {
        if(allowed(6, 'remove'))
        {
            $price = $purchase->price;

            /*if(posConfigurations()->maintain_inventory == 1)
            {
                $inventory = $purchase->item->inventory;
    
                $inventory->update(['quantity' => $inventory->quantity - $purchase->quantity, 
                                    'cost' => $inventory->cost - $purchase->price]);
            }*/
    
            $purchaseOrder = $purchase->purchaseOrder;
    
            $purchase->delete();
    
            if(!$purchaseOrder->purchases || count($purchaseOrder->purchases) < 1)
            {
                $purchaseOrder->delete();
            }
            else
            {
                $purchaseOrder->update([
                    'total' => $purchaseOrder->total - $price,
                    'balance' => (($purchaseOrder->total - $price) - $purchaseOrder->payment) >= 0? 
                                    (($purchaseOrder->total - $price) - $purchaseOrder->payment): 0,
                    'payment' => ($purchaseOrder->payment <= $purchaseOrder->total - $price)? 
                                ($purchaseOrder->payment): $purchaseOrder->total - $price
                ]);
            }
    
            return 1;
        }
        else
        {
            return 0;
        }
    }
}
