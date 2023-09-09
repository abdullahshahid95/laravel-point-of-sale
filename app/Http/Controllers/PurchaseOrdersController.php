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
use App\Supplier;
use App\Expense;
use App\Configuration;

class PurchaseOrdersController extends Controller
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
        if(allowed(14, 'view'))
        {
            $orders = PurchaseOrder::latest()->paginate(50);
            $suppliers = Supplier::all();
            
            $fromDate = '';
            $toDate = '';
            $receiptNumber = '';
            $selectedSuppliers = [];
            $total = '';
    
            $status = 3;
    
            return view('purchase-orders.index', compact('orders', 'suppliers', 'fromDate', 'toDate', 'receiptNumber', 'selectedSuppliers', 'status', 'total'));
    
        }
        else
        {
            return redirect('/sale/create');
        }
    }

    public function ordersReport()
    {
        if(allowed(12, 'view'))
        {
            // $orders = PurchaseOrder::latest()->paginate(50);
            $orders = [];
            $suppliers = Supplier::all();
            
            $fromDate = date("Y-m-d");
            $toDate = date("Y-m-d");
            $receiptNumber = '';
            $selectedSuppliers = [];
            $total = '';

            $status = 3;

            $ordersWhereArray = $status == 3? [[DB::raw("date(created_at)"), '>=', $fromDate],
            [DB::raw("date(created_at)"), '<=', $toDate]]: [[DB::raw("date(created_at)"), '>=', $fromDate],
            [DB::raw("date(created_at)"), '<=', $toDate],
            ['status', '=', $data['status']]];

            $totalWhereArray = $status == 3? [[DB::raw("date(purchase_orders.created_at)"), '>=', $fromDate],
            [DB::raw("date(purchase_orders.created_at)"), '<=', $toDate]]: [[DB::raw("date(purchase_orders.created_at)"), '>=', $fromDate],
            [DB::raw("date(purchase_orders.created_at)"), '<=', $toDate], ['purchase_orders.status', '=', $data['status']]];

            $orders = PurchaseOrder::where($ordersWhereArray)
                                            ->latest()->paginate(50);

            $total = DB::table('purchase_orders')
                    ->where($totalWhereArray)
                    ->select(DB::raw("SUM(purchase_orders.total) AS totalOrders"), 
                            DB::raw("SUM(purchase_orders.balance) AS totalBalance"))->first();

            if($total)
            $total = (array)$total;

            return view('reports.purchase-orders', compact('orders', 'suppliers', 'fromDate', 'toDate', 'receiptNumber', 'selectedSuppliers', 'status', 'total'));
        }
        else
        {
            return redirect('/sale/create');
        }
    }

    public function filter()
    {
        $data = request()->validate([
            'fromDate' => 'required_without_all:receipt_number',
            'toDate' => 'required_without_all:receipt_number',
            'selectedSuppliers' => '',
            'receipt_number' => 'required_without_all:fromDate,toDate,selectedSuppliers',
            'status' => ''
        ]);

        $fromDate = "";
        $toDate = "";

        $receiptNumber = "";
        $selectedSuppliers = [];

        $status = $data['status'];

        if(array_key_exists('fromDate', $data) && $data['fromDate'])
        {
            $fromDate = date('Y-m-d', strtotime($data['fromDate']));
        }

        if(array_key_exists('toDate', $data) && $data['toDate'])
        {
            $toDate = date('Y-m-d', strtotime($data['toDate']));
        }

        $receiptNumber = "";

        if(array_key_exists("receipt_number", $data) && $data['receipt_number'])
            $receiptNumber = $data['receipt_number'];

        if(array_key_exists("selectedSuppliers", $data) && $data['selectedSuppliers'])
            $selectedSuppliers = $data['selectedSuppliers'];

        if(sizeof($selectedSuppliers) > 0)
        {
            if($fromDate && $toDate && !$receiptNumber)
            {
                $ordersWhereArray = $status == 3? [[DB::raw("date(created_at)"), '>=', $fromDate],
                [DB::raw("date(created_at)"), '<=', $toDate]]: [[DB::raw("date(created_at)"), '>=', $fromDate],
                [DB::raw("date(created_at)"), '<=', $toDate],
                ['status', '=', $data['status']]];

                $totalWhereArray = $status == 3? [[DB::raw("date(purchase_orders.created_at)"), '>=', $fromDate],
                [DB::raw("date(purchase_orders.created_at)"), '<=', $toDate]]: [[DB::raw("date(purchase_orders.created_at)"), '>=', $fromDate],
                [DB::raw("date(purchase_orders.created_at)"), '<=', $toDate], ['purchase_orders.status', '=', $data['status']]];

                $orders = PurchaseOrder::where($ordersWhereArray)
                            ->whereIn('supplier_id', $selectedSuppliers)
                            ->latest()->paginate(50);
    
                $total = DB::table('purchase_orders')
                        ->where($totalWhereArray)
                        ->whereIn('supplier_id', $selectedSuppliers)
                        ->select(DB::raw("SUM(purchase_orders.total) AS totalOrders"), 
                                DB::raw("SUM(purchase_orders.balance) AS totalBalance"))->first();
    
            }
            elseif($receiptNumber && !$fromDate && !$toDate)
            {
                $ordersWhereArray = $status == 3? [['receipt_number', 'LIKE', "%$receiptNumber%"]]: [['receipt_number', 'LIKE', "%$receiptNumber%"],
                ['status', '=', $data['status']]];

                $orders = PurchaseOrder::where($ordersWhereArray)
                                        ->whereIn('supplier_id', $selectedSuppliers)
                                        ->latest()->paginate(50);
                $total = '';
            }
            else
            {
                $ordersWhereArray = $status == 3? [[DB::raw("date(created_at)"), '>=', $fromDate],
                [DB::raw("date(created_at)"), '<=', $toDate],
                ['receipt_number', 'LIKE', "%$receiptNumber%"]]: [[DB::raw("date(created_at)"), '>=', $fromDate],
                [DB::raw("date(created_at)"), '<=', $toDate],
                ['receipt_number', 'LIKE', "%$receiptNumber%"],
                ['status', '=', $data['status']]];

                $orders = PurchaseOrder::where($ordersWhereArray)
                            ->whereIn('supplier_id', $selectedSuppliers)
                            ->latest()->paginate(50);
    
                $total = '';
            }
        }
        else
        {
            if($fromDate && $toDate && !$receiptNumber)
            {
                $ordersWhereArray = $status == 3? [[DB::raw("date(created_at)"), '>=', $fromDate],
                [DB::raw("date(created_at)"), '<=', $toDate]]: [[DB::raw("date(created_at)"), '>=', $fromDate],
                [DB::raw("date(created_at)"), '<=', $toDate],
                ['status', '=', $data['status']]];

                $totalWhereArray = $status == 3? [[DB::raw("date(purchase_orders.created_at)"), '>=', $fromDate],
                [DB::raw("date(purchase_orders.created_at)"), '<=', $toDate]]: [[DB::raw("date(purchase_orders.created_at)"), '>=', $fromDate],
                [DB::raw("date(purchase_orders.created_at)"), '<=', $toDate], ['purchase_orders.status', '=', $data['status']]];

                $orders = PurchaseOrder::where($ordersWhereArray)
                                                ->latest()->paginate(50);
    
                $total = DB::table('purchase_orders')
                        ->where($totalWhereArray)
                        ->select(DB::raw("SUM(purchase_orders.total) AS totalOrders"), 
                                DB::raw("SUM(purchase_orders.balance) AS totalBalance"))->first();
    
            }
            elseif($receiptNumber && !$fromDate && !$toDate)
            {
                $ordersWhereArray = $status == 3? [['receipt_number', 'LIKE', "%$receiptNumber%"]]: [['receipt_number', 'LIKE', "%$receiptNumber%"],
                ['status', '=', $data['status']]];

                $orders = PurchaseOrder::where($ordersWhereArray)
                                ->latest()->paginate(50);
                $total = '';
            }
            else
            {
                $ordersWhereArray = $status == 3? [[DB::raw("date(created_at)"), '>=', $fromDate],
                [DB::raw("date(created_at)"), '<=', $toDate],
                ['receipt_number', 'LIKE', "%$receiptNumber%"]]: [[DB::raw("date(created_at)"), '>=', $fromDate],
                [DB::raw("date(created_at)"), '<=', $toDate],
                ['receipt_number', 'LIKE', "%$receiptNumber%"],
                ['status', '=', $data['status']]];

                $orders = PurchaseOrder::where($ordersWhereArray)
                                                ->latest()->paginate(50);
    
                $total = '';
            }
        }

        if($total)
            $total = (array)$total;

        $suppliers = Supplier::all();
                                        
        return view('purchase-orders.index', compact('orders', 'suppliers', 'fromDate', 'toDate', 'receiptNumber', 'selectedSuppliers', 'status', 'total'));
    }

    public function filterOrdersReport()
    {
        $data = request()->validate([
            'fromDate' => 'required_without_all:receipt_number',
            'toDate' => 'required_without_all:receipt_number',
            'selectedSuppliers' => '',
            'receipt_number' => 'required_without_all:fromDate,toDate,selectedSuppliers',
            'status' => ''
        ]);

        $fromDate = "";
        $toDate = "";

        $receiptNumber = "";
        $selectedSuppliers = [];

        $status = $data['status'];

        if(array_key_exists('fromDate', $data) && $data['fromDate'])
        {
            $fromDate = date('Y-m-d', strtotime($data['fromDate']));
        }

        if(array_key_exists('toDate', $data) && $data['toDate'])
        {
            $toDate = date('Y-m-d', strtotime($data['toDate']));
        }

        $receiptNumber = "";

        if(array_key_exists("receipt_number", $data) && $data['receipt_number'])
            $receiptNumber = $data['receipt_number'];

        if(array_key_exists("selectedSuppliers", $data) && $data['selectedSuppliers'])
            $selectedSuppliers = $data['selectedSuppliers'];

            if(sizeof($selectedSuppliers) > 0)
            {
                if($fromDate && $toDate && !$receiptNumber)
                {
                    $ordersWhereArray = $status == 3? [[DB::raw("date(created_at)"), '>=', $fromDate],
                    [DB::raw("date(created_at)"), '<=', $toDate]]: [[DB::raw("date(created_at)"), '>=', $fromDate],
                    [DB::raw("date(created_at)"), '<=', $toDate],
                    ['status', '=', $data['status']]];
    
                    $totalWhereArray = $status == 3? [[DB::raw("date(purchase_orders.created_at)"), '>=', $fromDate],
                    [DB::raw("date(purchase_orders.created_at)"), '<=', $toDate]]: [[DB::raw("date(purchase_orders.created_at)"), '>=', $fromDate],
                    [DB::raw("date(purchase_orders.created_at)"), '<=', $toDate], ['purchase_orders.status', '=', $data['status']]];
    
                    $orders = PurchaseOrder::where($ordersWhereArray)
                                ->whereIn('supplier_id', $selectedSuppliers)
                                ->latest()->paginate(50);
        
                    $total = DB::table('purchase_orders')
                            ->where($totalWhereArray)
                            ->whereIn('supplier_id', $selectedSuppliers)
                            ->select(DB::raw("SUM(purchase_orders.total) AS totalOrders"), 
                                    DB::raw("SUM(purchase_orders.balance) AS totalBalance"))->first();
        
                }
                elseif($receiptNumber && !$fromDate && !$toDate)
                {
                    $ordersWhereArray = $status == 3? [['receipt_number', 'LIKE', "%$receiptNumber%"]]: [['receipt_number', 'LIKE', "%$receiptNumber%"],
                    ['status', '=', $data['status']]];
    
                    $orders = PurchaseOrder::where($ordersWhereArray)
                                            ->whereIn('supplier_id', $selectedSuppliers)
                                            ->latest()->paginate(50);
                    $total = '';
                }
                else
                {
                    $ordersWhereArray = $status == 3? [[DB::raw("date(created_at)"), '>=', $fromDate],
                    [DB::raw("date(created_at)"), '<=', $toDate],
                    ['receipt_number', 'LIKE', "%$receiptNumber%"]]: [[DB::raw("date(created_at)"), '>=', $fromDate],
                    [DB::raw("date(created_at)"), '<=', $toDate],
                    ['receipt_number', 'LIKE', "%$receiptNumber%"],
                    ['status', '=', $data['status']]];
    
                    $orders = PurchaseOrder::where($ordersWhereArray)
                                ->whereIn('supplier_id', $selectedSuppliers)
                                ->latest()->paginate(50);
        
                    $total = '';
                }
            }
            else
            {
                if($fromDate && $toDate && !$receiptNumber)
                {
                    $ordersWhereArray = $status == 3? [[DB::raw("date(created_at)"), '>=', $fromDate],
                    [DB::raw("date(created_at)"), '<=', $toDate]]: [[DB::raw("date(created_at)"), '>=', $fromDate],
                    [DB::raw("date(created_at)"), '<=', $toDate],
                    ['status', '=', $data['status']]];
    
                    $totalWhereArray = $status == 3? [[DB::raw("date(purchase_orders.created_at)"), '>=', $fromDate],
                    [DB::raw("date(purchase_orders.created_at)"), '<=', $toDate]]: [[DB::raw("date(purchase_orders.created_at)"), '>=', $fromDate],
                    [DB::raw("date(purchase_orders.created_at)"), '<=', $toDate], ['purchase_orders.status', '=', $data['status']]];
    
                    $orders = PurchaseOrder::where($ordersWhereArray)
                                                    ->latest()->paginate(50);
        
                    $total = DB::table('purchase_orders')
                            ->where($totalWhereArray)
                            ->select(DB::raw("SUM(purchase_orders.total) AS totalOrders"), 
                                    DB::raw("SUM(purchase_orders.balance) AS totalBalance"))->first();
        
                }
                elseif($receiptNumber && !$fromDate && !$toDate)
                {
                    $ordersWhereArray = $status == 3? [['receipt_number', 'LIKE', "%$receiptNumber%"]]: [['receipt_number', 'LIKE', "%$receiptNumber%"],
                    ['status', '=', $data['status']]];
    
                    $orders = PurchaseOrder::where($ordersWhereArray)
                                    ->latest()->paginate(50);
                    $total = '';
                }
                else
                {
                    $ordersWhereArray = $status == 3? [[DB::raw("date(created_at)"), '>=', $fromDate],
                    [DB::raw("date(created_at)"), '<=', $toDate],
                    ['receipt_number', 'LIKE', "%$receiptNumber%"]]: [[DB::raw("date(created_at)"), '>=', $fromDate],
                    [DB::raw("date(created_at)"), '<=', $toDate],
                    ['receipt_number', 'LIKE', "%$receiptNumber%"],
                    ['status', '=', $data['status']]];
    
                    $orders = PurchaseOrder::where($ordersWhereArray)
                                                    ->latest()->paginate(50);
        
                    $total = '';
                }
            }

        if($total)
            $total = (array)$total;

        $suppliers = Supplier::all();
                                        
        return view('reports.purchase-orders', compact('orders', 'suppliers', 'fromDate', 'toDate', 'receiptNumber', 'selectedSuppliers', 'status', 'total'));
    }

    public function updateStatus(PurchaseOrder $purchaseOrder)
    {
        for($i = 0; $i < sizeof($purchaseOrder->purchases); $i++)
        {
            $rawInventory = RawInventory::where('item_id', $purchaseOrder->purchases[$i]['item_id'])->first();
            $rawInventory->update(['quantity' => $rawInventory->quantity + $purchaseOrder->purchases[$i]['quantity'],
                                    'cost' => $rawInventory->cost + $purchaseOrder->purchases[$i]['price']]);    
        }

        $purchaseOrder->update(['status' => 2]);

        return 1;
    }

    public function updateBalance(PurchaseOrder $purchaseOrder)
    {
        $data = request()->validate([
            'payment' => 'required',
        ]);

        $purchaseOrder->update(['payment' => $purchaseOrder->payment + $data['payment'], 
                            'balance' => ($purchaseOrder->balance - $data['payment'])]);

        return 1;
        // return redirect('/purchase-orders');
    }

    public function show(PurchaseOrder $purchaseOrder, $print = null)
    {
        if(allowed(14, 'view'))
        {
            $purchases = $purchaseOrder->purchases()->get();

            $total = '';
            $individualTotal = '';
    
            $fromDate = '';
            $toDate = '';
            $selectedItems = [];
            $return = '';
    
            return view('purchase-orders.show', compact('print', 'purchaseOrder', 'purchases', 'fromDate', 'toDate', 'total', 'individualTotal', 'selectedItems', 'return'));    
        }
        else
        {
            return redirect('/sale/create');
        }
    }

    public function destroy(PurchaseOrder $purchaseOrder)
    {
        if(allowed(14, 'remove'))
        {
            $purchases = $purchaseOrder->purchases;

            foreach ($purchases as $key => $purchase) 
            {
                /*if(posConfigurations()->maintain_inventory == 1)
                {
                    $inventory = $purchase->item->inventory;
        
                    $inventory->update(['quantity' => $inventory->quantity - $purchase->quantity,   
                                        'cost' => $inventory->cost - $purchase->price]);
                }*/
        
                $purchase->delete();
            }
    
            $purchaseOrder->delete();
    
            return 1;    
        }
        else
        {
            return 0;
        }
    }
}
