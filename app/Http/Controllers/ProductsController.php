<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;


;
use App\Order;

use App\Rate;
use App\Purchase;

class ProductsController extends Controller
{
    public function index()
    {
        $products = Product::orderBy('id', 'ASC')->get();
        
        return view('products.index', compact('products'));
    }

    public function create()
    {
        $departments = Department::all();
        return view('products.create', compact('departments'));
    }

    public function store()
    {
        $data = request()->validate([
            'name' => 'required',
            'department_id' => 'required',
            'unit' => 'required'
        ]);
        
        $product = Product::firstOrCreate($data);

        $product->inventory()->create(['quantity' => 0]);

        $product->rate()->create(['price' => 0]);

        return redirect('/products');
    }

    public function destroy(Product $product)
    {
        $product->productions()->delete();
        $product->inventory()->delete();
        $product->inventoryDeductions()->delete();
        if(sizeof($product->sales) > 0)
        {
            $orderId = $product->sales()->first()->order_id;
            Order::where('id', $orderId)->delete();
        }
        $product->sales()->delete();
        $product->rate()->delete();
        $product->delete();

        return 'deleted';
    }
}
