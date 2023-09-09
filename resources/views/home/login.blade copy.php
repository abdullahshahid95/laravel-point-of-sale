
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ posConfigurations()->title }}</title>
        <link rel="shortcut icon" href="{{ url('assets/uploads/' . posConfigurations()->logo) }}" />

        <!-- Scripts -->
        <script src="{{ url('assets/js/app.js') }}"></script> {{-- includes jQuery --}}

        <script src="{{ url('assets/jquery-validation-1.19.1/dist/jquery.validate.min.js') }}"></script>
        <script src="{{ url('assets/jquery-validation-1.19.1/dist/additional-methods.min.js') }}"></script>

        <link href="{{ url('assets/select2/dist/css/select2.min.css') }}" rel="stylesheet" />
        <script src="{{ url('assets/select2/dist/js/select2.min.js') }}"></script>
        
        <link href="{{ url('assets/modal/jquery.modal.min.css') }}" rel="stylesheet" />
        <script src="{{ url('assets/modal/jquery.modal.min.js') }}"></script>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">
        <link rel="dns-prefetch" href="//fonts.gstatic.com">

        <!-- Styles -->
        <link href="{{ url('assets/css/app.css') }}" rel="stylesheet">

        <!-- Datatables -->
        <link rel="stylesheet" type="text/css" href="{{ url('assets/DataTables/datatables.min.css') }}"/>
    </head>
    <body>
        <div class="container">
            @if(Session::get('message'))
            <div class="row alert alert-success">
                <div class="col-12">
                    {{ Session::get('message') }}
                </div>
            </div>
            @endif

            <form action="{{url('/login')}}" enctype="multipart/form-data" method="POST">
                @csrf
                <div class="row">
                    <div class="col-12">
                        <div class="row">
                            <h2>Login</h2>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-6 pl-5">
                                <div class="form-group row">
                                    <label for="username" class="col-md-4 col-form-label text-md-right">Username</label>
                                    <div class="col-md-6">
                                        <input id="username" type="text" class="form-control @error('username') is-invalid @enderror" name="username" value="{{ old('username') }}" required autocomplete="username">
                        
                                        @error('username')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="password" class="col-md-4 col-form-label text-md-right">Password</label>
                                    <div class="col-md-6">
                                        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" value="{{ old('password') }}" required autocomplete="password">
                        
                                        @error('password')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-md-4"></div>
                                    <div class="col-md-6">
                                        <button class="btn btn-primary float-right" type="submit">Login</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </body>
</html>