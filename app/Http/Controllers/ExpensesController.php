<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Expense;
use App\Configuration;

class ExpensesController extends Controller
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
        if(allowed(3, 'view'))
        {
            // $expenses = Expense::latest()->get();
            $expenses = Expense::latest()->paginate(50);

            $toDate = '';
            $fromDate = '';
            $total = '';
            
            return view('expenses.index', compact('expenses', 'toDate', 'fromDate', 'total'));
        }
        else
        {
            return redirect('/sale/create');
        }
    }

    public function expensesReport()
    {
        if(allowed(12, 'view'))
        {
            $expenses = [];

            $toDate = date("Y-m-d");
            $fromDate = date("Y-m-d");
            $total = '';
    
            $total = Expense::where([[DB::raw("date(created_at)"), '>=', $fromDate],
            [DB::raw("date(created_at)"), '<=', $toDate]])
            ->select(DB::raw("SUM(cost) AS totalExpense"))
            ->first();
                
            $expenses = Expense::where([[DB::raw("date(created_at)"), '>=', $fromDate],
                        [DB::raw("date(created_at)"), '<=', $toDate]])
                        ->latest()->paginate(50);
            
            return view('reports.expenses', compact('expenses', 'toDate', 'fromDate', 'total'));    
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
        ]);

        $fromDate = date('Y-m-d', strtotime($data['fromDate']));
        $toDate = date('Y-m-d', strtotime($data['toDate']));

        $expenses = '';
                        
        $expenses = Expense::where([[DB::raw("date(created_at)"), '>=', $fromDate],
                                [DB::raw("date(created_at)"), '<=', $toDate]])
                                ->latest()->paginate(50);
                                        
        return view('expenses.index', compact('expenses', 'fromDate', 'toDate'));
    }

    public function filterExpensesReport()
    {
        $data = request()->validate([
            'fromDate' => 'required',
            'toDate' => 'required'
        ]);

        $fromDate = date('Y-m-d', strtotime($data['fromDate']));
        $toDate = date('Y-m-d', strtotime($data['toDate']));

        $expenses = '';
        $total = '';

        $total = Expense::where([[DB::raw("date(created_at)"), '>=', $fromDate],
                            [DB::raw("date(created_at)"), '<=', $toDate]])
                        ->select(DB::raw("SUM(cost) AS totalExpense"))
                        ->first();
                        
        $expenses = Expense::where([[DB::raw("date(created_at)"), '>=', $fromDate],
                                [DB::raw("date(created_at)"), '<=', $toDate]])
                                ->latest()->paginate(50);
                                        
        return view('reports.expenses', compact('expenses', 'total', 'fromDate', 'toDate'));
    }

    public function create()
    {
        if(allowed(3, 'make'))
        {
            return view('expenses.create');
        }
        else
        {
            return redirect('/sale/create');
        }
    }

    public function store()
    {
        if(allowed(3, 'make'))
        {
            $data = request()->validate([
                'title' => 'required',
                'cost' => 'required|numeric',
                'date' => 'required',
            ]);
    
            Expense::create($data);
    
            return redirect('/expenses');
        }
        else
        {
            return redirect('/sale/create');
        }
    }

    public function destroy(Expense $expense)
    {
        if(allowed(3, 'remove'))
        {
            $expense->delete();

            return 'deleted';
        }
        else
        {
            return 0;
        }
    }
}
