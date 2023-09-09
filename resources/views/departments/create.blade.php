@extends('master')

@section('content')
    <div class="container">
        <form action="{{ url('/department') }}" enctype="multipart/form-data" method="POST">
        @csrf

            <div class="row">
                <div class="col-12">
                    <div class="row">
                        <h2>Add a Department</h2>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-6 pl-5">

                            <div class="form-group row">
                                <label for="name" class="col-md-4 col-form-label text-md-right">Name</label>
                                <div class="col-md-6">
                                    <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name">
                    
                                    @error('name')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-md-4"></div>
                                <div class="col-md-6">
                                    <button class="btn btn-primary float-right" type="submit">Submit</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script type="text/javaScript">
    </script>
@endsection