@extends('master')
@section('content')

<div class="container pos-window">    
    <form action="{{ url('/sale/filter') }}" method="GET">
        <div class="row">
            <div class="col-2">
                <label for="fromDate">From</label>
                <input id="fromDate" type="date" class="form-control @error('fromDate') is-invalid @enderror" name="fromDate" value="@if($fromDate){{$fromDate}}@endif" autocomplete="fromDate">                                    
                
                @error('fromDate')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <div class="col-2">
                <label for="toDate">To</label>
                <input id="toDate" type="date" class="form-control @error('toDate') is-invalid @enderror" name="toDate" value="@if($toDate){{$toDate}}@endif" autocomplete="toDate">

                @error('toDate')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            {{-- <div class="col-2">
                <label for="selectedCategories">Categories</label>
                <select id="selectedCategories" class="form-control selectedCategories" name="selectedCategories[]" multiple="multiple">
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @if(in_array($category->id, $selectedCategories)) selected @endif>{{ $category->name }}</option>
                    @endforeach
                </select>
                @error('selectedCategories')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <small>Or</small>

            <div class="col-2">
                <label for="selectedItems">Item(s)</label>
                <select id="selectedItems" class="form-control selectedItems" name="selectedItems[]" multiple="multiple">
                    @foreach ($items as $item)
                        <option value="{{ $item->id }}" @if(in_array($item->id, $selectedItems)) selected @endif>{{ $item->name }}</option>
                    @endforeach
                </select>
                @error('selectedItems')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div> --}}
        </div>

        {{-- <div class="row mt-3">
            <div class="col-12">
                <div>
                    <input type="radio" id="witoutReturn" name="return" value="1" @if($return == '1') checked @endif>
                    <label for="witoutReturn">Without Return</label>
                    |
                    <input type="radio" id="onlyReturn" name="return" value="2" @if($return == '2') checked @endif>
                    <label for="onlyReturn">Only Return</label>
                </div>
            </div>
        </div> --}}

        <div class="row pt-2 pb-2">
            <div class="col-1">
                <button type="submit" class="btn btn-primary">Search</button>
            </div>
            <div class="col-4">
                <a href="{{ url('/sales') }}" class="btn btn-danger">Reset</a>
            </div>
        </div>
    </form>

    {{--  --}}
    <div id="section-to-print">
        <div class="row pb-2">
            <div class="col-2">
                <button class="btn btn-primary no-print" id="print-btn" onclick="window.print()">Print</button>
            </div>
            {{-- @if($fromDate)
            <div class="col-2">
                From : {{ $fromDate }}
            </div>
            <div class="col-2">
                To : {{ $toDate }}
            </div>
            @endif
            @if($total)
            <div class="col-3">
                Total Sale: {{ $total->totalSale ?? 0 }}
            </div>
            @endif --}}
        </div>

        @if($individualTotal)
            <hr>
            <h4><u><strong>Total Individual Sale of Selected @if(sizeof($selectedCategories) > 0){{'Categories'}}@else{{'Items'}}@endif </strong></u></h4>
            <div class="row">
                <div class="col-12">
                    <table class="table">
                        <tr>
                            <th>@if(sizeof($selectedCategories) > 0){{'Categories'}}@else{{'Items'}}@endif</th>
                            <th>Total Sale</th>
                        </tr>
                    @if(sizeof($selectedCategories) > 0)
                        @foreach ($individualTotal as $sale)
                            <tr>
                                <td>{{$sale->category_name}}</td>
                                <td>{{$sale->totalSale}}</td>
                            </tr>
                        @endforeach
                    @else
                        @foreach ($individualTotal as $sale)
                            <tr>
                                <td>{{$sale->item->name}}</td>
                                <td>{{$sale->totalSale}}</td>
                            </tr>
                        @endforeach
                    @endif
                    </table>
                </div>
            </div>
        @endif
        
        <h4><u><strong>All Sale of @if($individualTotal) Selected @else All @endif Items</strong></u></h4>
        <div>
            <div class="col-12">
                <table class="table">
                    <tr>
                        <th>Item name</th>
                        <th>Quantity</th>
                        <th>Total Sale Price</th>
                        <th>Unit Sale Price</th>
                        <th></th>
                        <th>Date / Time</th>
                        {{-- @if(posConfigurations()->maintain_inventory == 1)
                        <th class="no-print">Return</th>
                        @endif
                        <th class="no-print">Delete</th> --}}
                    </tr>
                @foreach ($sales as $sale)
                    <tr>
                        <td>{{ $sale->item_name }}</td>
                        <td>
                            @if($sale->item_unit_id == 1)
                            {{ $sale->quantity }} kg

                            @elseif($sale->item_unit_id == 3)
                            {{ $sale->quantity }}

                            @elseif($sale->item_unit_id == 2)
                            {{ $sale->quantity . '(' . ((int)($sale->quantity / 12)) . ' Dozen' . ($sale->quantity % 12 > 0? ' ' . $sale->quantity % 12: '') . ')' }}
                            @endif
                        </td>
                        <td>{{ $sale->price }}</td>
                        <td>
                            {{ $sale->unit_price }}
                        </td>
                        <td></td>
                        <td>
                        @php
                            $new_datetime = DateTime::createFromFormat ( "Y-m-d H:i:s", $sale->created_at);
                            echo $new_datetime->format('d-m-y l, h:i A');
                        @endphp
                        </td>
                        {{-- @if(posConfigurations()->maintain_inventory == 1)
                        <td class="no-print">
                            @if($sale->status == 1)
                                <a href="#" class="btn btn-danger" onclick="openReturnDialogue({{ $sale->id }})">Return</a>
                            @elseif($sale->status == 2)
                                Returned
                            @endif
                        </td>
                        @endif
                        <td>
                            <a href="#" class="btn btn-danger" onclick="deleteSale({{ $sale->id }})">Delete</a>
                        </td> --}}
                    </tr>
                @endforeach
                </table>
            </div>
        </div>
    </div>
    {{--  --}}
    <div class="row">
        <div class="col-12 d-flex justify-content-center">
            @if($fromDate && $toDate && $selectedItems)
                {{ $sales->appends(['fromDate' => $fromDate, 'toDate' => $toDate, 'selectedItems' => $selectedItems])->links() }}
            @elseif($fromDate && $toDate && $selectedCategories)
                {{ $sales->appends(['fromDate' => $fromDate, 'toDate' => $toDate, 'selectedCategories' => $selectedCategories])->links() }}
            @elseif($fromDate && $toDate && $selectedItems && $return)
                {{ $sales->appends(['fromDate' => $fromDate, 'toDate' => $toDate, 'return' => $return, 'selectedItems' => $selectedItems])->links() }}
            @elseif($fromDate && $toDate && $selectedCategories && $return)
                {{ $sales->appends(['fromDate' => $fromDate, 'toDate' => $toDate, 'return' => $return, 'selectedCategories' => $selectedCategories])->links() }}
            @elseif($fromDate && $toDate && $return)
                {{ $sales->appends(['fromDate' => $fromDate, 'toDate' => $toDate, 'return' => $return])->links() }}
            @else
                {{ $sales->links() }}
            @endif
        </div>
    </div>
