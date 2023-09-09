@extends('master')

@section('content')
    <div class="container">
        <form action="{{ url('/purchase-orders-report/filter') }}" method="GET">
            <div class="row">
                <div class="col-4">
                    <label for="fromDate" class="col-md-4 col-form-label text-md-right">From</label>
                    <input id="fromDate" type="date" class="form-control @error('fromDate') is-invalid @enderror" name="fromDate" value="@if($fromDate){{$fromDate}}@endif" autocomplete="fromDate">                                    
                    
                    @error('fromDate')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="col-4">
                    <label for="toDate" class="col-md-4 col-form-label text-md-right">To</label>
                    <input id="toDate" type="date" class="form-control @error('toDate') is-invalid @enderror" name="toDate" value="@if($toDate){{$toDate}}@endif" autocomplete="toDate">

                    @error('toDate')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="col-4">
                    <label for="selectedSuppliers">Supplier</label>
                    <select id="selectedSuppliers" class="form-control selectedSuppliers" name="selectedSuppliers[]" multiple="multiple">
                        @foreach ($suppliers as $supplier)
                            <option value="{{ $supplier->id }}" @if(in_array($supplier->id, $selectedSuppliers)) selected @endif>{{ $supplier->name }}</option>
                        @endforeach
                    </select>

                    @error('selectedSuppliers')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <div class="col-4">
                    <label for="receipt_number" class="col-md-4 col-form-label text-md-right">Receipt#</label>
                    <input id="receipt_number" type="text" class="form-control @error('receipt_number') is-invalid @enderror" name="receipt_number" value="@if($receiptNumber){{$receiptNumber}}@endif" autocomplete="receipt_number">

                    @error('receipt_number')
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
            <!-- <div class="row">
                <div class="col-4">
                    <input id="fromTime" type="time" class="form-control @error('fromTime') is-invalid @enderror" name="fromTime" value="{{ old('fromTime') }}" autocomplete="fromTime">                                    
                    
                    @error('fromTime')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="col-4">
                    <input id="toTime" type="time" class="form-control @error('toTime') is-invalid @enderror" name="toTime" value="{{ old('toTime') }}" autocomplete="toTime">

                    @error('toTime')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div> -->
            <div class="row pt-2 pb-2">
                <div class="col-1">
                    <button type="submit" class="btn btn-primary">Search</button>
                </div>
                <div class="col-4">
                    <a href="{{ url('/purchase-orders-report') }}" class="btn btn-danger">Reset</a>
                </div>
            </div>
        </form>

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
                                <th width="30%">Total Purchase</th>
                                <th>{{ $total['totalOrders'] }}</th>
                            </tr>
                            <tr>
                                <th width="30%">Without Payable</th>
                                <th>{{ $total['totalOrders'] - $total['totalBalance'] }}</th>
                            </tr>
                            <tr>
                                <th width="30%">Total Payable</th>
                                <th>{{ $total['totalBalance'] }}</th>
                            </tr>
                            <tr style="display: none;">
                                <th></th>
                                <th></th>
                            </tr>
                        </table>
                    </div>
                </div>
                @endif
            </div>
        @if(sizeof($orders) > 0)
            <div class="row">
                <div class="col-12">
                    <table class="table">
                        <tr>
                            <th>Receipt Number</th>
                            <th>Supplier</th>
                            <th>Total</th>
                            <th>Paid Amount</th>
                            <th>Payable</th>
                            <th>Order Date</th>
                            <th>Receiving Date</th>
                            <th>Status</th>
                            {{-- <th class="no-print">Return</th>
                            <th>Delete</th> --}}
                        </tr>
                    @foreach ($orders as $order)
                        <tr>
                            <td><a href="{{ url('/purchase-order/') . '/' . $order->id }}">{{ $order->receipt_number }}</a></td>
                            <td>{{ $order->supplier->name }}</td>
                            <td>{{ $order->total }}</td>
                            <td>{{ $order->payment }}</td>
                            <td>
                                {{ $order->balance }}
                            </td>
                            <td>
                                @php
                                    $new_datetime = DateTime::createFromFormat ( "Y-m-d H:i:s", $order->created_at);
                                    echo $new_datetime->format('d-m-y l, h:i A');
                                @endphp
                            </td>
                            <td>
                                @php
                                    $new_datetime = DateTime::createFromFormat ( "Y-m-d H:i:s", $order->receiving_date);
                                    echo $new_datetime->format('d-m-y l, h:i A');
                                @endphp
                            </td>
                            <td id="status{{ $order->id }}">
                                @if($order->status == 1)
                                <span class="badge badge-warning">Not received</span>
                                @elseif($order->status == 2)
                                <span class="badge badge-success">Received</span>
                                @endif
                            </td>
                            {{-- <td class="no-print">
                                @if($order->status == 1)
                                <a href="#" class="btn btn-danger" onclick="openReturnDialogue({{ $order->id }})">Return</a>
                                @elseif($order->status == 2)
                                    Returned
                                @endif
                            </td>
                            <td>
                                <a href="#" class="btn btn-danger" onclick="deleteOrder({{ $order->id }})">Delete</a>
                            </td> --}}
                        </tr>
                    @endforeach
                    </table>
                </div>
            </div>
        @endif
        </div>
    @if(sizeof($orders) > 0)
        <div class="row">
            <div class="col-12 d-flex justify-content-center">
                {{ $orders->appends(['fromDate' => $fromDate, 'toDate' => $toDate, 'receiptNumber' => $receiptNumber, 'status' => $status , 'selectedSuppliers' => $selectedSuppliers])->links() }}
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
                filename: 'Supplier Wise Purchase',
                // filename: 'id',
                bootstrap: true,
                exportButtons: true,
                position: 'top',
                ignoreRows: null,
                ignoreCols: null,
                trimWhitespace: true,
                RTL: false,
                sheetname: 'Supplier Wise Purchase'
                // sheetname: 'id'
            });

            $("#selectedSuppliers").select2();
        });
    </script>
@endsection