@extends('master')

@section('content')
    <div class="container">
        <form action="{{ url('/expense') }}" method="POST">
        @csrf

        <div class="row">
            <div class="col-12">
                <div class="row">
                    <h2>Add an expense</h2>
                </div>
                <hr>
        
                <div class="row">
                    <div class="col-6 pl-5">

                        <div class="form-group row">
                            <label for="title" class="col-md-4 col-form-label text-md-right">Title</label>
                            <div class="col-md-6">
                                <input id="title" type="text" class="form-control @error('title') is-invalid @enderror" name="title" value="{{ old('title') }}" required autocomplete="title">
                
                                @error('title')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="cost" class="col-md-4 col-form-label text-md-right">Cost</label>
                            <div class="col-md-6">
                                <input id="cost" type="number" class="form-control @error('cost') is-invalid @enderror" name="cost" value="{{ old('cost') }}" required autocomplete="cost">
                                
                                @error('cost')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="date" class="col-md-4 col-form-label text-md-right">Date</label>
                            <div class="col-md-6">
                                <input id="date" type="date" class="form-control @error('date') is-invalid @enderror" name="date" value="{{ old('date') }}" required autocomplete="date">
                
                                @error('date')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <!-- <div class="form-group row">
                            <label for="spent" class="col-md-4 col-form-label text-md-right">Spent ?</label>
                            <div class="col-md-6">
                                <select id="spent" class="form-control @error('spent') is-invalid @enderror" name="spent" value="{{ old('spent') }}" required>
                                    <option value="yes" selected>Yes</option>
                                    <option value="no">No</option>
                                </select>
                                @error('spent')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div> -->

                        <div class="form-group row">
                            <div class="col-md-4"></div>
                            <div class="col-md-6">
                                <button class="btn btn-primary float-right" type="submit">Submit</button>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 pl-5">
                        <!-- <div class="form-group row">
                            <label for="description" class="col-md-4 col-form-label text-md-right">Description</label>
                            <div class="col-md-6">
                                <textarea id="description" class="form-control @error('description') is-invalid @enderror" name="description">{{ old('description') }}</textarea>
                
                                @error('description')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div> -->

                        <!-- <div class="form-group row">
                            <label for="category" class="col-md-4 col-form-label text-md-right">Category</label>
                            <div class="col-md-6">
                                <select id="category" 
                                                    class="form-control @error('category') is-invalid @enderror" 
                                                    name="category" value="{{ old('category') }}">

                                    <option value="">No category selected</option>
                                    <option value="internet">Internet</option>
                                    <option value="computer">Computer</option>
                                    <option value="electricity">Electricity</option>
                                    <option value="water">Water</option>
                                    <option value="gas">Gas</option>
                                    <option value="other">Other</option>
                                </select>
                
                                @error('category')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div> -->

                        <!-- <div class="form-group row">
                            <label for="quantity" class="col-md-4 col-form-label text-md-right">Quantity</label>
                            <div class="col-md-6">
                                <input id="quantity" type="text" class="form-control @error('quantity') is-invalid @enderror" name="quantity" value="{{ old('quantity') }}" autocomplete="quantity">
                
                                @error('quantity')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div> -->

                        <!-- <div class="form-group row">
                            <label for="time" class="col-md-4 col-form-label text-md-right">Time</label>
                            <div class="col-md-6">
                                <input id="time" type="time" class="form-control @error('time') is-invalid @enderror" name="time" value="{{ old('time') }}" autocomplete="time">
                
                                @error('time')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div> -->
                    </div>
                </div>
            </div>
        </div>
        </form>
    </div>
@endsection