</div>

<!-- The Modal -->
<div id="return-sale-modal" class="return-sale-modal">
    <!-- Modal content -->
    <div class="return-sale-modal-content">
        <div class="row alert-danger">
            <div class="col-12" style="color: #761b18;">
                Return Sale
                <span id="return-sale-modal-close" class="return-sale-modal-close" onclick="closeReturnDialogue()">&times;</span>
            </div>
        </div>
        <div class="row">
            <div class="form-group col-12">
                Return this sale? Please check below if sale should not be added to inventory.
                <br>
                <label for="deduct"><strong>Do not add to inventory</strong></label>
                <input type="checkbox" id="deduct"/>
                <br>
                <a href="#" id="return-sale-submit-btn" onclick="returnSale()" class="btn btn-danger submit">Submit</a>
            </div>
        </div>
    </div>
</div>

<script type="application/javascript">
    function openReturnDialogue(id) 
    {
        var modal = document.getElementById("return-sale-modal");
        modal.style.display = "block";

        document.getElementById("return-sale-submit-btn").setAttribute("data-id", id);
    }

    function closeReturnDialogue()
    {
        var modal = document.getElementById("return-sale-modal");
        modal.style.display = "none";
    }

    function returnSale()
    {
        var id = document.getElementById("return-sale-submit-btn").getAttribute("data-id");

        var deduct = 0;
        if($("#deduct").prop("checked") == true)
        {
            deduct = 1;
        }
        
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: "{{ url('/sale/return') }}/" + id,
            data: {deduct: deduct},
            type: 'PUT',
            success: function(response){
                if(response == 'updated')
                {
                    alert("Sale Returned.");
                    window.location.href = "{{ url('/sales') }}";
                }
                else
                    console.log(response);
            },
            error: function(response){
                console.log(response);
            }
        }); 
    }

    function deleteSale(id)
    {
        var _delete = confirm('Delete this sale?');

        if(_delete)
        {
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{ url('/sale') }}/" + id,
                type: 'DELETE',
                success: function(response){
                    if(response == 1)
                    {
                        alert('Deleted');
                        
                        window.location.href = "{{ url('/sales') }}";
                    }
                    else
                        console.log(response);
                },
                error: function(response){
                    console.log(response);
                }
            });
        }
    }

    $(document).ready(function(){
        $(".selectedCategories").select2({
            closeOnSelect: false
        });
        $(".selectedItems").select2({
            closeOnSelect: false
        });
        $(".selectedCustomers").select2({
            closeOnSelect: false
        });
    });
</script>
@endsection