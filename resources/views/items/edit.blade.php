@extends('master')

@section('content')
    <div class="container">
        <form action="{{ url('/item/') . '/' . $item->id }}" enctype="multipart/form-data" method="POST">
        @method('PUT')
        @csrf
            <div class="row">
                <div class="col-12">
                    <div class="row">
                        <h2>Edit Item</h2>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-6 pl-5">

                            <div class="form-group row">
                                <label for="category_id" class="col-md-4 col-form-label text-md-right">Category</label>
                                <div class="col-md-6">
                                    <select id="category_id" class="form-control @error('category_id') is-invalid @enderror" name="category_id" required>
                                        <option value="">Please select a Category</option>
                                        @foreach ($categories as $category)
                                        <option value="{{ $category->id }}" @if($item->category_id == $category->id) {{ 'selected' }} @endif>{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('category_id')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="name" class="col-md-4 col-form-label text-md-right">Name</label>
                                <div class="col-md-6">
                                    <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ $item->name ?? old('name') }}" required autocomplete="name">
                    
                                    @error('name')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="reorder_level" class="col-md-4 col-form-label text-md-right">Reorder Level</label>
                                <div class="col-md-6">
                                    <input id="reorder_level" type="number" min="1" class="form-control @error('reorder_level') is-invalid @enderror" name="reorder_level" value="{{ $item->reorder_level ?? old('reorder_level') }}" required autocomplete="reorder_level">
                    
                                    @error('reorder_level')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="image" class="col-md-4 col-form-label text-md-right">Image</label>
                                <div class="col-md-6">
                                    <input id="image" type="file" class="form-control @error('image') is-invalid @enderror" name="image" value="{{ old('image') }}" autocomplete="image">
                    
                                    @error('image')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="unit_id" class="col-md-4 col-form-label text-md-right">Unit</label>
                                <div class="col-md-6">
                                    <select id="unit_id" class="form-control @error('unit_id') is-invalid @enderror" name="unit_id" required>
                                        <option value="">Please select a unit</option>
                                        <option value="1" @if($item->unit_id == 1) selected @endif>Kilogram</option>
                                        <option value="2" @if($item->unit_id == 2) selected @endif>Dozen</option>
                                        <option value="3" @if($item->unit_id == 3) selected @endif>Piece</option>
                                    </select>
                                    @error('unit_id')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="price" class="col-md-4 col-form-label text-md-right">Price</label>
                                <div class="col-md-6">
                                    <input id="price" type="number" min="0" class="form-control @error('price') is-invalid @enderror" name="price" value="{{ $item->rate->price ?? old('price') }}" autocomplete="price">
                    
                                    @error('price')
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