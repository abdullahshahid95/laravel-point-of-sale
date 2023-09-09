@extends('master')
@section('content')

<div class="container pos-window">    
    <form action="{{ url('/purchase/filter') }}" method="GET">
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

        <div class="row pt-2 pb-2">
            <div class="col-1">
                <button type="submit" class="btn btn-primary">Search</button>
            </div>
            <div class="col-4">
                <a href="{{ url('/purchases') }}" class="btn btn-danger">Reset</a>
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
                Total Purchase: {{ $total->totalPurchase ?? 0 }}
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
                        @foreach ($individualTotal as $purchase)
                            <tr>
                                <td>{{$purchase->category_name}}</td>
                                <td>{{$purchase->totalPurchase}}</td>
                            </tr>
                        @endforeach
                    @else
                        @foreach ($individualTotal as $purchase)
                            <tr>
                                <td>{{$purchase->item->name}}</td>
                                <td>{{$purchase->totalPurchase}}</td>
                            </tr>
                        @endforeach
                    @endif
                    </table>
                </div>
            </div>
        @endif
        
        <h4><u><strong>All Purchase of @if($individualTotal) Selected @else All @endif Items</strong></u></h4>
        <div class="row">
            <div class="col-12">
                <table class="table">
                    <tr>
                        <th>Item name</th>
                        <th>Quantity</th>
                        <th>Total Purchase Price</th>
                        <th>Unit Purchase Price</th>
                        <th>Date / Time</th>
                    </tr>
                @foreach ($purchases as $purchase)
                    <tr>
                        <td>{{ $purchase->item_name }}</td>
                        <td>
                            @if($purchase->item_unit_id == 1)
                            {{ $purchase->quantity }} kg

                            @elseif($purchase->item_unit_id == 3)
                            {{ $purchase->quantity }}

                            @elseif($purchase->item_unit_id == 2)
                            {{ $purchase->quantity . '(' . ((int)($purchase->quantity / 12)) . ' Dozen' . ($purchase->quantity % 12 > 0? ' ' . $purchase->quantity % 12: '') . ')' }}
                            @endif
                        </td>
                        <td>{{ $purchase->price }}</td>
                        <td>
                            {{ $purchase->unit_cost }}
                        </td>
                        <td>
                        @php
                            $new_datetime = DateTime::createFromFormat ( "Y-m-d H:i:s", $purchase->created_at);
                            echo $new_datetime->format('d-m-y l, h:i A');
                        @endphp
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
            @if($fromDate && $toDate && $selectedItems)
                {{ $purchases->appends(['fromDate' => $fromDate, 'toDate' => $toDate, 'selectedItems' => $selectedItems])->links() }}
            @elseif($fromDate && $toDate && $selectedCategories)
                {{ $purchases->appends(['fromDate' => $fromDate, 'toDate' => $toDate, 'selectedCategories' => $selectedCategories])->links() }}
            @elseif($fromDate && $toDate && $selectedItems && $return)
                {{ $purchases->appends(['fromDate' => $fromDate, 'toDate' => $toDate, 'return' => $return, 'selectedItems' => $selectedItems])->links() }}
            @elseif($fromDate && $toDate && $selectedCategories && $return)
                {{ $purchases->appends(['fromDate' => $fromDate, 'toDate' => $toDate, 'return' => $return, 'selectedCategories' => $selectedCategories])->links() }}
                @else
                {{ $purchases->links() }}
            @endif
        </div>
    </div>
</div>

<script type="application/javascript">
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