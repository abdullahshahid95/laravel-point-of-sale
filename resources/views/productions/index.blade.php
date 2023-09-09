@extends('master')

@section('content')
    <div class="container">
        <div class="row pb-3">
            <a href="{{ url('/production/create') }}" class="btn btn-primary offset-10">Add a Product</a>
        </div>

        <form action="{{ url('/production/filter') }}" method="GET" class="no-print">
            <div class="row">
                <div class="col-4">
                    <label for="fromDate">From</label>
                    <input id="fromDate" type="date" class="form-control @error('fromDate') is-invalid @enderror" name="fromDate" value="@if($fromDate){{$fromDate}}@endif" autocomplete="fromDate">                                    
                    
                    @error('fromDate')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="col-4">
                    <label for="toDate">To</label>
                    <input id="toDate" type="date" class="form-control @error('toDate') is-invalid @enderror" name="toDate" value="@if($toDate){{$toDate}}@endif" autocomplete="toDate">

                    @error('toDate')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="col-4">
                    <label for="selectedItems">Product(s)</label>
                    <select id="selectedItems" class="form-control selectedItems" name="selectedItems[]" multiple="multiple">
                        @foreach ($products as $product)
                            <option value="{{ $product->id }}" @if(in_array($product->id, $selectedItems)) selected @endif>{{ $product->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-4">
                    <label for="selectedDepartments">Department(s)</label>
                    <select id="selectedDepartments" class="form-control selectedDepartments" name="selectedDepartments[]" multiple="multiple">
                        @foreach ($departments as $department)
                            <option value="{{ $department->id }}" @if(in_array($department->id, $selectedDepartments)) selected @endif>{{ $department->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="row pt-2 pb-2">
                <div class="col-1">
                    <button type="submit" class="btn btn-primary">Search</button>
                </div>
                <div class="col-4">
                    <a href="{{ url('/productions') }}" class="btn btn-danger">Reset</a>
                </div>
            </div>
        </form>

        <div id="section-to-print">
            <div class="row pb-2">
                <div class="col-12">
                    <button class="btn btn-primary no-print" id="print-btn" onclick="window.print()">Print</button>
                </div>
            </div>
            @if($fromDate)
            <div class="row">
                <div class="col-5 offset-7">
                    <h4>
                        From {{ date('d M Y', strtotime($fromDate)) }} - To {{ date('d M Y', strtotime($toDate)) }}
                    </h4>
                </div>
            </div>
            @endif

            @if($individualTotal)
                <hr>
                <h4><u><strong>Total Individual Quantity of Selected Products</strong></u></h4>
                <div class="row">
                    <div class="col-12">
                        <table class="table">
                            <tr>
                                <th>Product</th>
                                <th>Department</th>
                                <th>Total Quantity</th>
                            </tr>
                        @foreach ($individualTotal as $production)
                            <tr>
                                <td>{{$production->productName}}</td>
                                <td>{{$production->departmentName}}</td>
                                <td>{{$production->totalQuantity}}</td>
                            </tr>
                        @endforeach
                        </table>
                    </div>
                </div>
            @endif
            <div>
                
            </div>

            <h4><u><strong>All Quantity of @if($individualTotal) Selected @else All @endif Products</strong></u></h4>
            
            <div class="row">
                <div class="col-12">
                    <table class="table">
                        <tr>
                            <th>Product name</th>
                            <th>Department</th>
                            <th>Quantity</th>
                            <th>Date</th>
                            <th class="no-print">Delete</th>
                        </tr>
                    @foreach ($productions as $production)
                        <tr>
                            <td>{{ $production->product->name }}</td>
                            <td>{{ $production->product->department->name }}</td>
                            <td>
                                @if($production->purchaseUnit->type == 'darjan')
                                    @if($production->purchaseUnit->value % 12 == 0)
                                        {{ $production->purchaseUnit->value/12 . ' ' . $production->purchaseUnit->type }}
                                    @else
                                        {{ (int)($production->purchaseUnit->value / 12) > 0? 
                                            (string)((int)($production->purchaseUnit->value / 12)) . ' ' . $production->purchaseUnit->type . ' ' 
                                            .$production->purchaseUnit->value % 12 . ' ' . $production->purchaseUnit->name . '(' . (int)$production->purchaseUnit->value . ')': 
                                            $production->purchaseUnit->value % 12 . ' ' . $production->purchaseUnit->name }}
                                    @endif
                                @else
                                    {{ $production->purchaseUnit->value . ' ' . $production->purchaseUnit->type }}
                                @endif
                            </td>
                            <td>{{ date('d M Y, h:i A', strtotime($production->created_at)) }}</td>
                            <td class="no-print"><button class="btn btn-danger" onclick="deleteProduction({{ $production->id }})">Delete</button></td>
                        </tr>
                    @endforeach
                    </table>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12 d-flex justify-content-center">
                {{ $productions->links() }}
            </div>
        </div>
    </div>

    <script type="application/javascript">
        function deleteProduction(id)
        {
            var _delete = confirm('Delete this production?');

            if(_delete)
            {
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: "{{ url('/production/') }}/" + id,
                    type: 'DELETE',
                    success: function(response){
                        if(response == 'deleted')
                        {
                            alert("Deleted.");
                            window.location.href = "{{ url('/productions') }}";
                        }
                        else
                        {
                            console.log(response);
                        }
                    },
                    error: function (jqXHR, status, err) {
                        console.log(jqXHR);
                    }
                });
            }
        }
        $(document).ready(function(){
            $("#selectedItems").select2();
            $("#selectedDepartments").select2();
        });
    </script>
@endsection