@extends('master')

@section('content')
    <div class="container">
        <form action="{{ url('/usage') }}" enctype="multipart/form-data" method="POST">
        @csrf

        <div class="row">
            <div class="col-12">
                <div class="row">
                    <h2>Add a Raw Material for Department</h2>
                </div>
                <hr>
        
                <div class="row">
                    <div class="col-6 pl-5">

                        <div class="form-group row">
                            <label for="item_id" class="col-md-4 col-form-label text-md-right">Item</label>
                            <div class="col-md-6">
                                <select id="item_id" class="form-control @error('item_id') is-invalid @enderror" name="item_id" required>
                                    <option value="">Please select a Raw Material</option>
                                        @foreach ($items as $item)
                                            <option value="{{ $item->id }}" 
                                                data-unit="{{ $item->unit }}">
                                                {{ $item->name }}
                                            </option>
                                        @endforeach
                                </select>
                
                                @error('item_id')
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
            $("#item_id").select2();
            $("#department_id").select2();
            $("#purchase_unit_id").select2();

            $(document).on('select2:select', '#item_id', function(e){
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

                    $("#purchase_unit_id").append(toAppend);
                }
            });
        });
    </script>
@endsection