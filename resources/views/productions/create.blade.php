@extends('master')

@section('content')
    <div class="container">
        <form action="{{ url('/production') }}" enctype="multipart/form-data" method="POST">
        @csrf

        <div class="row">
            <div class="col-12">
                <div class="row">
                    <h2>Add a Product</h2>
                </div>
                <hr>
        
                <div class="row">
                    <div class="col-6 pl-5">

                        <div class="form-group row">
                            <label for="product_id" class="col-md-4 col-form-label text-md-right">Product</label>
                            <div class="col-md-6">
                                <select id="product_id" class="form-control @error('product_id') is-invalid @enderror" name="product_id" required>
                                    <option value="">Please select a Product</option>
                                        @foreach ($products as $product)
                                            <option value="{{ $product->id }}" 
                                                data-unit="{{ $product->unit }}">
                                                {{ $product->name }}
                                            </option>
                                        @endforeach
                                </select>
                
                                @error('product_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="purchase_unit_id" class="col-md-4 col-form-label text-md-right">Quantity</label>
                            <div class="col-md-6">
                                <select id="purchase_unit_id" class="form-control" name="purchase_unit_id" required>
                                    <option value="">Please select quantity</option>
                                </select>
                
                                @error('purchase_unit_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-md-4"></div>
                            <div class="col-md-6">
                                <button class="btn btn-primary float-right" type="submit">Add</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </form>
    </div>

    <script type="text/javaScript">
        $(document).ready(function(){
            $("#product_id").select2();
            $("#department_id").select2();
            $("#purchase_unit_id").select2();

            $(document).on('select2:select', '#product_id', function(e){
                var item = $(this);
                $("#purchase_unit_id")
                            .find('option')
                            .remove()
                            .end()
                            .append('<option value="">Please select quantity</option>');
                        ;
                $("#purchase_unit_id").select2().trigger('change');

                if(item.children("option:selected").val() != '')
                {
                    var toAppend = "";
                    if(item.children("option:selected").attr("data-unit") == "kg")
                    {
                        toAppend = `@foreach ($units as $unit)
                                        @if($unit->type == "kg")
                                            <option value="{{ $unit->id }}" data-value="{{ $unit->value }}">
                                                {{ $unit->name}}
                                            </option>
                                        @endif
                                    @endforeach`;
                    }
                    else if(item.children("option:selected").attr("data-unit") == "gaddi")
                    {
                        toAppend = `@foreach ($units as $unit)
                                        @if($unit->type == "gaddi")
                                            <option value="{{ $unit->id }}" data-value="{{ $unit->value }}">
                                                {{ $unit->name}}
                                            </option>
                                        @endif
                                    @endforeach`;
                    }
                    else if(item.children("option:selected").attr("data-unit") == "darjan")
                    {
                        toAppend = `@foreach ($units as $unit)
                                        @if($unit->type == "darjan")
                                            <option value="{{ $unit->id }}" data-value="{{ $unit->value }}">
                                                {{ $unit->name}}
                                            </option>
                                        @endif
                                    @endforeach`;
                    }
                    else if(item.children("option:selected").attr("data-unit") == "piece")
                    {
                        toAppend = `@foreach ($units as $unit)
                                        @if($unit->type == "piece")
                                            <option value="{{ $unit->id }}" data-value="{{ $unit->value }}">
                                                {{ $unit->name}}
                                            </option>
                                        @endif
                                    @endforeach`;
                    }
                    else if(item.children("option:selected").attr("data-unit") == "packet")
                    {
                        toAppend = `@foreach ($units as $unit)
                                        @if($unit->type == "packet")
                                            <option value="{{ $unit->id }}" data-value="{{ $unit->value }}">
                                                {{ $unit->name}}
                                            </option>
                                        @endif
                                    @endforeach`;
                    }

                    $("#purchase_unit_id").append(toAppend);
                }
            });
        });
    </script>
@endsection