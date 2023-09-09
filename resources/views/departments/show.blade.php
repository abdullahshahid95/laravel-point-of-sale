@extends('master')
@section('content')
    <div class="container">
        <form action="{{ url('/department/') . '/' . $department->id }}/sales/filter" method="GET">
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
                    <label for="selectedItems">Item(s)</label>
                    <select id="selectedItems" class="form-control selectedItems" name="selectedItems[]" multiple="multiple">
                        @foreach ($products as $product)
                            <option value="{{ $product->id }}" @if(in_array($product->id, $selectedItems)) selected @endif>{{ $product->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-12">
                    <div>
                        <input type="radio" id="witoutReturn" name="return" value="1" @if($return == '1') checked @endif>
                        <label for="witoutReturn">Without Return</label>
                        |
                        <input type="radio" id="onlyReturn" name="return" value="2" @if($return == '2') checked @endif>
                        <label for="onlyReturn">Only Return</label>
                    </div>
                    <small>(Keep unselected for Sales with Return.)</small>
                </div>
            </div>

            <div class="row pt-2 pb-2">
                <div class="col-1">
                    <button type="submit" class="btn btn-primary">Search</button>
                </div>
                <div class="col-4">
                    <a href="{{ url('/department/') . '/' . $department->id }}" class="btn btn-danger">Reset</a>
                </div>
            </div>
        </form>

        {{--  --}}
        <div id="section-to-print">
            <div class="row pb-2">
                <div class="col-2">
                    <button class="btn btn-primary no-print" id="print-btn" onclick="window.print()">Print</button>
                </div>
                @if($fromDate)
                <div class="col-2">
                    From : {{ $fromDate }}
                </div>
                <div class="col-2">
                    To : {{ $toDate }}
                </div>
                @endif
                @if($total)
                <div class="col-3">
                    Total Sale: {{ $total['totalSale'] }}
                </div>
                @endif
            </div>

            @if($individualTotal)
                <hr>
                <h4><u><strong>Total Individual Sale of Selected Products</strong></u></h4>
                <div class="row">
                    <div class="col-12">
                        <table class="table">
                            <tr>
                                <th>Item</th>
                                <th>Total Sale</th>
                            </tr>
                        @foreach ($individualTotal as $sale)
                            <tr>
                                <td>{{$sale->product->name}}</td>
                                <td>{{$sale->totalSale}}</td>
                            </tr>
                        @endforeach
                        </table>
                    </div>
                </div>
            @endif
            
            <div>
                
            </div>
            <hr>
            <h2 class="text-center">{{ $department->name }}</h2>
            <hr>
            <h4><u><strong>All Sale of @if($individualTotal) Selected @else All @endif Products</strong></u></h4>
            <div class="row">
                <div class="col-12">
                    <table class="table">
                        <tr>
                            <th>Product name</th>
                            <th>Quantity</th>
                            <th>Price</th>
                            <th></th>
                            <th>Date / Time</th>
                            <th class="no-print">Return</th>
                        </tr>
                    @foreach ($sales as $sale)
                        <tr>
                            <td>{{ $sale->product->name }}</td>
                            <td>
                                @if($sale->unit->type == 'darjan')
                                    @if($sale->unit->value % 12 == 0)
                                        {{ $sale->unit->value/12 . ' ' . $sale->unit->type }}
                                    @else
                                        {{ (int)($sale->unit->value / 12) > 0? 
                                            (string)((int)($sale->unit->value / 12)) . ' ' . $sale->unit->type . ' ' 
                                            .$sale->unit->value % 12 . ' ' . $sale->product->name . '(' . (int)$sale->unit->value . ')': 
                                            $sale->unit->value % 12 . ' ' . $sale->product->name }}
                                    @endif
                                @else
                                    {{ $sale->unit->value . ' ' . $sale->unit->type }}
                                @endif
                            </td>
                            <td>{{ $sale->price }}</td>
                            <td></td>
                            <td>
                            @php
                                $new_datetime = DateTime::createFromFormat ( "Y-m-d H:i:s", $sale->created_at);
                                echo $new_datetime->format('d-m-y l, h:i A');
                            @endphp
                            </td>
                            <td class="no-print">
                                @if($sale->status == 1)
                                    <a href="#return" class="btn btn-danger" rel="modal:open">Return</a>
                                    <div id="return" class="modal">
                                        <div class="row">
                                            <div class="form-group col-12">
                                                <div class="alert alert-danger">Return this sale? Please check below if sale should not be added to inventory.</div>
                                                <label for="deduct{{$sale->id}}"><strong>Do not add to inventory</strong></label>
                                                <input type="checkbox" id="deduct{{$sale->id}}"/>
                                                <br>
                                                <a href="#" onclick="returnSale({{$sale->id}})" rel="modal:close" class="btn btn-danger submit">Submit</a>
                                            </div>
                                        </div>
                                    </div>
                                @elseif($sale->status == 2)
                                    Returned
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </table>
                </div>
            </div>
        </div>
        {{--  --}}
        <div class="row">
            <div class="col-12 d-flex justify-content-center">
                @if($fromDate && $toDate)
                    {{ $sales->appends(['fromDate' => $fromDate, 'toDate' => $toDate, 'selectedItems' => $selectedItems])->links() }}
                @elseif($fromDate && $toDate && $return)
                    {{ $sales->appends(['fromDate' => $fromDate, 'toDate' => $toDate, 'return' => $return, 'selectedItems' => $selectedItems])->links() }}
                @else
                    {{ $sales->links() }}
                @endif
            </div>
        </div>
    </div>

    <script type="application/javascript">
        function returnSale(id)
        {
            var deduct = 0;
            if($("#deduct" + id).prop("checked") == true)
                deduct = 1;
            
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{ url('/sale/') }}/" + id,
                data: {deduct: deduct},
                type: 'PUT',
                success: function(response){
                    if(response == 'updated')
                    {
                        alert("Sale Returned.");
                        window.location.href = '/sales';
                    }
                    else
                        console.log(response);
                },
                error: function(response){
                    console.log(response);
                }
            }); 
        }
        $(document).ready(function(){
            $(".selectedItems").select2();
            $(".selectedCustomers").select2();
        });
    </script>
@endsection