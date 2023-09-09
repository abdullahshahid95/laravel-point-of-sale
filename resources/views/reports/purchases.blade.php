@extends('master')
@section('content')

<div class="container pos-window">    
    <form action="{{ url('/purchases-report/filter') }}" method="GET">
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

            <div class="col-2">
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
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-12">
                <div>
                    <input type="radio" id="both" name="status" value="3" @if($status == 3) checked @endif>
                    <label for="both">All</label>
                    |
                    <input type="radio" id="received" name="status" value="2" @if($status == 2) checked @endif>
                    <label for="received">Received</label>
                    |
                    <input type="radio" id="not-received" name="status" value="1" @if($status == 1) checked @endif>
                    <label for="not-received">Not Received</label>
                </div>
            </div>
        </div>

        <div class="row pt-2 pb-2">
            <div class="col-1">
                <button type="submit" class="btn btn-primary">Search</button>
            </div>
            <div class="col-4">
                <a href="{{ url('/purchases-report') }}" class="btn btn-danger">Reset</a>
            </div>
        </div>
    </form>

    {{--  --}}
    <div id="section-to-print">
        <div class="row pb-2">
            <div class="col-2">
                <button class="btn btn-primary no-print" id="print-btn" onclick="window.print()">Print</button>
            </div>
        </div>
        <div id="section-to-export">
            @if($fromDate)
            <div class="row pb-2">
                <div class="col-12">
                    <table class="table table-bordered w-50 bg-white">
                        <tr>
                            <th width="10%">From</th>
                            <th>{{ $fromDate }}</th>
                            <th width="10%">To</th>
                            <th>{{ $toDate }}</th>
                        </tr>
                        <tr style="display: none;">
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                        </tr>
                    </table>
                </div>
            </div>
            @endif
            @if($total)
            <div class="row pb-2">
                <div class="col-12">
                    <table class="table table-bordered w-50 bg-white">
                        <tr>
                            <th width="25%">Total Purchase</th>
                            <th>{{ $total->totalPurchase ?? 0 }}</th>
                        </tr>
                        <tr style="display: none;">
                            <th></th>
                            <th></th>
                        </tr>
                    </table>
                </div>
            </div>
            @endif

            @if($individualTotal)
                <hr>
                <h4><u><strong>Total Individual Purchase of Selected @if(sizeof($selectedCategories) > 0){{'Categories'}}@else{{'Items'}}@endif </strong></u></h4>
                <div class="row">
                    <div class="col-12">
                        <table class="table table-bordered bg-white">
                            <tr>
                                <th>@if(sizeof($selectedCategories) > 0){{'Categories'}}@else{{'Items'}}@endif</th>
                                <th>Total Purchase</th>
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
                                    <td>{{$purchase->item_name}}</td>
                                    <td>{{$purchase->totalPurchase}}</td>
                                </tr>
                            @endforeach
                        @endif
                        </table>
                    </div>
                </div>
            @endif
        </div>
    @if(sizeof($purchases) > 0)
        <h4 class="no-print"><u><strong>All Purchase of @if($individualTotal) Selected @else All @endif Items</strong></u></h4>
        <div class="row no-print">
            <div class="col-12">
                <table class="table">
                    <tr>
                        <th>Item name</th>
                        <th>Quantity</th>
                        <th>Total Purchase Price</th>
                        <th>Unit Purchase Price</th>
                        <th></th>
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
                        <td></td>
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
    @endif
    </div>
    
    @if(sizeof($purchases) > 0)
    <div class="row">
        <div class="col-12 d-flex justify-content-center">
            {{ $purchases->appends(['fromDate' => $fromDate, 'toDate' => $toDate, 'status' => $status, 'selectedItems' => $selectedItems, 'selectedCategories' => $selectedCategories])->links() }}
        </div>
    </div>
    @endif
</div>

<script type="application/javascript">
    $(document).ready(function(){
        $("#section-to-export").tableExport({
                headers: true,
                footers: true,
                formats: ['csv', 'xls'],
                // formats: ['xls'],
                filename: 'Item Wise Purchase',
                // filename: 'id',
                bootstrap: true,
                exportButtons: true,
                position: 'top',
                ignoreRows: null,
                ignoreCols: null,
                trimWhitespace: true,
                RTL: false,
                sheetname: 'Item Wise Purchase'
                // sheetname: 'id'
        });

        $(".selectedCategories").select2({
            closeOnSelect: false
        });
        $(".selectedItems").select2({
            closeOnSelect: false
        });
        $(".selectedSuppliers").select2({
            closeOnSelect: false
        });
    });
</script>
@endsection