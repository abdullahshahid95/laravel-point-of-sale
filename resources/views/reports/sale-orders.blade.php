@extends('master')

@section('content')
    <div class="container">
        <form action="{{ url('/sale-orders-report/filter') }}" method="GET">
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
                    <label for="selectedCustomers">Customer</label>
                    <select id="selectedCustomers" class="form-control selectedCustomers" name="selectedCustomers[]" multiple="multiple">
                        @foreach ($customers as $customer)
                            <option value="{{ $customer->id }}" @if(in_array($customer->id, $selectedCustomers)) selected @endif>{{ $customer->name }}</option>
                        @endforeach
                    </select>

                    @error('selectedCustomers')
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
                    <div class="d-inline-block bg-secondary text-white pl-1 pr-1">
                        <strong>Collection Status</strong> 
                        <br>
                        <input type="radio" id="both" name="status" value="3" @if($status == 3) checked @endif>
                        <label for="both">All</label>
                        |
                        <input type="radio" id="received" name="status" value="2" @if($status == 2) checked @endif>
                        <label for="received">Collected</label>
                        |
                        <input type="radio" id="not-received" name="status" value="1" @if($status == 1) checked @endif>
                        <label for="not-received">Not Collected</label>
                    </div>

                    <div class="d-inline-block ml-5 bg-secondary text-white pl-1 pr-1">
                        <strong>Order Type</strong> 
                        <br>
                        <input type="checkbox" id="take-away" name="typeTakeAway" @if($typeTakeAway) checked @endif>
                        <label for="take-away">Take Away</label>
                        |
                        <input type="checkbox" id="home-delivery" name="typeHomeDelivery" @if($typeHomeDelivery) checked @endif>
                        <label for="home-delivery">Home Delivery</label>
                        |
                        <input type="checkbox" id="dine-in" name="typeDineIn" @if($typeDineIn) checked @endif>
                        <label for="dine-in">Dine-In</label>
                    </div>

                    <div class="d-inline-block ml-5 bg-secondary text-white pl-1 pr-1">
                        <strong>Paid/Unpaid</strong> 
                        <br>
                        <input type="radio" id="paid-and-unpaid" name="paidUnpaid" value="3" @if($paidUnpaid == 3) checked @endif>
                        <label for="paid-and-unpaid">All</label>
                        |
                        <input type="radio" id="paid" name="paidUnpaid" value="2" @if($paidUnpaid == 2) checked @endif>
                        <label for="paid">Paid</label>
                        |
                        <input type="radio" id="unpaid" name="paidUnpaid" value="1" @if($paidUnpaid == 1) checked @endif>
                        <label for="unpaid">Unpaid</label>
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
                    <a href="{{ url('/sale-orders-report') }}" class="btn btn-danger">Reset</a>
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
                                <th width="30%">Total Sale</th>
                                <th>{{ $total['totalOrders'] }}</th>
                            </tr>
                            <tr>
                                <th width="30%">Total Discount</th>
                                <th>{{ $total['totalDiscount'] }}</th>
                            </tr>
                            <tr>
                                <th width="30%">Without Receivable</th>
                                <th>{{ $total['totalOrders'] - $total['totalBalance'] }}</th>
                            </tr>
                            <tr>
                                <th width="30%">Total Receivable</th>
                                <th>{{ $total['totalBalance'] }}</th>
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
            </div>
        @if(sizeof($orders) > 0)
            <div class="row">
                <div class="col-12">
                    <table class="table">
                        <tr>
                            <th>Receipt Number</th>
                            <th>Customer</th>
                            <th>Sub Total</th>
                            <th>Discount</th>
                            <th>Total</th>
                            <th>Received Amount</th>
                            <th>Receivable</th>
                            <th>Order Date</th>
                            <th>Collection Date</th>
                            <th>Status</th>
                            <th>Type</th>
                        </tr>
                    @foreach ($orders as $order)
                        <tr>
                            <td><a href="{{ url('/order/') . '/' . $order->id }}">{{ $order->receipt_number }}</a></td>
                            <td>{{ $order->customer->name }}</td>
                            <td>{{ $order->total }}</td>
                            <td>
                                {{ $order->discount_amount }}
                                @if($order->discount_type == 1 && $order->discount > 0)
                                <span class="badge badge-success">{{ $order->discount }}%</span>
                                @endif
                            </td>
                            <td>{{ $order->sub_total }}</td>
                            <td>{{ $order->payment }}</td>
                            <td>{{ $order->sub_total - $order->payment }}</td>
                            <td>
                                @php
                                    $new_datetime = DateTime::createFromFormat ( "Y-m-d H:i:s", $order->created_at);
                                    echo $new_datetime->format('d-m-y l, h:i A');
                                @endphp
                            </td>
                            <td>
                                @php
                                    $new_datetime = DateTime::createFromFormat ( "Y-m-d H:i:s", $order->receiving_date);
                                    echo $new_datetime->format('d-m-y l');
                                @endphp
                            </td>
                            <td>
                                @if($order->status == 1)
                                <span class="badge badge-warning">Not collected</span>
                                @elseif($order->status == 2)
                                <span class="badge badge-success">Collected</span>
                                @endif
                            </td>
                            <td>
                                @if($order->type == 1)
                                <span class="badge badge-success">Home Delivery</span>
                                @elseif($order->type == 2)
                                <span class="badge badge-success">Takeaway</span>
                                @elseif($order->type == 3)
                                <span class="badge badge-success">Dine-In</span>
                                @endif
                            </td>
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
                {{ $orders->appends(['fromDate' => $fromDate, 'toDate' => $toDate, 'selectedCustomers' => $selectedCustomers, 'status' => $status, 'receipt_number' => $receiptNumber,'paidUnpaid' => $paidUnpaid, 'typeTakeAway' => ($typeTakeAway == 0? null: $typeTakeAway), 'typeHomeDelivery' => ($typeHomeDelivery == 0? null: $typeHomeDelivery), 'typeDineIn' => ($typeDineIn == 0? null: $typeDineIn)])->links() }}
                {{-- @if($fromDate && $toDate)
                    {{ $orders->appends(['fromDate' => $fromDate, 'toDate' => $toDate, 'status' => $status])->links() }}
                @elseif($fromDate && $toDate && $selectedCustomers)
                    {{ $orders->appends(['fromDate' => $fromDate, 'toDate' => $toDate, 'selectedCustomers' => $selectedCustomers, 'status' => $status])->links() }}
                @elseif($fromDate && $toDate && $selectedCustomers)
                    {{ $orders->appends(['fromDate' => $fromDate, 'toDate' => $toDate, 'selectedCustomers' => $selectedCustomers, 'status' => $status, 'receipt_number' => $receiptNumber])->links() }}
                @else
                    {{ $orders->links() }}
                @endif --}}
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
                filename: 'Customer Wise Sale',
                // filename: 'id',
                bootstrap: true,
                exportButtons: true,
                position: 'top',
                ignoreRows: null,
                ignoreCols: null,
                trimWhitespace: true,
                RTL: false,
                sheetname: 'Customer Wise Sale'
                // sheetname: 'id'
        });

            $("#selectedCustomers").select2();
        });
    </script>
@endsection