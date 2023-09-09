@extends('master')
@section('content')

<div class="container pos-window">    
    <form action="{{ url('/sales-report/filter') }}" method="GET">
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
                <a href="{{ url('/sales-report') }}" class="btn btn-danger">Reset</a>
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
                            <th width="20%">Total Sale</th>
                            <th>{{ $total->totalSale ?? 0 }}</th>
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
                <h4><u><strong>Total Individual Sale of Selected @if(sizeof($selectedCategories) > 0){{'Categories'}}@else{{'Items'}}@endif </strong></u></h4>
                <div class="row">
                    <div class="col-12">
                        <table class="table table-bordered bg-white">
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
                                    <td>{{$sale->item_name}}</td>
                                    <td>{{$sale->totalSale}}</td>
                                </tr>
                            @endforeach
                        @endif
                        </table>
                    </div>
                </div>
            @endif
        </div>
        
    @if(sizeof($sales) > 0)
        <h4 class="no-print"><u><strong>All Sale of @if($individualTotal) Selected @else All @endif Items</strong></u></h4>
        <div class="row no-print">
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
    @endif
    </div>
    
    @if(sizeof($sales) > 0)
    <div class="row">
        <div class="col-12 d-flex justify-content-center">
            @if($fromDate && $toDate && $selectedItems)
                {{ $sales->appends(['fromDate' => $fromDate, 'toDate' => $toDate, 'selectedItems' => $selectedItems])->links() }}
            @elseif($fromDate && $toDate && $selectedCategories)
                {{ $sales->appends(['fromDate' => $fromDate, 'toDate' => $toDate, 'selectedCategories' => $selectedCategories])->links() }}
            @elseif($fromDate && $toDate && $selectedItems && $status)
                {{ $sales->appends(['fromDate' => $fromDate, 'toDate' => $toDate, 'status' => $status, 'selectedItems' => $selectedItems])->links() }}
            @elseif($fromDate && $toDate && $selectedCategories && $status)
                {{ $sales->appends(['fromDate' => $fromDate, 'toDate' => $toDate, 'status' => $status, 'selectedCategories' => $selectedCategories])->links() }}
            @elseif($fromDate && $toDate && $status)
                {{ $sales->appends(['fromDate' => $fromDate, 'toDate' => $toDate, 'status' => $status])->links() }}
            @else
                {{ $sales->links() }}
            @endif
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
                filename: 'Item Wise Sale',
                // filename: 'id',
                bootstrap: true,
                exportButtons: true,
                position: 'top',
                ignoreRows: null,
                ignoreCols: null,
                trimWhitespace: true,
                RTL: false,
                sheetname: 'Item Wise Sale'
                // sheetname: 'id'
        });

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