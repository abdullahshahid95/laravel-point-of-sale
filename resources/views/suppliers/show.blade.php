@extends('master')

@section('content')
    <div class="container">
        <form action="{{ url('/supplier/') . '/' . $supplier->id }}/filter-orders" method="GET">
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
            </div>
            <div class="row pt-2 pb-2">
                <div class="col-1">
                    <button type="submit" class="btn btn-primary">Search</button>
                </div>
                <div class="col-4">
                    <a href="{{ url('/supplier/') . '/' . $supplier->id }}" class="btn btn-danger">Reset</a>
                </div>
            </div>
        </form>
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
                <div class="col-2">
                    Total Purchase: {{ $total['totalOrders'] }}
                    <br>
                    <small>Without Paying: {{ $total['totalOrders'] + $total['totalBalance'] }}</small>
                </div>
                <div class="col-2">
                    Total Payable: {{ $total['totalBalance'] }}
                </div>
            @endif
            </div>

            @if($supplier->name != 'Stop-supplier')
                <div class="row">
                    <div class="col-12">
                        <table class="table">
                            <tr>
                                <th>Name</th>
                                <th>Phone</th>
                                <th>Address</th>
                            </tr>
                            <tr>
                                <td>{{ $supplier->name }}</td>
                                <td>{{ $supplier->phone ?? '--' }}</td>
                                <td>{{ $supplier->address ?? '--' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            @endif
            @if($total)
            <div class="row">
                <div class="col-3">
                    Total Sale: {{ $total['totalOrders'] }}
                </div>
            </div>
        @endif

            <div class="row">
                <div class="col-12">
                    <table class="table">
                        <tr>
                            <th>Receipt Number</th>
                            <th>Total</th>
                            <th>Discount</th>
                            <th>Sub Total<th>
                            <th>Date / Time</th>
                        </tr>
                    @foreach ($orders as $order)
                        <tr>
                            <td><a href="{{ url('/order/') . '/' . $order->id }}">{{ $order->receipt_number }}</a></td>
                            <td>{{ $order->total }}</td>
                            <td>{{ $order->discount }}</td>
                            <td>{{ $order->sub_total }}</td>
                            <td></td>
                            <td>
                            @php
                                $new_datetime = DateTime::createFromFormat ( "Y-m-d H:i:s", $order->created_at);
                                echo $new_datetime->format('d-m-y l, h:i A');
                            @endphp
                            </td>
                        </tr>
                    @endforeach
                    </table>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12 d-flex justify-content-center">
                @if($fromDate && $toDate)
                    {{ $orders->appends(['fromDate' => $fromDate, 'toDate' => $toDate])->links() }}
                @else
                    {{ $orders->links() }}
                @endif
            </div>
        </div>
    </div>

    <script type="application/javascript">

    </script>
@endsection