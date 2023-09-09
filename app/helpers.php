<?php

 function posConfigurations()
 {
    return App\Configuration::first();
 }

 $data = null;
 function globalData($dataParam = null)
 {
   if($dataParam)
   {
     $data = $dataParam;
   }
   else
   {
     return $data;
   }
 }

 function allowed($permissionId, $action)
 {
    if(auth()->user()->role_id == 1)
    {
      return 1;
    }

    $permission = auth()->user()->role->permissions()->where('permission_id', $permissionId)->first();
    if(auth()->user()->role->permissions()->where('permission_id', $permissionId)->first())
        return $permission[$action];
    else
        return 0;
 }

 function shopName()
 {
    return 'qamarunnisa';
 }

 function errorFile($message)
 {
    return `<!doctype html>

    <html lang="en">
    
      <head>
        <meta charset="utf-8">
    
        <title>¯\_(ツ)_/¯</title>
        <meta name="description" content="Error page example with random background image from Unsplash">
        <meta name="viewport" content="width=device-width, initial-scale=1">
    
        {{-- <link rel="icon" href="../images/favicon.png" type="image/png"> --}}
    
        <style>
            body, html {
                color: #ffffff;
                font-family: monospace;
                height: 100%;
                margin: 0;
                }
    
                body {
                background-position: center;
                background-repeat: no-repeat;
                background-size: cover;
                height: 100%;
                }
    
                a {
                color: #ffffff;
                display: inline-block;
                margin: 10px 0;
                }
    
                h1 {
                box-sizing: border-box;
                margin: 10px 0;
                }
    
                .overlay {
                background-color: rgba(0, 0, 0, .5);
                bottom: 0;
                left: 0;
                position: absolute;
                right: 0;
                top: 0;
                }
    
                /* .bottom {
                bottom: 30px;
                position: absolute;
                text-align: center;
                width: 100%;
                } */
                .center {
                margin: auto;
                width: 50%;
                padding: 10px;
                margin-top: 10%;
                }
        </style>
      </head>
    
      <body style="background-image:url('{{url('/uploads/exception-error.jpg')}}')">
        <div class="overlay">
          <div class="center">
            <h1>¯\_(ツ)_/¯</h1>
            <h1 style="font-size: 3em;">{{ $message }}</h1>
          </div>
        </div>
      </body>
    
    </html>`;
 }
?>