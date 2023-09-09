<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
// use Mike42\Escpos\PrintConnectors\FilePrintConnector;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
// use Mike42\Escpos\CapabilityProfile;
use Mike42\Escpos\Printer;
use App\CustomStuff\PrintCustomItem;

use App\Order;
use App\Sale;
use App\Item;
use App\RawInventory;
use App\RawWaste;
use App\Unit;
use App\Rate;
use App\Customer;
use App\Purchase;
use App\Expense;
use App\Configuration;

class OrdersController extends Controller
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
        if(allowed(9, 'view'))
        {
            $orders = Order::latest()->paginate(50);
            $customers = Customer::all();
            
            $fromDate = '';
            $toDate = '';
            $receiptNumber = '';
            $selectedCustomers = [];
            $total = '';
    
            $status = 3;
            $typeTakeAway = true;
            $typeHomeDelivery = true;
            $typeDineIn = true;
            $paidUnpaid = 3;
    
            return view('orders.index', compact('orders', 'customers', 'fromDate', 'toDate', 'receiptNumber', 'selectedCustomers', 'status', 'typeTakeAway', 'typeHomeDelivery', 'typeDineIn', 'paidUnpaid', 'total'));    
        }
        else
        {
            return redirect('/sale/create');
        }
    }

    public function getOrder($receiptNumber)
    {
        if(!Order::where('receipt_number', $receiptNumber)->first())
        {
            return json_encode([
                "message" => 2,
            ]);
        }

       $sales = DB::table("sales")
                    ->join("orders", "sales.order_id", "=", "orders.id")
                    ->join("items", "sales.item_id", "=", "items.id")
                    ->select("sales.*", "items.name AS item_name", "items.unit_id AS unit_id")
                    ->where("orders.receipt_number", "=", $receiptNumber)
                    ->get();

        $order = DB::table("orders")
                    ->join("customers", "orders.customer_id", "=", "customers.id")
                    ->select("orders.*", DB::raw("DATE(orders.receiving_date) AS receiving_date"), 
                    DB::raw("TIME(receiving_date) AS receiving_time"), "customers.name AS customer_name")
                    ->where("orders.receipt_number", "=", $receiptNumber)
                    ->first();
        //DB::raw("TIME_FORMAT(orders.receiving_date, '%r') AS receiving_time")
        return json_encode([
            "message" => 1,
            "sales" => $sales,
            "order" => $order
        ]);
    }

    public function ordersReport()
    {
        if(allowed(12, 'view'))
        {
            $customers = Customer::all();
        
            $fromDate = date("Y-m-d");
            $toDate = date("Y-m-d");
            $receiptNumber = '';
            $selectedCustomers = [];
            
            $status = 3;
            $typeTakeAway = true;
            $typeHomeDelivery = true;
            $typeDineIn = true;
            $paidUnpaid = 3;
    
            $ordersWhereArray = $status == 3? [[DB::raw("date(created_at)"), '>=', $fromDate],
            [DB::raw("date(created_at)"), '<=', $toDate], ['payment', ($paidUnpaid == 1? '<' : ($paidUnpaid == 2? '=' : '<=')), DB::raw('sub_total')]]: [[DB::raw("date(created_at)"), '>=', $fromDate],
            [DB::raw("date(created_at)"), '<=', $toDate],
            ['status', '=', $data['status']], ['payment', ($paidUnpaid == 1? '<' : ($paidUnpaid == 2? '=' : '<=')), DB::raw('sub_total')]];
    
            $totalWhereArray = $status == 3? [[DB::raw("date(orders.created_at)"), '>=', $fromDate],
            [DB::raw("date(orders.created_at)"), '<=', $toDate], ['payment', ($paidUnpaid == 1? '<' : ($paidUnpaid == 2? '=' : '<=')), DB::raw('sub_total')]]: [[DB::raw("date(orders.created_at)"), '>=', $fromDate],
            [DB::raw("date(orders.created_at)"), '<=', $toDate], ['orders.status', '=', $data['status']], ['payment', ($paidUnpaid == 1? '<' : ($paidUnpaid == 2? '=' : '<=')), DB::raw('sub_total')]];
    
            $orders = Order::where($ordersWhereArray)
                        ->whereIn('type', [1,2,3])
                        ->latest()->paginate(50);
    
            $total = DB::table('orders')
                    ->where($totalWhereArray)
                    ->whereIn('type', [1,2,3])
                    ->select(DB::raw("SUM(orders.sub_total) AS totalOrders"), 
                            DB::raw("SUM(orders.discount) AS totalDiscount"),
                            DB::raw("SUM(orders.balance) AS totalBalance"))->first();
    
            if($total)
            $total = (array)$total;
    
            return view('reports.sale-orders', compact('orders', 'customers', 'fromDate', 'toDate', 'receiptNumber', 'selectedCustomers', 'status', 'typeTakeAway', 'typeHomeDelivery', 'typeDineIn', 'paidUnpaid', 'total'));    
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
            'selectedCustomers' => '',
            'receipt_number' => 'required_without_all:fromDate,toDate,selectedCustomers',
            'status' => '',
            'typeTakeAway' => '',
            'typeHomeDelivery' => '',
            'typeDineIn' => '',
            'paidUnpaid' => ''
        ]);
        $fromDate = "";
        $toDate = "";

        $receiptNumber = "";
        $selectedCustomers = [];

        $status = $data['status'];
        $typeTakeAway = array_key_exists('typeTakeAway', $data)? true: false;
        $typeHomeDelivery = array_key_exists('typeHomeDelivery', $data)? true: false;
        $typeDineIn = array_key_exists('typeDineIn', $data)? true: false;
        $paidUnpaid = $data['paidUnpaid'];

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
        {
            $receiptNumber = $data['receipt_number'];
        }

        if(array_key_exists("selectedCustomers", $data) && $data['selectedCustomers'])
        {
            $selectedCustomers = $data['selectedCustomers'];
        }

        $selectedTypes = [$typeTakeAway? 2: null];
        array_push($selectedTypes, $typeHomeDelivery? 1: null);
        array_push($selectedTypes, $typeDineIn? 3: null);
        $selectedTypes = array_filter($selectedTypes, function($type){
            if($type)
            {
                return $type;
            }
        });

        if(sizeof($selectedCustomers) > 0)
        {
            if($fromDate && $toDate && !$receiptNumber)
            {
                $ordersWhereArray = $status == 3? [[DB::raw("date(created_at)"), '>=', $fromDate],
                [DB::raw("date(created_at)"), '<=', $toDate], ['payment', ($paidUnpaid == 1? '<' : ($paidUnpaid == 2? '=' : '<=')), DB::raw('sub_total')]]: [[DB::raw("date(created_at)"), '>=', $fromDate],
                [DB::raw("date(created_at)"), '<=', $toDate],
                ['status', '=', $data['status']], ['payment', ($paidUnpaid == 1? '<' : ($paidUnpaid == 2? '=' : '<=')), DB::raw('sub_total')]];

                $totalWhereArray = $status == 3? [[DB::raw("date(orders.created_at)"), '>=', $fromDate],
                [DB::raw("date(orders.created_at)"), '<=', $toDate], ['payment', ($paidUnpaid == 1? '<' : ($paidUnpaid == 2? '=' : '<=')), DB::raw('sub_total')]]: [[DB::raw("date(orders.created_at)"), '>=', $fromDate],
                [DB::raw("date(orders.created_at)"), '<=', $toDate], ['orders.status', '=', $data['status']], ['payment', ($paidUnpaid == 1? '<' : ($paidUnpaid == 2? '=' : '<=')), DB::raw('sub_total')]];
                
                $orders = Order::where($ordersWhereArray)
                            ->whereIn('customer_id', $selectedCustomers)
                            ->whereIn('type', $selectedTypes)
                            ->latest()->paginate(50);
    
                $total = DB::table('orders')
                        ->where($totalWhereArray)
                        ->whereIn('customer_id', $selectedCustomers)
                        ->whereIn('type', $selectedTypes)
                        ->select(DB::raw("SUM(orders.sub_total) AS totalOrders"), 
                                DB::raw("SUM(orders.discount) AS totalDiscount"),
                                DB::raw("SUM(orders.balance) AS totalBalance"))->first();
    
            }
            elseif($receiptNumber && !$fromDate && !$toDate)
            {
                $ordersWhereArray = $status == 3? [['receipt_number', 'LIKE', "%$receiptNumber%"], ['payment', ($paidUnpaid == 1? '<' : ($paidUnpaid == 2? '=' : '<=')), DB::raw('sub_total')]]: [['receipt_number', 'LIKE', "%$receiptNumber%"],
                ['status', '=', $data['status']], ['payment', ($paidUnpaid == 1? '<' : ($paidUnpaid == 2? '=' : '<=')), DB::raw('sub_total')]];

                $orders = Order::where($ordersWhereArray)
                                ->whereIn('customer_id', $selectedCustomers)
                                ->whereIn('type', $selectedTypes)
                                ->latest()->paginate(50);
                $total = '';
            }
            else
            {
                $ordersWhereArray = $status == 3? [[DB::raw("date(created_at)"), '>=', $fromDate],
                [DB::raw("date(created_at)"), '<=', $toDate],
                ['receipt_number', 'LIKE', "%$receiptNumber%"], ['payment', ($paidUnpaid == 1? '<' : ($paidUnpaid == 2? '=' : '<=')), DB::raw('sub_total')]]: [[DB::raw("date(created_at)"), '>=', $fromDate],
                [DB::raw("date(created_at)"), '<=', $toDate],
                ['receipt_number', 'LIKE', "%$receiptNumber%"],
                ['status', '=', $data['status']], ['payment', ($paidUnpaid == 1? '<' : ($paidUnpaid == 2? '=' : '<=')), DB::raw('sub_total')]];

                $orders = Order::where($ordersWhereArray)
                            ->whereIn('customer_id', $selectedCustomers)
                            ->whereIn('type', $selectedTypes)
                            ->latest()->paginate(50);
    
                $total = '';
            }
        }
        else
        {
            if($fromDate && $toDate && !$receiptNumber)
            {
                $ordersWhereArray = $status == 3? [[DB::raw("date(created_at)"), '>=', $fromDate],
                [DB::raw("date(created_at)"), '<=', $toDate], ['payment', ($paidUnpaid == 1? '<' : ($paidUnpaid == 2? '=' : '<=')), DB::raw('sub_total')]]: [[DB::raw("date(created_at)"), '>=', $fromDate],
                [DB::raw("date(created_at)"), '<=', $toDate],
                ['status', '=', $data['status']], ['payment', ($paidUnpaid == 1? '<' : ($paidUnpaid == 2? '=' : '<=')), DB::raw('sub_total')]];

                $totalWhereArray = $status == 3? [[DB::raw("date(orders.created_at)"), '>=', $fromDate],
                [DB::raw("date(orders.created_at)"), '<=', $toDate], ['payment', ($paidUnpaid == 1? '<' : ($paidUnpaid == 2? '=' : '<=')), DB::raw('sub_total')]]: [[DB::raw("date(orders.created_at)"), '>=', $fromDate],
                [DB::raw("date(orders.created_at)"), '<=', $toDate], ['orders.status', '=', $data['status']], ['payment', ($paidUnpaid == 1? '<' : ($paidUnpaid == 2? '=' : '<=')), DB::raw('sub_total')]];
 
                $orders = Order::where($ordersWhereArray)
                            ->whereIn('type', $selectedTypes)
                            ->latest()->paginate(50);

                $total = DB::table('orders')
                        ->where($totalWhereArray)
                        ->whereIn('type', $selectedTypes)
                        ->select(DB::raw("SUM(orders.sub_total) AS totalOrders"), 
                                DB::raw("SUM(orders.discount) AS totalDiscount"),
                                DB::raw("SUM(orders.balance) AS totalBalance"))->first();
    
            }
            elseif($receiptNumber && !$fromDate && !$toDate)
            {
                $ordersWhereArray = $status == 3? [['receipt_number', 'LIKE', "%$receiptNumber%"], ['payment', ($paidUnpaid == 1? '<' : ($paidUnpaid == 2? '=' : '<=')), DB::raw('sub_total')]]: [['receipt_number', 'LIKE', "%$receiptNumber%"],
                ['status', '=', $data['status']], ['payment', ($paidUnpaid == 1? '<' : ($paidUnpaid == 2? '=' : '<=')), DB::raw('sub_total')]];

                $orders = Order::where($ordersWhereArray)
                                ->whereIn('type', $selectedTypes)
                                ->latest()->paginate(50);
                $total = '';
            }
            else
            {
                $ordersWhereArray = $status == 3? [[DB::raw("date(created_at)"), '>=', $fromDate],
                [DB::raw("date(created_at)"), '<=', $toDate],
                ['receipt_number', 'LIKE', "%$receiptNumber%"], ['payment', ($paidUnpaid == 1? '<' : ($paidUnpaid == 2? '=' : '<=')), DB::raw('sub_total')]]: [[DB::raw("date(created_at)"), '>=', $fromDate],
                [DB::raw("date(created_at)"), '<=', $toDate],
                ['receipt_number', 'LIKE', "%$receiptNumber%"],
                ['status', '=', $data['status']], ['payment', ($paidUnpaid == 1? '<' : ($paidUnpaid == 2? '=' : '<=')), DB::raw('sub_total')]];

                $orders = Order::where($ordersWhereArray)
                            ->whereIn('type', $selectedTypes)
                            ->latest()->paginate(50);
    
                $total = '';
            }
        }

        if($total)
            $total = (array)$total;

        $customers = Customer::all();
        return view('orders.index', compact('orders', 'customers', 'fromDate', 'toDate', 'receiptNumber', 'selectedCustomers', 'status', 'typeTakeAway', 'typeHomeDelivery', 'typeDineIn', 'paidUnpaid', 'total'));
    }

    public function filterOrdersReport()
    {
        $data = request()->validate([
            'fromDate' => 'required_without_all:receipt_number',
            'toDate' => 'required_without_all:receipt_number',
            'selectedCustomers' => '',
            'receipt_number' => 'required_without_all:fromDate,toDate,selectedCustomers',
            'status' => '',
            'typeTakeAway' => '',
            'typeHomeDelivery' => '',
            'typeDineIn' => '',
            'paidUnpaid' => ''
        ]);

        $fromDate = "";
        $toDate = "";

        $receiptNumber = "";
        $selectedCustomers = [];

        $status = $data['status'];
        $typeTakeAway = array_key_exists('typeTakeAway', $data)? true: false;
        $typeHomeDelivery = array_key_exists('typeHomeDelivery', $data)? true: false;
        $typeDineIn = array_key_exists('typeDineIn', $data)? true: false;
        $paidUnpaid = $data['paidUnpaid'];

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
        {
            $receiptNumber = $data['receipt_number'];
        }

        if(array_key_exists("selectedCustomers", $data) && $data['selectedCustomers'])
        {
            $selectedCustomers = $data['selectedCustomers'];
        }

        //Old code below, can be deleted after successfull testing
        /*if(sizeof($selectedCustomers) > 0)
        {
            if($fromDate && $toDate && !$receiptNumber)
            {
                $ordersWhereArray = $status == 3? [[DB::raw("date(created_at)"), '>=', $fromDate],
                [DB::raw("date(created_at)"), '<=', $toDate]]: [[DB::raw("date(created_at)"), '>=', $fromDate],
                [DB::raw("date(created_at)"), '<=', $toDate],
                ['status', '=', $data['status']]];

                $totalWhereArray = $status == 3? [[DB::raw("date(orders.created_at)"), '>=', $fromDate],
                [DB::raw("date(orders.created_at)"), '<=', $toDate]]: [[DB::raw("date(orders.created_at)"), '>=', $fromDate],
                [DB::raw("date(orders.created_at)"), '<=', $toDate], ['orders.status', '=', $data['status']]];
                
                $orders = Order::where($ordersWhereArray)
                            ->whereIn('customer_id', $selectedCustomers)
                            ->latest()->paginate(50);
    
                $total = DB::table('orders')
                        ->where($totalWhereArray)
                        ->whereIn('customer_id', $selectedCustomers)
                        ->select(DB::raw("SUM(orders.sub_total) AS totalOrders"), 
                                DB::raw("SUM(orders.discount) AS totalDiscount"),
                                DB::raw("SUM(orders.balance) AS totalBalance"))->first();
    
            }
            elseif($receiptNumber && !$fromDate && !$toDate)
            {
                $ordersWhereArray = $status == 3? [['receipt_number', 'LIKE', "%$receiptNumber%"]]: [['receipt_number', 'LIKE', "%$receiptNumber%"],
                ['status', '=', $data['status']]];

                $orders = Order::where($ordersWhereArray)
                                ->whereIn('customer_id', $selectedCustomers)
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

                $orders = Order::where($ordersWhereArray)
                            ->whereIn('customer_id', $selectedCustomers)
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

                $totalWhereArray = $status == 3? [[DB::raw("date(orders.created_at)"), '>=', $fromDate],
                [DB::raw("date(orders.created_at)"), '<=', $toDate]]: [[DB::raw("date(orders.created_at)"), '>=', $fromDate],
                [DB::raw("date(orders.created_at)"), '<=', $toDate], ['orders.status', '=', $data['status']]];
 
                $orders = Order::where($ordersWhereArray)
                            ->latest()->paginate(50);

                $total = DB::table('orders')
                        ->where($totalWhereArray)
                        ->select(DB::raw("SUM(orders.sub_total) AS totalOrders"), 
                                DB::raw("SUM(orders.discount) AS totalDiscount"),
                                DB::raw("SUM(orders.balance) AS totalBalance"))->first();
    
            }
            elseif($receiptNumber && !$fromDate && !$toDate)
            {
                $ordersWhereArray = $status == 3? [['receipt_number', 'LIKE', "%$receiptNumber%"]]: [['receipt_number', 'LIKE', "%$receiptNumber%"],
                ['status', '=', $data['status']]];

                $orders = Order::where($ordersWhereArray)
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

                $orders = Order::where($ordersWhereArray)
                            ->latest()->paginate(50);
    
                $total = '';
            }
        }*/

        /*
        if(sizeof($selectedCustomers) > 0)
        {
            if($fromDate && $toDate && !$receiptNumber)
            {
                $ordersWhereArray = $status == 3? [[DB::raw("date(created_at)"), '>=', $fromDate],
                [DB::raw("date(created_at)"), '<=', $toDate], ['payment', ($paidUnpaid == 1? '<' : ($paidUnpaid == 2? '=' : '<=')), DB::raw('sub_total')]]: [[DB::raw("date(created_at)"), '>=', $fromDate],
                [DB::raw("date(created_at)"), '<=', $toDate],
                ['status', '=', $data['status']], ['payment', ($paidUnpaid == 1? '<' : ($paidUnpaid == 2? '=' : '<=')), DB::raw('sub_total')]];

                $totalWhereArray = $status == 3? [[DB::raw("date(orders.created_at)"), '>=', $fromDate],
                [DB::raw("date(orders.created_at)"), '<=', $toDate], ['payment', ($paidUnpaid == 1? '<' : ($paidUnpaid == 2? '=' : '<=')), DB::raw('sub_total')]]: [[DB::raw("date(orders.created_at)"), '>=', $fromDate],
                [DB::raw("date(orders.created_at)"), '<=', $toDate], ['orders.status', '=', $data['status']], ['payment', ($paidUnpaid == 1? '<' : ($paidUnpaid == 2? '=' : '<=')), DB::raw('sub_total')]];
                
                $orders = Order::where($ordersWhereArray)
                            ->whereIn('customer_id', $selectedCustomers)
                            ->latest()->paginate(50);
    
                $total = DB::table('orders')
                        ->where($totalWhereArray)
                        ->whereIn('customer_id', $selectedCustomers)
                        ->select(DB::raw("SUM(orders.sub_total) AS totalOrders"), 
                                DB::raw("SUM(orders.discount) AS totalDiscount"),
                                DB::raw("SUM(orders.balance) AS totalBalance"))->first();
    
            }
            elseif($receiptNumber && !$fromDate && !$toDate)
            {
                $ordersWhereArray = $status == 3? [['receipt_number', 'LIKE', "%$receiptNumber%"], ['payment', ($paidUnpaid == 1? '<' : ($paidUnpaid == 2? '=' : '<=')), DB::raw('sub_total')]]: [['receipt_number', 'LIKE', "%$receiptNumber%"],
                ['status', '=', $data['status']], ['payment', ($paidUnpaid == 1? '<' : ($paidUnpaid == 2? '=' : '<=')), DB::raw('sub_total')]];

                $orders = Order::where($ordersWhereArray)
                                ->whereIn('customer_id', $selectedCustomers)
                                ->latest()->paginate(50);
                $total = '';
            }
            else
            {
                $ordersWhereArray = $status == 3? [[DB::raw("date(created_at)"), '>=', $fromDate],
                [DB::raw("date(created_at)"), '<=', $toDate],
                ['receipt_number', 'LIKE', "%$receiptNumber%"], ['payment', ($paidUnpaid == 1? '<' : ($paidUnpaid == 2? '=' : '<=')), DB::raw('sub_total')]]: [[DB::raw("date(created_at)"), '>=', $fromDate],
                [DB::raw("date(created_at)"), '<=', $toDate],
                ['receipt_number', 'LIKE', "%$receiptNumber%"],
                ['status', '=', $data['status']], ['payment', ($paidUnpaid == 1? '<' : ($paidUnpaid == 2? '=' : '<=')), DB::raw('sub_total')]];

                $orders = Order::where($ordersWhereArray)
                            ->whereIn('customer_id', $selectedCustomers)
                            ->latest()->paginate(50);
    
                $total = '';
            }
        }
        else
        {
            if($fromDate && $toDate && !$receiptNumber)
            {
                $ordersWhereArray = $status == 3? [[DB::raw("date(created_at)"), '>=', $fromDate],
                [DB::raw("date(created_at)"), '<=', $toDate], ['payment', ($paidUnpaid == 1? '<' : ($paidUnpaid == 2? '=' : '<=')), DB::raw('sub_total')]]: [[DB::raw("date(created_at)"), '>=', $fromDate],
                [DB::raw("date(created_at)"), '<=', $toDate],
                ['status', '=', $data['status']], ['payment', ($paidUnpaid == 1? '<' : ($paidUnpaid == 2? '=' : '<=')), DB::raw('sub_total')]];

                $totalWhereArray = $status == 3? [[DB::raw("date(orders.created_at)"), '>=', $fromDate],
                [DB::raw("date(orders.created_at)"), '<=', $toDate], ['payment', ($paidUnpaid == 1? '<' : ($paidUnpaid == 2? '=' : '<=')), DB::raw('sub_total')]]: [[DB::raw("date(orders.created_at)"), '>=', $fromDate],
                [DB::raw("date(orders.created_at)"), '<=', $toDate], ['orders.status', '=', $data['status']], ['payment', ($paidUnpaid == 1? '<' : ($paidUnpaid == 2? '=' : '<=')), DB::raw('sub_total')]];
 
                $orders = Order::where($ordersWhereArray)
                            ->latest()->paginate(50);

                $total = DB::table('orders')
                        ->where($totalWhereArray)
                        ->select(DB::raw("SUM(orders.sub_total) AS totalOrders"), 
                                DB::raw("SUM(orders.discount) AS totalDiscount"),
                                DB::raw("SUM(orders.balance) AS totalBalance"))->first();
    
            }
            elseif($receiptNumber && !$fromDate && !$toDate)
            {
                $ordersWhereArray = $status == 3? [['receipt_number', 'LIKE', "%$receiptNumber%"], ['payment', ($paidUnpaid == 1? '<' : ($paidUnpaid == 2? '=' : '<=')), DB::raw('sub_total')]]: [['receipt_number', 'LIKE', "%$receiptNumber%"],
                ['status', '=', $data['status']], ['payment', ($paidUnpaid == 1? '<' : ($paidUnpaid == 2? '=' : '<=')), DB::raw('sub_total')]];

                $orders = Order::where($ordersWhereArray)
                                ->latest()->paginate(50);
                $total = '';
            }
            else
            {
                $ordersWhereArray = $status == 3? [[DB::raw("date(created_at)"), '>=', $fromDate],
                [DB::raw("date(created_at)"), '<=', $toDate],
                ['receipt_number', 'LIKE', "%$receiptNumber%"], ['payment', ($paidUnpaid == 1? '<' : ($paidUnpaid == 2? '=' : '<=')), DB::raw('sub_total')]]: [[DB::raw("date(created_at)"), '>=', $fromDate],
                [DB::raw("date(created_at)"), '<=', $toDate],
                ['receipt_number', 'LIKE', "%$receiptNumber%"],
                ['status', '=', $data['status']], ['payment', ($paidUnpaid == 1? '<' : ($paidUnpaid == 2? '=' : '<=')), DB::raw('sub_total')]];

                $orders = Order::where($ordersWhereArray)
                            ->latest()->paginate(50);
    
                $total = '';
            }
        }
        */
        //Old code above, can be deleted after successfull testing

        $selectedTypes = [$typeTakeAway? 2: null];
        array_push($selectedTypes, $typeHomeDelivery? 1: null);
        array_push($selectedTypes, $typeDineIn? 3: null);
        $selectedTypes = array_filter($selectedTypes, function($type){
            if($type)
            {
                return $type;
            }
        });

        if(sizeof($selectedCustomers) > 0)
        {
            if($fromDate && $toDate && !$receiptNumber)
            {
                $ordersWhereArray = $status == 3? [[DB::raw("date(created_at)"), '>=', $fromDate],
                [DB::raw("date(created_at)"), '<=', $toDate], ['payment', ($paidUnpaid == 1? '<' : ($paidUnpaid == 2? '=' : '<=')), DB::raw('sub_total')]]: [[DB::raw("date(created_at)"), '>=', $fromDate],
                [DB::raw("date(created_at)"), '<=', $toDate],
                ['status', '=', $data['status']], ['payment', ($paidUnpaid == 1? '<' : ($paidUnpaid == 2? '=' : '<=')), DB::raw('sub_total')]];

                $totalWhereArray = $status == 3? [[DB::raw("date(orders.created_at)"), '>=', $fromDate],
                [DB::raw("date(orders.created_at)"), '<=', $toDate], ['payment', ($paidUnpaid == 1? '<' : ($paidUnpaid == 2? '=' : '<=')), DB::raw('sub_total')]]: [[DB::raw("date(orders.created_at)"), '>=', $fromDate],
                [DB::raw("date(orders.created_at)"), '<=', $toDate], ['orders.status', '=', $data['status']], ['payment', ($paidUnpaid == 1? '<' : ($paidUnpaid == 2? '=' : '<=')), DB::raw('sub_total')]];
                
                $orders = Order::where($ordersWhereArray)
                            ->whereIn('customer_id', $selectedCustomers)
                            ->whereIn('type', $selectedTypes)
                            ->latest()->paginate(50);
    
                $total = DB::table('orders')
                        ->where($totalWhereArray)
                        ->whereIn('customer_id', $selectedCustomers)
                        ->whereIn('type', $selectedTypes)
                        ->select(DB::raw("SUM(orders.sub_total) AS totalOrders"), 
                                DB::raw("SUM(orders.discount) AS totalDiscount"),
                                DB::raw("SUM(orders.balance) AS totalBalance"))->first();
    
            }
            elseif($receiptNumber && !$fromDate && !$toDate)
            {
                $ordersWhereArray = $status == 3? [['receipt_number', 'LIKE', "%$receiptNumber%"], ['payment', ($paidUnpaid == 1? '<' : ($paidUnpaid == 2? '=' : '<=')), DB::raw('sub_total')]]: [['receipt_number', 'LIKE', "%$receiptNumber%"],
                ['status', '=', $data['status']], ['payment', ($paidUnpaid == 1? '<' : ($paidUnpaid == 2? '=' : '<=')), DB::raw('sub_total')]];

                $orders = Order::where($ordersWhereArray)
                                ->whereIn('customer_id', $selectedCustomers)
                                ->whereIn('type', $selectedTypes)
                                ->latest()->paginate(50);
                $total = '';
            }
            else
            {
                $ordersWhereArray = $status == 3? [[DB::raw("date(created_at)"), '>=', $fromDate],
                [DB::raw("date(created_at)"), '<=', $toDate],
                ['receipt_number', 'LIKE', "%$receiptNumber%"], ['payment', ($paidUnpaid == 1? '<' : ($paidUnpaid == 2? '=' : '<=')), DB::raw('sub_total')]]: [[DB::raw("date(created_at)"), '>=', $fromDate],
                [DB::raw("date(created_at)"), '<=', $toDate],
                ['receipt_number', 'LIKE', "%$receiptNumber%"],
                ['status', '=', $data['status']], ['payment', ($paidUnpaid == 1? '<' : ($paidUnpaid == 2? '=' : '<=')), DB::raw('sub_total')]];

                $orders = Order::where($ordersWhereArray)
                            ->whereIn('customer_id', $selectedCustomers)
                            ->whereIn('type', $selectedTypes)
                            ->latest()->paginate(50);
    
                $total = '';
            }
        }
        else
        {
            if($fromDate && $toDate && !$receiptNumber)
            {
                $ordersWhereArray = $status == 3? [[DB::raw("date(created_at)"), '>=', $fromDate],
                [DB::raw("date(created_at)"), '<=', $toDate], ['payment', ($paidUnpaid == 1? '<' : ($paidUnpaid == 2? '=' : '<=')), DB::raw('sub_total')]]: [[DB::raw("date(created_at)"), '>=', $fromDate],
                [DB::raw("date(created_at)"), '<=', $toDate],
                ['status', '=', $data['status']], ['payment', ($paidUnpaid == 1? '<' : ($paidUnpaid == 2? '=' : '<=')), DB::raw('sub_total')]];

                $totalWhereArray = $status == 3? [[DB::raw("date(orders.created_at)"), '>=', $fromDate],
                [DB::raw("date(orders.created_at)"), '<=', $toDate], ['payment', ($paidUnpaid == 1? '<' : ($paidUnpaid == 2? '=' : '<=')), DB::raw('sub_total')]]: [[DB::raw("date(orders.created_at)"), '>=', $fromDate],
                [DB::raw("date(orders.created_at)"), '<=', $toDate], ['orders.status', '=', $data['status']], ['payment', ($paidUnpaid == 1? '<' : ($paidUnpaid == 2? '=' : '<=')), DB::raw('sub_total')]];
 
                $orders = Order::where($ordersWhereArray)
                            ->whereIn('type', $selectedTypes)
                            ->latest()->paginate(50);

                $total = DB::table('orders')
                        ->where($totalWhereArray)
                        ->whereIn('type', $selectedTypes)
                        ->select(DB::raw("SUM(orders.sub_total) AS totalOrders"), 
                                DB::raw("SUM(orders.discount) AS totalDiscount"),
                                DB::raw("SUM(orders.balance) AS totalBalance"))->first();
    
            }
            elseif($receiptNumber && !$fromDate && !$toDate)
            {
                $ordersWhereArray = $status == 3? [['receipt_number', 'LIKE', "%$receiptNumber%"], ['payment', ($paidUnpaid == 1? '<' : ($paidUnpaid == 2? '=' : '<=')), DB::raw('sub_total')]]: [['receipt_number', 'LIKE', "%$receiptNumber%"],
                ['status', '=', $data['status']], ['payment', ($paidUnpaid == 1? '<' : ($paidUnpaid == 2? '=' : '<=')), DB::raw('sub_total')]];

                $orders = Order::where($ordersWhereArray)
                                ->whereIn('type', $selectedTypes)
                                ->latest()->paginate(50);
                $total = '';
            }
            else
            {
                $ordersWhereArray = $status == 3? [[DB::raw("date(created_at)"), '>=', $fromDate],
                [DB::raw("date(created_at)"), '<=', $toDate],
                ['receipt_number', 'LIKE', "%$receiptNumber%"], ['payment', ($paidUnpaid == 1? '<' : ($paidUnpaid == 2? '=' : '<=')), DB::raw('sub_total')]]: [[DB::raw("date(created_at)"), '>=', $fromDate],
                [DB::raw("date(created_at)"), '<=', $toDate],
                ['receipt_number', 'LIKE', "%$receiptNumber%"],
                ['status', '=', $data['status']], ['payment', ($paidUnpaid == 1? '<' : ($paidUnpaid == 2? '=' : '<=')), DB::raw('sub_total')]];

                $orders = Order::where($ordersWhereArray)
                            ->whereIn('type', $selectedTypes)
                            ->latest()->paginate(50);
    
                $total = '';
            }
        }

        if($total)
            $total = (array)$total;

        $customers = Customer::all();
                                        
        return view('reports.sale-orders', compact('orders', 'customers', 'fromDate', 'toDate', 'receiptNumber', 'selectedCustomers', 'status', 'typeTakeAway', 'typeHomeDelivery', 'typeDineIn', 'paidUnpaid', 'total'));
    }

    public function show(Order $order, $print = null)
    {
        if(allowed(9, 'view'))
        {
            $sales = $order->sales()->latest()->get();

            $total = '';
            $individualTotal = '';
    
            $fromDate = '';
            $toDate = '';
            $selectedItems = [];
            $return = '';
    
            return view('orders.show', compact('print', 'order', 'sales', 'fromDate', 'toDate', 'total', 'individualTotal', 'selectedItems', 'return'));    
        }
        else
        {
            return redirect('/sale/create');
        }
    }

    public function printBill(Order $order)
    {
        $sales = $order->sales;

        $connector = new WindowsPrintConnector("MyThermalPrinter");
        $printer = new Printer($connector);

        $subtotal = new PrintCustomItem('Subtotal', '', '', $order['total']);
        $total = new PrintCustomItem('Total', '', '', $order['sub_total']);
        $paidAmount = new PrintCustomItem('Paid amount', '', '', $order['cash_amount']);
        $changeAmount = new PrintCustomItem('Change', '', '', $order['change_amount']);
        
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
        $printer -> text(date('Y-m-d') . ' ' . date('h:i A') . '            Invoice No: ' . $order['receipt_number']. "\n");
        if($order['status'] == 1)
            $printer -> text('Collect at: ' . date("Y-m-d", strtotime($order['receiving_date'])) . '                 ' . date("h:i A", strtotime($order['receiving_date'])));

        if($order['customer_id'] != 1)
            $printer -> text(new PrintCustomItem($order->customer->name, '', '', '') . "\n");
            
        $printer -> feed();

        /* Items */
        $printer -> setEmphasis(true);
        $printer -> text("________________________________________________");
        $printer -> text(new PrintCustomItem('Item', 'Qty', 'Price', 'Total'));
        $printer -> text("________________________________________________");
        $printer -> setEmphasis(false);

        for($i = 0; $i < sizeof($order->sales); $i++)
        {
            if(strlen($order->sales[$i]->item['name']) > 15)
            {
                // $line = sprintf('%-15.40s %-13.40s %3.40s %13.40s', substr($order->sales[$i]['name'], 0, 15) , '', '', ''); 
                $printer -> text(substr($order->sales[$i]->item['name'], 0, 15) . "\n");

                $line = sprintf('%-15.40s %-13.40s %3.40s %10.40s', substr($order->sales[$i]->item['name'], 15, strlen($order->sales[$i]->item['name'])) , ($order->sales[$i]['quantity'] + 0), ($order->sales[$i]['unit_price'] + 0), ($order->sales[$i]['price'] + 0)); 
                $printer -> text("$line\n");
            }
            else
            {
                $line = sprintf('%-15.40s %-13.40s %3.40s %10.40s', $order->sales[$i]->item['name'] , ($order->sales[$i]['quantity'] + 0), ($order->sales[$i]['unit_price'] + 0), ($order->sales[$i]['price'] + 0));
                $printer -> text("$line\n");
            }

            if($order->sales[$i]['discount_amount'] > 0)
            {
                $printer -> text(new PrintCustomItem('Discount', '', '', ($order->sales[$i]['discount_amount'] + 0)));

                $savedAmount = $savedAmount + $order->sales[$i]['discount_amount'];
            }

            $printer -> text("________________________________________________");
        }

        $printer -> feed();

        $printer -> setEmphasis(true);

        $printer -> text("\n");

        $printer -> text(new PrintCustomItem('Total Items', sizeof($order->sales), '', ''));

        $printer -> text("________________________________________________");

        $printer -> text($subtotal);

        if($order['discount_amount'] > 0)
        {
            $printer -> text(new PrintCustomItem('Discount', '', '', $order['discount_amount']));
            $savedAmount = $savedAmount + $order['discount_amount'];
        }

        $printer -> text($total);

        $balance = 0;
        if($order['payment'] < $order['sub_total'])
        {
            $balance = $order['sub_total'] - $order['payment'];

            $printer -> text("\n" . new PrintCustomItem('Payment', '', '', $order['payment']));
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
        $printer -> text("User: " . $order->user->name . "\n");

        $printer -> setJustification(Printer::JUSTIFY_CENTER);
        if(posConfigurations()->footer_text)
            $printer -> text(posConfigurations()->footer_text . " " . (posConfigurations()->footer_number ?? '') . "\n");
        // if(posConfigurations()->footer_number)
        //     $printer -> text(posConfigurations()->footer_number . "\n");

        $printer->cut();

        $printer->close();

        {
            // $connector = new WindowsPrintConnector("MyThermalPrinter");
            // $printer = new Printer($connector);
            
            // $subtotal = new PrintCustomItem('Subtotal', '', '', $order['total'] + 0);
            // $total = new PrintCustomItem('Total', '', '', $order['sub_total'] + 0);
            
            // $printer -> setJustification(Printer::JUSTIFY_CENTER);
    
            // /* Name of shop */
            // $printer -> selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
            // $printer -> text(posConfigurations()->title . "\n");
            // $printer -> selectPrintMode();
            // if(posConfigurations()->subtitle && strlen(posConfigurations()->subtitle) > 0)
            //     $printer -> text(posConfigurations()->subtitle . "\n");
            
            // $printer -> text(posConfigurations()->address . "\n");
            // $printer -> text(posConfigurations()->contact . "\n");
            // $printer -> feed();
            // $printer -> setJustification(Printer::JUSTIFY_LEFT);
            // $printer -> text(date('Y-m-d', strtotime($order['created_at'])) . ' ' . date('h:i A', strtotime($order['created_at'])) . '            ' . $order['receipt_number']. "\n");
            // if($order['status'] == 1)
            //     $printer -> text('Collect at: ' . date('Y-m-d', strtotime($order['receiving_date'])) . '                 ' . date('h:i A', strtotime($order['receiving_date'])). "\n");
    
            // if($order['customer_id'] != 1)
            //     $printer -> text(new PrintCustomItem($order->customer->name, '', '', '') . "\n");
                
            // $printer -> feed();
    
            // /* Items */
            // $printer -> setEmphasis(true);
            // $printer -> text("________________________________________________");
            // $printer -> text(new PrintCustomItem('Item', 'Qty', 'Price', 'Total'));
            // $printer -> text("________________________________________________");
            // $printer -> setEmphasis(false);
    
            // for($i = 0; $i < sizeof($sales); $i++)
            // {
            //     if(strlen($sales[$i]->item->name) > 15)
            //     {
            //         // $line = sprintf('%-15.40s %-13.40s %3.40s %13.40s', substr($sales[$i]['name'], 0, 15) , '', '', ''); 
            //         $printer -> text(substr($sales[$i]->item->name, 0, 15) . "\n");
    
            //         $line = sprintf('%-15.40s %-13.40s %3.40s %10.40s', substr($sales[$i]->item->name, 15, strlen($sales[$i]->item->name)) , $sales[$i]['quantity'] + 0, $sales[$i]->item->rate->sale_price + 0, $sales[$i]->price + 0); 
            //         $printer -> text("$line\n");
            //     }
            //     else
            //     {
            //         $line = sprintf('%-15.40s %-13.40s %3.40s %10.40s', $sales[$i]->item->name , $sales[$i]['quantity'] + 0, $sales[$i]->item->rate->sale_price + 0, $sales[$i]->price + 0); 
            //         $printer -> text("$line\n");
            //     }
    
            //     if($sales[$i]->discount > 0)
            //     {
            //         $printer -> text(new PrintCustomItem('Discount', '', '', $sales[$i]->discount + 0));
            //     }
    
            //     $printer -> text("________________________________________________");
            // }
    
            // $printer -> feed();
    
            // $printer -> setEmphasis(true);
            // $printer -> text($subtotal);
            // // $printer -> setEmphasis(false);
    
            // if($order['discount'] > 0)
            //     $printer -> text(new PrintCustomItem('Discount', '', '', $order['discount'] + 0));
    
            // // $printer -> selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
            // $printer -> text($total);
    
            // $balance = 0;
            // if($order['payment'] < $order['sub_total'])
            // {
            //     $balance = $order['sub_total'] - $order['payment'];
    
            //     $printer -> text("\n" . new PrintCustomItem('Payment', '', '', $order['payment'] + 0));
            //     $printer -> text(new PrintCustomItem('Balance', '', '', $balance));
            // }
    
            // $printer -> setEmphasis(false);
    
            // $printer -> selectPrintMode();
    
            // /* Footer */
            // $printer -> feed(1);
            // $printer -> setJustification(Printer::JUSTIFY_CENTER);
            // if(posConfigurations()->thank_note)
            //     $printer -> text(posConfigurations()->thank_note . "\n");
    
            // $printer->feed();
    
            // $printer -> selectPrintMode(Printer::MODE_FONT_B);
    
            // $printer -> setJustification(Printer::JUSTIFY_LEFT);
            // $printer -> text("User: " . $order->user->name . "\n");
    
            // $printer -> setJustification(Printer::JUSTIFY_CENTER);
            // if(posConfigurations()->footer_text)
            //     $printer -> text(posConfigurations()->footer_text . " " . (posConfigurations()->footer_number ?? '') . "\n");
            // // if(posConfigurations()->footer_number)
            // //     $printer -> text(posConfigurations()->footer_number . "\n");
    
            // $printer->cut();
            // $printer->close();
        }

        return 1;
    }

    public function filterSales(Order $order)
    {
        $data = request()->validate([
            'selectedItems' => 'required_without_all:return',
            'return' => 'required_without_all:selectedItems',
        ]);

        $sales = '';
        $total = '';
        $individualTotal = '';
        $selectedItems = [];
        $return = '';

        if(array_key_exists('return', $data))
        {
            if(array_key_exists('selectedItems', $data))
            {
                $total = $order->sales()->where([
                                    ["status", '=', $data['return']]])
                            ->whereIn('item_id', $data['selectedItems'])
                            ->select(DB::raw("SUM(price) AS totalSale"))->first();

                $sales = $order->sales()->where([
                                ["status", '=', $data['return']]])
                                ->whereIn('item_id', $data['selectedItems'])
                                ->latest()->paginate(50);

                $individualTotal = $order->sales()->where([
                                                ["status", '=', $data['return']]])
                                    ->whereIn('item_id', $data['selectedItems'])
                                    ->select(DB::raw("SUM(price) AS totalSale"),
                                            'item_id')
                                    ->groupBy('item_id')->get();

                $selectedItems = $data['selectedItems'];
            }
            else
            {
                $total = $order->sales()->where([
                                    ["status", '=', $data['return']]])
                            ->select(DB::raw("SUM(price) AS totalSale"), 
                                    DB::raw("SUM(profit) AS totalProfit"))->first();

                $sales = $order->sales()->where([
                                ["status", '=', $data['return']]])
                                ->latest()->paginate(50);
            }

            $return = $data['return'];
        }
        else
        {
            $total = $order->sales()
                        ->whereIn('item_id', $data['selectedItems'])
                            ->select(DB::raw("SUM(price) AS totalSale"), 
                                    DB::raw("SUM(profit) AS totalProfit"))
                            ->first();

            $sales = $order->sales()
                                ->whereIn('item_id', $data['selectedItems'])
                                ->latest()->paginate(50);

            $individualTotal = $order->sales()
                            ->whereIn('item_id', $data['selectedItems'])
                            ->select('item_id', DB::raw("SUM(price) AS totalSale"), 
                                    DB::raw("SUM(profit) AS totalProfit"))
                            ->groupBy('item_id')->get();

            $selectedItems = $data['selectedItems'];
        }

        $items = Item::all();
                                        
        return view('orders.show', compact('order', 'sales', 'items', 'selectedItems', 'return', 'fromDate', 'toDate', 'total', 'individualTotal'));
    }

    public function return(Order $order)
    {
        $data = request()->validate([
            'deduct' => ''
        ]);

        $sales = $order->sales;

        foreach ($sales as $key => $sale) 
        {
            if($data['deduct'] == 0)
            {
                $inventory = $sale->item->inventory;

                $inventory->update(['quantity' => $inventory->quantity + $sale->quantity]);
            }
            else
            {
                RawWaste::create(['item_id' => $sale->item_id, 'quantity' => $sale->quantity]);
            }

            $sale->update(['status' => 2]); //status: 1 - active/normal, 2 - return, 0 - other than both
        }

        $order->update(['status' => 2]);

        return 'updated';
    }

    public function updateStatus(Order $order)
    {
        for($i = 0; $i < sizeof($order->sales); $i++)
        {
            if(posConfigurations()->maintain_inventory == 1)
            {
                $q = $order->sales[$i]['quantity'];
                DB::update("update raw_inventories set quantity = quantity-{$q}, cost = cost - " . ($order->sales[$i]['average_unit_cost'] * $q) . " where item_id = ?", [$order->sales[$i]['item_id']]);
            }
        }

        $order->update(['status' => 2]);

        return 1;
    }

    public function updateBalance(Order $order)
    {
        $data = request()->validate([
            'payment' => 'required',
        ]);

        $order->update(['payment' => $order->payment + $data['payment'], 
                            'balance' => ($order->balance - $data['payment'])]);

        return 1;
    }

    public function destroy(Order $order)
    {
        if(allowed(9, 'remove'))
        {
            $sales = $order->sales;

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
    
            $order->delete();
    
            return 1;
        }
        else
        {
            return 0;
        }
    }
}
