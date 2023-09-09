<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use App\Charts\UserChart;

use App\User;
use App\Role;
use App\Permission;
use App\PermissionRole;
use App\Order;
use App\Sale;
use App\Purchase;
use App\Configuration;

class HomeController extends Controller
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
        try {
            $json = @file_get_contents('https://drive.google.com/uc?id=123');
            if($json !== FALSE)
            {
                $obj = json_decode($json, true);

                if(array_key_exists(shopName(), $obj) && 
                array_key_exists("status", $obj[shopName()]))
                {
                    if($obj[shopName()]["status"] == "off")
                    {
                        Configuration::find(1)->update(['status' => 0]);
                        return view('errors.expired-error', ['message' => $obj[shopName()]["message"]]);
                    }
                    elseif($obj[shopName()]["status"] == "on" && Configuration::find(1)->status == 0)
                    {
                        Configuration::find(1)->update(['status' => 1]);
                    }
                }
            }
        }
        catch(Exception $e) {

        }
        
        if(allowed(19, 'view'))
        {
            if(Auth::check())
            {
                $yesterday = date("Y-m-d", strtotime("-1 days"));
                $beforeSixDays = date("Y-m-d", strtotime("-20 week"));

                $orders = Order::where([[DB::raw("date(created_at)"), '>=', $beforeSixDays],
                                        [DB::raw("date(created_at)"), '<=', $yesterday]])
                                ->select(DB::raw("DAYOFWEEK(created_at) AS dayofweek"), DB::raw("DAYNAME(created_at) AS day"), DB::raw("DATE_FORMAT(created_at, '%H:%i') AS time"))
                                ->orderBy("dayofweek", "ASC")
                                ->get();

                $occurrences = [];
                foreach ($orders->toArray() as $key => $order) 
                {
                    if($order["day"] == "Monday")
                    {
                        $occurrences["Monday"][] = $order["time"];
                    }
                    elseif($order["day"] == "Tuesday")
                    {
                        $occurrences["Tuesday"][] = $order["time"];
                    }
                    elseif($order["day"] == "Wednesday")
                    {
                        $occurrences["Wednesday"][] = $order["time"];
                    }
                    elseif($order["day"] == "Thursday")
                    {
                        $occurrences["Thursday"][] = $order["time"];
                    }
                    elseif($order["day"] == "Friday")
                    {
                        $occurrences["Friday"][] = $order["time"];
                    }
                    elseif($order["day"] == "Saturday")
                    {
                        $occurrences["Saturday"][] = $order["time"];
                    }
                    elseif($order["day"] == "Sunday")
                    {
                        $occurrences["Sunday"][] = $order["time"];
                    }
                }

                foreach ($occurrences as $key => $occurrence) 
                {
                    $occurrences[$key] = array_count_values($occurrence);
                    arsort($occurrences[$key]);

                    $occurrences[$key] = array_key_first($occurrences[$key]);

                    $occurrences[$key] = date("h:i A", strtotime($occurrences[$key]));
                }

                $topSellingItem = collect(DB::select("SELECT COUNT(sales.item_id) AS itemCount, SUM(sales.price) AS totalPrice, CONCAT(SUM(sales.quantity), IF(items.unit_id=1, ' kg', IF(items.unit_id=2, ' dozen', ''))) AS totalQuantity, items.name AS item 
                                            FROM items
                                            LEFT JOIN sales ON items.id = sales.item_id
                                            WHERE MONTH(sales.created_at) = MONTH(CURDATE())
                                            AND YEAR(sales.created_at) = YEAR(CURDATE())
                                            GROUP BY items.id ORDER BY itemCount DESC LIMIT 1"))->first();

                $worstSellingItem = collect(DB::select("SELECT COUNT(sales.item_id) AS itemCount, SUM(sales.price) AS totalPrice, CONCAT(SUM(sales.quantity), IF(items.unit_id=1, ' kg', IF(items.unit_id=2, ' dozen', ''))) AS totalQuantity, items.name AS item 
                                            FROM items 
                                            LEFT JOIN sales ON items.id = sales.item_id
                                            WHERE MONTH(sales.created_at) = MONTH(CURDATE())
                                            AND YEAR(sales.created_at) = YEAR(CURDATE())
                                            GROUP BY items.id ORDER BY itemCount ASC LIMIT 1"))->first();

                $ordersThisMonth = collect(DB::select("SELECT SUM(total) AS totalAmount, SUM(discount) AS totalDiscount 
                                    FROM orders
                                    WHERE MONTH(created_at) = MONTH(CURDATE())
                                    AND YEAR(created_at) = YEAR(CURDATE())
                                    LIMIT 1"))->first();

                $purchaseOrdersThisMonth = collect(DB::select("SELECT SUM(total) AS totalAmount FROM purchase_orders 
                                            WHERE MONTH(created_at) = MONTH(CURDATE()) AND 
                                            YEAR(created_at) = YEAR(CURDATE()) 
                                            LIMIT 1"))->first();

                $expensesThisMonth = collect(DB::select("SELECT SUM(cost) AS totalExpenses FROM expenses 
                                                WHERE MONTH(created_at) = MONTH(CURDATE()) AND 
                                                YEAR(created_at) = YEAR(CURDATE()) 
                                                LIMIT 1"))->first();
                
                $netProfitThisMonth = (($ordersThisMonth->totalAmount - $ordersThisMonth->totalDiscount) - $purchaseOrdersThisMonth->totalAmount) - $expensesThisMonth->totalExpenses;

                //last month
                $ordersLastMonth = collect(DB::select("SELECT SUM(total) AS totalAmount, SUM(discount) AS totalDiscount 
                                            FROM orders
                                            WHERE YEAR(created_at) = YEAR(CURRENT_DATE - INTERVAL 1 MONTH)
                                            AND MONTH(created_at) = MONTH(CURRENT_DATE - INTERVAL 1 MONTH)
                                            LIMIT 1"))->first();

                $purchaseOrdersLastMonth = collect(DB::select("SELECT SUM(total) AS totalAmount FROM purchase_orders 
                                                    WHERE YEAR(created_at) = YEAR(CURRENT_DATE - INTERVAL 1 MONTH)
                                                    AND MONTH(created_at) = MONTH(CURRENT_DATE - INTERVAL 1 MONTH)
                                                    LIMIT 1"))->first();

                $expensesLastMonth = collect(DB::select("SELECT SUM(cost) AS totalExpenses FROM expenses 
                                                WHERE YEAR(created_at) = YEAR(CURRENT_DATE - INTERVAL 1 MONTH)
                                                AND MONTH(created_at) = MONTH(CURRENT_DATE - INTERVAL 1 MONTH)
                                                LIMIT 1"))->first();
                
                $netProfitLastMonth = (($ordersLastMonth->totalAmount - $ordersLastMonth->totalDiscount) - $purchaseOrdersLastMonth->totalAmount) - $expensesLastMonth->totalExpenses;

                $percentDifference = 0;

                if($netProfitThisMonth == 0 && $netProfitLastMonth == 0)
                {
                    $percentDifference = 0;
                }
                elseif(($netProfitThisMonth == 0 && $netProfitLastMonth) > 0 ||
                        ($netProfitLastMonth == 0 && $netProfitThisMonth > 0))
                {
                    $percentDifference = 100;
                }
                elseif(($netProfitThisMonth == 0 && $netProfitLastMonth) < 0 ||
                        ($netProfitLastMonth == 0 && $netProfitThisMonth < 0))
                {
                    $percentDifference = 100;
                }
                else
                {
                    if($netProfitThisMonth > $netProfitLastMonth)
                    {
                        $percentDifference = (($netProfitThisMonth - $netProfitLastMonth) / $netProfitThisMonth) * 100;
                    }
                    elseif($netProfitLastMonth > $netProfitThisMonth)
                    {
                        $percentDifference = (($netProfitLastMonth - $netProfitThisMonth) / $netProfitLastMonth) * 100;
                    }
                    else
                    {
                        $percentDifference = (($netProfitLastMonth - $netProfitThisMonth) / $netProfitLastMonth) * 100;
                    }
                }

                $percentDifference = round($percentDifference, 2);

                $totalPayable = collect(DB::select("SELECT SUM(balance) AS amount FROM purchase_orders LIMIT 1"))->first();
                $totalReceivable = collect(DB::select("SELECT SUM(balance) AS amount FROM orders LIMIT 1"))->first();

                return view('home.index', compact('topSellingItem', 'worstSellingItem', 'occurrences', 'netProfitThisMonth', 'netProfitLastMonth', 'percentDifference', 'expensesThisMonth', 'totalPayable', 'totalReceivable'));
            }
            $message = "Login";
            return view('home.login', compact('message'));
        }
        else
        {
            return redirect('/sale/create');
        }
    }

    public function getSalesGraph($type) 
    {
        $sales = [];

        DB::statement("SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));");

        if($type == 1)
        {
            $sales = DB::select("SELECT YEAR(created_at) AS 'interval', COUNT(id) AS 'sale', 
                                SUM(sub_total) AS 'total' FROM orders WHERE created_at >= DATE_SUB(now(), INTERVAL 10 YEAR) 
                                GROUP BY YEAR(created_at)");
        }
        elseif($type == 2)
        {
            $sales = DB::select("SELECT CONCAT(MONTHNAME(created_at), '-', YEAR(created_at)) AS 'interval', COUNT(id) AS 'sale', 
                                SUM(sub_total) AS 'total' FROM orders WHERE created_at >= DATE_SUB(now(), INTERVAL 12 MONTH) 
                                GROUP BY YEAR(created_at), MONTH(created_at)");
        }
        elseif($type == 3)
        {
            $sales = DB::select("SELECT CONCAT(DAY(created_at), '-',MONTHNAME(created_at), '-', YEAR(created_at)) AS 'interval', COUNT(id) AS 'sale', 
                    SUM(sub_total) AS 'total' FROM orders WHERE created_at >= DATE_SUB(now(), INTERVAL 40 DAY) 
                    GROUP BY YEAR(created_at), MONTH(created_at), DAY(created_at)");
        }

        DB::statement("SET sql_mode=(SELECT CONCAT(@@sql_mode, ',ONLY_FULL_GROUP_BY'));");

        return json_encode($sales);
    }

    public function topSellingItemByFixedDuration($type)
    {
        $topSellingItem = null;

        if($type == 1)
        {
            $topSellingItem = DB::select("SELECT COUNT(sales.item_id) AS itemCount, SUM(sales.price) AS totalPrice, CONCAT(SUM(sales.quantity), IF(items.unit_id=1, ' kg', IF(items.unit_id=2, ' dozen', ''))) AS totalQuantity, items.name AS item 
                    FROM items 
                    LEFT JOIN sales ON items.id = sales.item_id
                    WHERE YEAR(sales.created_at) = YEAR(CURDATE())
                    GROUP BY items.id ORDER BY itemCount DESC LIMIT 1");
        }
        elseif($type == 2)
        {
            $topSellingItem = DB::select("SELECT COUNT(sales.item_id) AS itemCount, SUM(sales.price) AS totalPrice, CONCAT(SUM(sales.quantity), IF(items.unit_id=1, ' kg', IF(items.unit_id=2, ' dozen', ''))) AS totalQuantity, items.name AS item 
                    FROM items 
                    LEFT JOIN sales ON items.id = sales.item_id
                    WHERE MONTH(sales.created_at) = MONTH(CURDATE())
                    AND YEAR(sales.created_at) = YEAR(CURDATE())
                    GROUP BY items.id ORDER BY itemCount DESC LIMIT 1");
        }
        elseif($type == 3)
        {
            $topSellingItem = DB::select("SELECT COUNT(sales.item_id) AS itemCount, SUM(sales.price) AS totalPrice, CONCAT(SUM(sales.quantity), IF(items.unit_id=1, ' kg', IF(items.unit_id=2, ' dozen', ''))) AS totalQuantity, items.name AS item 
                    FROM items 
                    LEFT JOIN sales ON items.id = sales.item_id
                    WHERE WEEK(sales.created_at) = WEEK(CURDATE())
                    AND YEAR(sales.created_at) = YEAR(CURDATE())
                    GROUP BY items.id ORDER BY itemCount DESC LIMIT 1");
        }
        elseif($type == 4)
        {
            $topSellingItem = DB::select("SELECT COUNT(sales.item_id) AS itemCount, SUM(sales.price) AS totalPrice, CONCAT(SUM(sales.quantity), IF(items.unit_id=1, ' kg', IF(items.unit_id=2, ' dozen', ''))) AS totalQuantity, items.name AS item 
                    FROM items 
                    LEFT JOIN sales ON items.id = sales.item_id
                    WHERE DAY(sales.created_at) = DAY(CURDATE())
                    AND MONTH(sales.created_at) = MONTH(CURDATE())
                    AND YEAR(sales.created_at) = YEAR(CURDATE())
                    GROUP BY items.id ORDER BY itemCount DESC LIMIT 1");
        }
        elseif($type == 5)
        {
            $topSellingItem = DB::select("SELECT COUNT(sales.item_id) AS itemCount, SUM(sales.price) AS totalPrice, CONCAT(SUM(sales.quantity), IF(items.unit_id=1, ' kg', IF(items.unit_id=2, ' dozen', ''))) AS totalQuantity, items.name AS item 
                    FROM items 
                    LEFT JOIN sales ON items.id = sales.item_id
                    WHERE HOUR(sales.created_at) = HOUR(NOW())
                    AND DAY(sales.created_at) = DAY(CURDATE())
                    AND MONTH(sales.created_at) = MONTH(CURDATE())
                    AND YEAR(sales.created_at) = YEAR(CURDATE())
                    GROUP BY items.id ORDER BY itemCount DESC LIMIT 1");
        }
        elseif($type == 6)
        {
            $topSellingItem = DB::select("SELECT COUNT(sales.item_id) AS itemCount, SUM(sales.price) AS totalPrice, CONCAT(SUM(sales.quantity), IF(items.unit_id=1, ' kg', IF(items.unit_id=2, ' dozen', ''))) AS totalQuantity, items.name AS item 
                    FROM items 
                    LEFT JOIN sales ON items.id = sales.item_id
                    GROUP BY items.id ORDER BY itemCount DESC LIMIT 1");
        }

        return json_encode(collect($topSellingItem)->first());
    }

    public function worstSellingItemByFixedDuration($type)
    {
        $worstSellingItem = null;

        if($type == 1)
        {
            $worstSellingItem = DB::select("SELECT COUNT(sales.item_id) AS itemCount, SUM(sales.price) AS totalPrice, CONCAT(SUM(sales.quantity), IF(items.unit_id=1, ' kg', IF(items.unit_id=2, ' dozen', ''))) AS totalQuantity, items.name AS item 
                    FROM items 
                    LEFT JOIN sales ON items.id = sales.item_id
                    WHERE YEAR(sales.created_at) = YEAR(CURDATE())
                    GROUP BY items.id ORDER BY itemCount ASC LIMIT 1");
        }
        elseif($type == 2)
        {
            $worstSellingItem = DB::select("SELECT COUNT(sales.item_id) AS itemCount, SUM(sales.price) AS totalPrice, CONCAT(SUM(sales.quantity), IF(items.unit_id=1, ' kg', IF(items.unit_id=2, ' dozen', ''))) AS totalQuantity, items.name AS item 
                    FROM items 
                    LEFT JOIN sales ON items.id = sales.item_id
                    WHERE MONTH(sales.created_at) = MONTH(CURDATE())
                    AND YEAR(sales.created_at) = YEAR(CURDATE())
                    GROUP BY items.id ORDER BY itemCount ASC LIMIT 1");
        }
        elseif($type == 3)
        {
            $worstSellingItem = DB::select("SELECT COUNT(sales.item_id) AS itemCount, SUM(sales.price) AS totalPrice, CONCAT(SUM(sales.quantity), IF(items.unit_id=1, ' kg', IF(items.unit_id=2, ' dozen', ''))) AS totalQuantity, items.name AS item 
                    FROM items 
                    LEFT JOIN sales ON items.id = sales.item_id
                    WHERE WEEK(sales.created_at) = WEEK(CURDATE())
                    AND YEAR(sales.created_at) = YEAR(CURDATE())
                    GROUP BY items.id ORDER BY itemCount ASC LIMIT 1");
        }
        elseif($type == 4)
        {
            $worstSellingItem = DB::select("SELECT COUNT(sales.item_id) AS itemCount, SUM(sales.price) AS totalPrice, CONCAT(SUM(sales.quantity), IF(items.unit_id=1, ' kg', IF(items.unit_id=2, ' dozen', ''))) AS totalQuantity, items.name AS item 
                    FROM items 
                    LEFT JOIN sales ON items.id = sales.item_id
                    WHERE DAY(sales.created_at) = DAY(CURDATE())
                    AND MONTH(sales.created_at) = MONTH(CURDATE())
                    AND YEAR(sales.created_at) = YEAR(CURDATE())
                    GROUP BY items.id ORDER BY itemCount ASC LIMIT 1");
        }
        elseif($type == 5)
        {
            $worstSellingItem = DB::select("SELECT COUNT(sales.item_id) AS itemCount, SUM(sales.price) AS totalPrice, CONCAT(SUM(sales.quantity), IF(items.unit_id=1, ' kg', IF(items.unit_id=2, ' dozen', ''))) AS totalQuantity, items.name AS item 
                    FROM items 
                    LEFT JOIN sales ON items.id = sales.item_id
                    WHERE HOUR(sales.created_at) = HOUR(NOW())
                    AND DAY(sales.created_at) = DAY(CURDATE())
                    AND MONTH(sales.created_at) = MONTH(CURDATE())
                    AND YEAR(sales.created_at) = YEAR(CURDATE())
                    GROUP BY items.id ORDER BY itemCount ASC LIMIT 1");
        }
        elseif($type == 6)
        {
            $worstSellingItem = DB::select("SELECT COUNT(sales.item_id) AS itemCount, SUM(sales.price) AS totalPrice, CONCAT(SUM(sales.quantity), IF(items.unit_id=1, ' kg', IF(items.unit_id=2, ' dozen', ''))) AS totalQuantity, items.name AS item 
                    FROM items 
                    LEFT JOIN sales ON items.id = sales.item_id
                    GROUP BY items.id ORDER BY itemCount ASC LIMIT 1");
        }

        return json_encode(collect($worstSellingItem)->first());
    }

    public function netProfitFixedDuration($type)
    {
        $condition = "";
        $lastMonthCondition = "";

        if($type == 1)
        {
            $condition = "WHERE YEAR(created_at) = YEAR(CURDATE())";
            $lastMonthCondition = "WHERE YEAR(created_at) = YEAR(CURRENT_DATE - INTERVAL 1 YEAR)";
        }
        elseif($type == 2)
        {
            $condition = "WHERE MONTH(created_at) = MONTH(CURDATE())
            AND YEAR(created_at) = YEAR(CURDATE())";
            $lastMonthCondition = "WHERE YEAR(created_at) = YEAR(CURRENT_DATE - INTERVAL 1 MONTH)
            AND MONTH(created_at) = MONTH(CURRENT_DATE - INTERVAL 1 MONTH)";
        }
        elseif($type == 3)
        {
            $condition = "WHERE WEEK(created_at) = WEEK(CURDATE())
                            AND YEAR(created_at) = YEAR(CURDATE())";
            $lastMonthCondition = "WHERE WEEK(created_at) = WEEK(CURRENT_DATE - INTERVAL 1 WEEK) AND 
                                    YEAR(created_at) = YEAR(CURRENT_DATE - INTERVAL 1 WEEK)";
        }
        elseif($type == 4)
        {
            $condition = "WHERE DAY(created_at) = DAY(CURDATE())
                            AND MONTH(created_at) = MONTH(CURDATE())
                            AND YEAR(created_at) = YEAR(CURDATE())";
            $lastMonthCondition = "WHERE DAY(created_at) = DAY(CURRENT_DATE - INTERVAL 1 DAY)
                                    AND YEAR(created_at) = YEAR(CURRENT_DATE - INTERVAL 1 DAY)
                                    AND MONTH(created_at) = MONTH(CURRENT_DATE - INTERVAL 1 DAY)";
        }
        elseif($type == 5)
        {
            $condition = "WHERE HOUR(created_at) = HOUR(NOW())
                            AND DAY(created_at) = DAY(CURDATE())
                            AND MONTH(created_at) = MONTH(CURDATE())
                            AND YEAR(created_at) = YEAR(CURDATE())";
            $lastMonthCondition = "WHERE HOUR(created_at) = HOUR(CURRENT_DATE - INTERVAL 1 HOUR)
                                    AND DAY(created_at) = DAY(CURRENT_DATE - INTERVAL 1 HOUR)
                                    AND MONTH(created_at) = MONTH(CURRENT_DATE - INTERVAL 1 HOUR)
                                    AND YEAR(created_at) = YEAR(CURRENT_DATE - INTERVAL 1 HOUR)";
        }
        elseif($type == 6)
        {
            $condition = "";
            $lastMonthCondition = "";
        }

        $ordersThisMonth = collect(DB::select("SELECT SUM(total) AS totalAmount, SUM(discount) AS totalDiscount 
                    FROM orders " . $condition . "  LIMIT 1"))->first();

        $purchaseOrdersThisMonth = collect(DB::select("SELECT SUM(total) AS totalAmount FROM purchase_orders " . $condition . " LIMIT 1"))->first();

        $expensesThisMonth = collect(DB::select("SELECT SUM(cost) AS totalExpenses FROM expenses " . $condition . " LIMIT 1"))->first();
        
        $netProfitThisMonth = (($ordersThisMonth->totalAmount - $ordersThisMonth->totalDiscount) - $purchaseOrdersThisMonth->totalAmount) - $expensesThisMonth->totalExpenses;
        
        $netProfitLastMonth = 0;
        $percentDifference = 0;

        if($type != 6)
        {
            //last month
            $ordersLastMonth = collect(DB::select("SELECT SUM(total) AS totalAmount, SUM(discount) AS totalDiscount 
            FROM orders
            " . $lastMonthCondition . "
            LIMIT 1"))->first();

            $purchaseOrdersLastMonth = collect(DB::select("SELECT SUM(total) AS totalAmount FROM purchase_orders 
                        " . $lastMonthCondition . "
                            LIMIT 1"))->first();

            $expensesLastMonth = collect(DB::select("SELECT SUM(cost) AS totalExpenses FROM expenses 
                        " . $lastMonthCondition . "
                        LIMIT 1"))->first();

            $netProfitLastMonth = (($ordersLastMonth->totalAmount - $ordersLastMonth->totalDiscount) - $purchaseOrdersLastMonth->totalAmount) - $expensesLastMonth->totalExpenses;

            $percentDifference = 0;

            if($netProfitThisMonth == 0 && $netProfitLastMonth == 0)
            {
                $percentDifference = 0;
            }
            elseif(($netProfitThisMonth == 0 && $netProfitLastMonth) > 0 ||
                    ($netProfitLastMonth == 0 && $netProfitThisMonth > 0))
            {
                $percentDifference = 100;
            }
            else 
            {
                if($netProfitThisMonth > $netProfitLastMonth)
                {
                    $percentDifference = (($netProfitThisMonth - $netProfitLastMonth) / $netProfitThisMonth) * 100;
                }
                elseif($netProfitLastMonth > $netProfitThisMonth)
                {
                    $percentDifference = (($netProfitLastMonth - $netProfitThisMonth) / $netProfitLastMonth) * 100;
                }
                else
                {
                    $percentDifference = (($netProfitLastMonth - $netProfitThisMonth) / $netProfitLastMonth) * 100;
                }
            }
        }

        $percentDifference = round($percentDifference, 2);

        return json_encode(['netProfitThisMonth' => $netProfitThisMonth,
                            'netProfitLastMonth' => $netProfitLastMonth,
                            'percentDifference' =>  $percentDifference ]);
    }

    public function getTotalExpenses($type)
    {
        $condition = "";

        if($type == 1)
        {
            $condition = "WHERE YEAR(created_at) = YEAR(CURDATE())";
        }
        elseif($type == 2)
        {
            $condition = "WHERE MONTH(created_at) = MONTH(CURDATE())
            AND YEAR(created_at) = YEAR(CURDATE())";
        }
        elseif($type == 3)
        {
            $condition = "WHERE WEEK(created_at) = WEEK(CURDATE())
                            AND YEAR(created_at) = YEAR(CURDATE())";
        }
        elseif($type == 4)
        {
            $condition = "WHERE DAY(created_at) = DAY(CURDATE())
                            AND MONTH(created_at) = MONTH(CURDATE())
                            AND YEAR(created_at) = YEAR(CURDATE())";
        }
        elseif($type == 5)
        {
            $condition = "WHERE HOUR(created_at) = HOUR(NOW())
                            AND DAY(created_at) = DAY(CURDATE())
                            AND MONTH(created_at) = MONTH(CURDATE())
                            AND YEAR(created_at) = YEAR(CURDATE())";
        }
        elseif($type == 6)
        {
            $condition = "";
        }

        $expensesThisMonth = collect(DB::select("SELECT SUM(cost) AS totalExpenses 
                                FROM expenses " . $condition . " LIMIT 1"))->first();

        return json_encode($expensesThisMonth);
    }

    public function busyHour()
    {
        $yesterday = date("Y-m-d", strtotime("-1 days"));
        $beforeSixDays = date("Y-m-d", strtotime("-1 week +1 day"));

        dd($yesterday);
    }
}
