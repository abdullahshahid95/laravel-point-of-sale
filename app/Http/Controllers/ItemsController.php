<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

use App\Category;
use App\Item;
use App\Rate;
use App\Purchase;
use App\Configuration;

class ItemsController extends Controller
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
        if(allowed(5, 'view'))
        {
            $categories = Category::all();

            if($request->input('searchValue'))
            {
                $searchValue = $request->input('searchValue');
    
                $items = DB::table('items')
                                ->join('categories', 'items.category_id', '=', 'categories.id')
                                ->join('rates', 'rates.item_id', '=', 'items.id')
                                ->where(DB::raw("LOCATE('{$searchValue}', items.name)"), '>', 0)
                                ->orWhere(DB::raw("LOCATE('{$searchValue}', items.sku)"), '>', 0)
                                ->orWhere(DB::raw("LOCATE('{$searchValue}', categories.name)"), '>', 0)
                                ->orWhere(DB::raw("LOCATE('{$searchValue}', rates.sale_price)"), '>', 0)
                                ->orWhere(DB::raw("LOCATE('{$searchValue}', rates.purchase_price)"), '>', 0)
                                ->select('items.*', 'categories.name AS category_name', 'rates.sale_price',
                                'rates.purchase_price')
                                ->orderBy('items.status', 'DESC')
                                ->orderBy('items.id', 'ASC')
                                ->paginate(10);
    
                return view('items.index', compact('items', 'searchValue', 'categories'));
            }
    
            $searchValue = null;
    
            $items = DB::table('items')
                        ->join('categories', 'items.category_id', '=', 'categories.id')
                        ->join('rates', 'rates.item_id', '=', 'items.id')
                        ->select('items.*', 'categories.name AS category_name', 'rates.sale_price',
                        'rates.purchase_price')
                        ->orderBy('items.status', 'DESC')
                        ->orderBy('items.id', 'ASC')
                        ->paginate(10);
            
            return view('items.index', compact('items', 'searchValue', 'categories'));
        }
        else
        {
            return redirect('/sale/create');
        }
    }

    public function create()
    {
        if(allowed(5, 'make'))
        {
            $categories = Category::all();

            return view('items.create', compact('categories'));    
        }
        else
        {
            return redirect('/sale/create');
        }
    }

    public function store()
    {
        if(allowed(5, 'make'))
        {
            $data = request()->validate([
                'category_id' => 'required',
                'name' => 'required',
                'label' => '',
                'reorder_level' => 'required',
                'image' => '',
                'unit_id' => 'required',
                'sale_price' => '',
                'purchase_price' => '',
                'tax' => '',
                'discount' => '',
                'rows_count' => 'required'
            ]);
    
            for($i = 1; $i <= $data['rows_count']; $i++)
            {
                if(request()->has('tax_type_' . $i))
                {
                    $data['tax_type'][] = request()->get('tax_type_' . $i);
                }
    
                if(request()->has('discount_type_' . $i))
                {
                    $data['discount_type'][] = request()->get('discount_type_' . $i);
                }
            }
    
            if(sizeof($data['category_id']) > 0 && sizeof($data['name']) > 0 && 
                sizeof($data['reorder_level']) > 0 && sizeof($data['unit_id']) > 0)
            {
                for($i = 0; $i < sizeof($data['category_id']); $i++)
                {
                    $name = NULL;
    
                    if(array_key_exists('image', $data) && array_key_exists($i, $data['image']) && $data['image'][$i])
                    {
                        $file = $data['image'][$i];
                        $extension = pathInfo($file->getClientOriginalName())['extension'];
    
                        $name = ((string) Str::uuid()) . '.' . $extension;
            
                        $file->move("uploads/", $name);
            
                        $image = Image::make("uploads/{$name}")->fit(100, 100);
                        $image->save();
                    }
    
                    $item = Item::firstOrCreate(['category_id' => $data['category_id'][$i],
                                                'name' => $data['name'][$i],
                                                'label' => trim($data['label'][$i]),
                                                'discount_type' => $data['discount_type'][$i],
                                                'discount' => $data['discount'][$i] ?? 0,
                                                'tax_type' => $data['tax_type'][$i],
                                                'tax' => $data['tax'][$i] ?? 0,
                                                'reorder_level' => $data['reorder_level'][$i],
                                                'unit_id' => $data['unit_id'][$i],
                                                'image' => $name]);
    
                    $sku = substr(Category::where('id', $data['category_id'][$i])->first()->name, 0, 2) . 
                            '-' . substr($data['name'][$i], 0, 2) . '-' . $item->id;
    
                    $item->update(['sku' => trim(strtolower($sku))]);
    
                    if(!trim($data['label'][$i]))
                    {
                        $label = $item->id;
            
                        if(strlen($item->id) < 8)
                        {
                            $digits = 8 - strlen($item->id);
                            $randomNumber = rand(pow(10, $digits-1), pow(10, $digits)-1);
                
                            $label .= $randomNumber;
                        }
                
                        $item->update(['label' => $label]);
                    }
                    
                    $item->inventory()->firstOrCreate(['quantity' => 0, 'cost' => 0]);
                    $item->rate()->firstOrCreate(['sale_price' => (array_key_exists($i, $data['sale_price']) && $data['sale_price'][$i] && $data['sale_price'][$i] > 0? $data['sale_price'][$i]: 0),
                                                'purchase_price' => (array_key_exists($i, $data['purchase_price']) && $data['purchase_price'][$i] && $data['purchase_price'][$i] > 0? $data['purchase_price'][$i]: 0)]);
                }
            }
    
            return redirect('/items');
        }
        else
        {
            return redirect('/sale/create');
        }
    }

    public function edit(Item $item)
    {
        if(allowed(5, 'edit'))
        {
            $categories = Category::all();

            return view('items.edit', compact('categories', 'item'));    
        }
        else
        {
            return redirect('/sale/create');
        }
    }

    public function update(Item $item, Request $request)
    {
        if(allowed(5, 'edit'))
        {
            $data = request()->validate([
                'category_id' => 'required',
                'name' => 'required',
                'label' => 'required',
                'tax_type' => 'required',
                'tax' => '',
                'discount_type' => 'required',
                'discount' => '',
                'reorder_level' => 'required',
                'image' => '',
                'unit_id' => 'required',
                'sale_price' => 'numeric|nullable',
                'purchase_price' => 'numeric|nullable',
                'redirect_url' => ''
            ]);
    
            $exists = Item::where([['id', '!=', $item->id],['label', '=', $data['label']]])->first();
            if($exists)
            {
                return redirect($data['redirect_url'])->withErrors(['message' => 'Please enter another label. Item with this lable already exists.']); 
            }
    
            $name = NULL;
    
            if(array_key_exists('image', $data) && $data['image'])
            {
                if($item->image && file_exists('uploads/' . $item->image))
                {
                    unlink('uploads/' . $item->image);
                }
    
                $file = request()->file('image');
                $extension = pathInfo($file->getClientOriginalName())['extension'];
    
                $name = ((string) Str::uuid()) . '.' . $extension;
    
                $file->move("uploads/", $name);
    
                $image = Image::make("uploads/{$name}")->fit(100, 100);
                $image->save();
            }
    
            $item->rate()->update(['sale_price' => $data['sale_price']? $data['sale_price']: 0,
                                'purchase_price' => $data['purchase_price']? $data['purchase_price']: 0]);
    
            $item->update(['category_id' => $data['category_id'],
                            'name' => $data['name'],
                            'label' => $data['label'],
                            'tax_type' => $data['tax_type'],
                            'tax' => $data['tax'],
                            'discount_type' => $data['discount_type'],
                            'discount' => $data['discount'],
                            'reorder_level' => $data['reorder_level'],
                            'unit_id' => $data['unit_id'],
                            'image' => $name? $name: $item->image
                            ]);
    
            return redirect($data['redirect_url']);
        }
        else
        {
            return redirect('/sale/create');
        }
    }

    public function deactivateItem(Item $item)
    {
        if(allowed(5, 'edit'))
        {
            $item->update(['status' => 0]);
            /*$item->purchases()->delete();
            $item->sales()->delete();
            $item->inventory()->delete();
            $item->rate()->delete();
            $item->wastage()->delete();
    
            $item->delete();*/
    
            return 'deactivated';
        }
        else
        {
            return 0;
        }
    }

    public function activateItem(Item $item)
    {
        if(allowed(5, 'edit'))
        {
            $item->update(['status' => 1]);
            /*$item->purchases()->delete();
            $item->sales()->delete();
            $item->inventory()->delete();
            $item->rate()->delete();
            $item->wastage()->delete();
    
            $item->delete();*/
    
            return 'activated';
        }
        else
        {
            return 0;
        }
    }

    public function destroy(Item $item)
    {
        if(allowed(5, 'remove'))
        {
            $item->update(['status' => 0]);
            $item->purchases()->delete();
            $item->sales()->delete();
            $item->inventory()->delete();
            $item->rate()->delete();
            $item->wastage()->delete();
    
            $item->delete();
    
            return 'deleted';
        }
        else
        {
            return 0;
        }
    }

    //For barcodes below

    public function generateBarcodes()
    {
        if(allowed(20, 'make'))
        {
            return view('barcodes.index');
        }
        else
        {
            return redirect('/sale/create');
        }
    }

    public function getItemsForBarcode()
    {
        if(allowed(20, 'make'))
        {
            $items = DB::table('items')
                        ->join('rates', 'rates.item_id', '=', 'items.id')
                        // ->where(DB::raw("LOCATE('{$searchValue}', items.name)"), '>', 0)
                        // ->orWhere(DB::raw("LOCATE('{$searchValue}', items.sku)"), '>', 0)
                        ->where('items.status', '=', 1)
                        ->select('items.*', 'rates.sale_price', 'rates.purchase_price')
                        ->orderBy('items.created_at', 'DESC')
                        ->get();
                        // ->paginate(20);

            return json_encode(['items' => $items]);
        }
        else
        {
            return json_encode(['items' => []]);
        }
    }
}
