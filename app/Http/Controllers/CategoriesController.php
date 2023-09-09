<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Intervention\Image\Facades\Image;

use App\Category;
use App\Item;
use App\RawInventory;
use App\Rate;
use App\Purchase;
use App\Configuration;

class CategoriesController extends Controller
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
        if(allowed(4, 'view'))
        {
            if($request->input('searchValue'))
            {
                $searchValue = $request->input('searchValue');
    
                $categories = DB::table('categories')
                                ->where(DB::raw("LOCATE('{$searchValue}', name)"), '>', 0)
                                ->orderBy('id', 'ASC')->paginate(5);
    
                return view('categories.index', compact('categories', 'searchValue'));
            }
    
            $searchValue = null;
            
            $categories = Category::orderBy('id', 'ASC')->paginate(5);
            
            return view('categories.index', compact('categories', 'searchValue'));
        }
        else
        {
            return redirect('/sale/create');
        }
    }

    public function create()
    {
        if(allowed(4, 'make'))
        {
            return view('categories.create');
        }
        else
        {
            return redirect('/sale/create');
        }
    }

    public function store()
    {
        if(allowed(4, 'make'))
        {
            $data = request()->validate([
                'name' => 'required',
                'image' => 'image'
            ]);
    
            $name = NULL;
    
            if(array_key_exists('image', $data) && $data['image'])
            {
                $file = request()->file('image');
                $extension = pathInfo($file->getClientOriginalName())['extension'];
    
                $name = time() . '.' . $extension;
    
                $file->move("uploads/", $name);
    
                $image = Image::make("uploads/{$name}")->fit(100, 100);
                $image->save();
            }
            
            $category = Category::firstOrCreate(['name' => $data['name'], 'image' => $name]);
    
            return redirect('/categories');
        }
        else
        {
            return redirect('/sale/create');
        }
    }

    public function edit(Category $category)
    {
        if(allowed(4, 'edit'))
        {
            return view('categories.edit', compact('category'));
        }
        else
        {
            return redirect('/sale/create');
        }
    }

    public function update(Category $category)
    {
        if(allowed(4, 'edit'))
        {
            $data = request()->validate([
                'name' => 'required',
                'image' => 'image'
            ]);
    
            $name = NULL;
    
            if(array_key_exists('image', $data) && $data['image'])
            {
                if($category->image && file_exists('uploads/' . $category->image))
                {
                    unlink('uploads/' . $category->image);
                }
    
                $file = request()->file('image');
                $extension = pathInfo($file->getClientOriginalName())['extension'];
    
                $name = time() . '.' . $extension;
    
                $file->move("uploads/", $name);
    
                $image = Image::make("uploads/{$name}")->fit(100, 100);
                $image->save();
    
                $category->update(['name' => $data['name'], 'image' => $name]);
                return redirect('/categories');
            }
    
            $category->update(['name' => $data['name']]);
        
            return redirect('/categories');
        }
        else
        {
            return redirect('/sale/create');
        }
    }

    public function destroy(Category $category)
    {
        if(allowed(4, 'remove'))
        {
            $items = $category->items;

            foreach ($items as $item) 
            {
                $item->purchases()->delete();
                $item->sales()->delete();
                $item->inventory()->delete();
                $item->wastage()->delete();
                $item->rate()->delete();
    
                if($item->image && file_exists('uploads/' . $item->image))
                {
                    unlink('uploads/' . $item->image);
                }
        
                $item->delete();
            }
    
            if($category->image && file_exists('uploads/' . $category->image))
            {
                unlink('uploads/' . $category->image);
            }
    
            $category->delete();
    
            return 'deleted';
        }
        else
        {
            return 0;
        }
    }
}
