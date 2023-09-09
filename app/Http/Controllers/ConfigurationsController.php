<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

use App\User;
use App\Configuration;
use App\Role;
use App\Permission;
use App\PermissionRole;

class ConfigurationsController extends Controller
{
    public function edit()
    {
        if(!auth()->check() || (auth()->check() && auth()->user()->id != 1))
        {
            return redirect('/');
        }

        $configurations = Configuration::first();

        return view('configurations.update', compact('configurations'));
    }

    public function update()
    {
        if(!auth()->check() || (auth()->check() && auth()->user()->id != 1))
        {
            return redirect('/');
        }

        $data = request()->validate([
            'title' => 'required',
            'subtitle' => '',
            'contact' => 'required',
            'address' => 'required',
            'background_image' => 'image',
            'logo' => 'image',
            'expiry_date' => 'required',
            'status' => 'required',
            'maintain_inventory' => 'required',
            'thank_note' => '',
            'terms_conditions' => '',
            'footer_text' => '',
            'footer_number' => ''
        ]);

        $configurations = Configuration::first();

        if(array_key_exists('background_image', $data))
        {
            if(file_exists("uploads/" . $configurations->background_image))
            {
                unlink("uploads/" . $configurations->background_image);
            }

            $backgroundImage = request()->file('background_image');
            $backgroundImageExtension = pathInfo($backgroundImage->getClientOriginalName())['extension'];

            $backgroundImageName = time() . 'b.' . $backgroundImageExtension;
            $backgroundImage->move("uploads/", $backgroundImageName);

            $data['background_image'] = $backgroundImageName;
        }

        if(array_key_exists('logo', $data))
        {
            if(file_exists("uploads/" . $configurations->logo))
            {
                unlink("uploads/" . $configurations->logo);
            }

            $logo = request()->file('logo');

            $logoExtension = pathInfo($logo->getClientOriginalName())['extension'];
            $logoName = time() . 'l.' . $logoExtension;
            $logo->move("uploads/", $logoName);
            $logo = Image::make("uploads/{$logoName}")->fit(500, 500);
            $logo->save();
    
            $data['logo'] = $logoName;
        }

        $configurations->update($data);

        return redirect('/configuration');
    }
}
