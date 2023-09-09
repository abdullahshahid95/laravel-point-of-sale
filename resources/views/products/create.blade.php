@extends('master')

@section('content')
    <div class="container">
        <form action="/product" enctype="multipart/form-data" method="POST">
        @csrf

            <div class="row">
                <div class="col-12">
                    <div class="row">
                        <h2>Add an Item</h2>
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
                                <label for="department_id" class="col-md-4 col-form-label text-md-right">Department</label>
                                <div class="col-md-6">
                                    <select id="department_id" class="form-control @error('department_id') is-invalid @enderror" name="department_id" required>
                                        <option value="">Please select a Department</option>
                                            @foreach ($departments as $department)
                                                <option value="{{ $department->id }}">
                                                    {{ $department->name }}
                                                </option>
                                            @endforeach
                                    </select>
                    
                                    @error('department_id')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="unit" class="col-md-4 col-form-label text-md-right">Unit</label>
                                <div class="col-md-6">
                                    <select id="unit" class="form-control @error('unit') is-invalid @enderror" name="unit" required>
                                        <option value="">Please select a unit</option>
                                        <option value="kg">Kilo</option>
                                        <option value="gaddi">Gaddi</option>
                                        <option value="darjan">Darjan</option>
                                        <option value="piece">Piece</option>
                                        <option value="packet">Packet</option>
                                    </select>
                                    @error('unit')
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
        $("#category_id").select2();
    </script>
@endsection