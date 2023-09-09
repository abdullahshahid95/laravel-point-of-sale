@extends('master')

@section('content')
    <div id="spinner" class=""></div>
    <div id="overlay" class=""></div>
    <div class="jumbotron">
        <form action="{{ url('/order/filter') }}" method="GET">
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
                    <a href="{{ url('/orders') }}" class="btn btn-danger">Reset</a>
                </div>
            </div>
        </form>

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
                <div class="col-2">
                    Total Sale: {{ $total['totalOrders'] }}
                    <br>
                    <small>Without Receiving: {{ $total['totalOrders'] - $total['totalBalance'] }}</small>
                </div>
                <div class="col-2">
                    Total Discount: {{ $total['totalDiscount'] }}
                </div>
                <div class="col-2">
                    Total Receivable: {{ $total['totalBalance'] }}
                </div>
            @endif --}}
            </div>
        
            <div class="row">
                <div class="col-12">
                    <table class="table table-hover">
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
                            <th class="no-print">Delete</th>
                        </tr>
                    @foreach ($orders as $order)
                        <tr id="order-row{{ $order->id }}">
                            <td><a href="{{ url('/order/') . '/' . $order->id }}">{{ $order->receipt_number }}</a></td>
                            <td>{{ $order->customer->name }}</td>
                            <td>{{ $order->total }}</td>
                            <td>
                                {{ $order->discount_amount }}
                                @if($order->discount_type == 1 && $order->discount > 0)
                                <span class="badge badge-success">{{ $order->discount }}%</span>
                                @endif
                            </td>
                            <td id="subtotal{{ $order->id }}">{{ $order->sub_total }}</td>
                            <td id="received{{ $order->id }}">{{ $order->payment }}</td>
                            <td>
                                <span id="receiveable{{ $order->id }}">{{ $order->sub_total - $order->payment }}</span>
                                @if($order->sub_total - $order->payment > 0)
                                    <a href="#" id="receivebutton{{ $order->id }}" class="btn btn-primary no-print" onclick="openUpdatePaymentDialogue({{ $order->id }}, {{ $order->payment }}, {{ $order->balance }})">Receive</a>
                                @endif
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
                                <button type="button" class="btn btn-warning badge p-2" onclick="updateStatus({{ $order->id }})" title="Click to Collect">Not collected</button>
                                @elseif($order->status == 2)
                                <span class="badge badge-success">Collected</span>
                                @endif
                            </td>
                            <td id="type{{ $order->id }}">
                                @if($order->type == 1)
                                <span class="badge badge-success">Home Delivery</span>
                                @elseif($order->type == 2)
                                <span class="badge badge-success">Takeaway</span>
                                @elseif($order->type == 3)
                                <span class="badge badge-success">Dine-In</span>
                                @endif
                            </td>
                            <td class="no-print">
                                <a href="#" class="btn btn-danger" onclick="deleteOrder({{ $order->id }})">Delete</a>                                
                                {{-- @if($order->status == 2)
                                    -
                                @else
                                    <a href="#" class="btn btn-danger" onclick="deleteOrder({{ $order->id }})">Delete</a>
                                @endif --}}
                            </td>
                        </tr>
                    @endforeach
                    </table>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12 d-flex justify-content-center">
                {{ $orders->appends(['fromDate' => $fromDate, 'toDate' => $toDate, 'selectedCustomers' => $selectedCustomers, 'receipt_number' => $receiptNumber, 'status' => $status, 'paidUnpaid' => $paidUnpaid, 'typeTakeAway' => ($typeTakeAway == 0? null: $typeTakeAway), 'typeHomeDelivery' => ($typeHomeDelivery == 0? null: $typeHomeDelivery), 'typeDineIn' => ($typeDineIn == 0? null: $typeDineIn)])->links() }}
                {{-- @if($fromDate && $toDate)
                    {{ $orders->appends(['fromDate' => $fromDate, 'toDate' => $toDate])->links() }}
                @else
                    {{ $orders->links() }}
                @endif --}}
            </div>
        </div>
    </div>

    <!-- The Modal -->
    {{-- <div id="return-order-modal" class="return-order-modal">
        <!-- Modal content -->
        <div class="return-order-modal-content">
            <div class="row alert-danger">
                <div class="col-12" style="color: #761b18;">
                    Return Order
                    <span id="return-order-modal-close" class="return-order-modal-close" onclick="closeReturnDialogue()">&times;</span>
                </div>
            </div>
            <div class="row">
                <div class="form-group col-12">
                    Return this order? Please check below if items should not be added to inventory.
                    <br>
                    <label for="deduct"><strong>Do not add to inventory</strong></label>
                    <input type="checkbox" id="deduct"/>
                    <br>
                    <a href="#" id="return-order-submit-btn" onclick="returnOrder()" class="btn btn-danger submit">Submit</a>
                </div>
            </div>
        </div>
    </div> --}}

    <!-- The Modal -->
    <div id="update-payment-modal" class="update-payment-modal">
        <!-- Modal content -->
        <div class="update-payment-modal-content">
            <div class="row">
                <div class="col-12">
                    Receive Payment
                    <span id="update-payment-modal-close" class="update-payment-modal-close" onclick="closeUpdatePaymentnDialogue()">&times;</span>
                </div>
            </div>
            <div class="row">
                <div class="form-group col-12">
                    <label for="payment"><strong>Enter amount</strong></label>
                    <input id="payment" class="form-control" type="number" min="1"/>
                    <br>
                    <a href="#" id="update-payment-submit-btn" onclick="updatePayment()" class="btn btn-primary submit">Submit</a>
                </div>
                <div class="col-4">
                    Receiveable: <span id="receiveable"></span>
                </div>
            </div>
        </div>
    </div>

    <script type="application/javascript">
        function openReturnDialogue(id) 
        {
            var modal = document.getElementById("return-order-modal");
            modal.style.display = "block";

            document.getElementById("return-order-submit-btn").setAttribute("data-id", id);
        }

        function closeReturnDialogue()
        {
            var modal = document.getElementById("return-order-modal");
            modal.style.display = "none";
        }

        function returnOrder()
        {
            var id = document.getElementById("return-order-submit-btn").getAttribute("data-id");

            var deduct = 0;

            if($("#deduct").prop("checked") == true)
            {
                deduct = 1;
            }

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{ url('/order/return/') }}/" + id,
                data: {deduct: deduct},
                type: 'PUT',
                success: function(response){
                    if(response == 'updated')
                    {
                        alert("Order Returned.");
                        window.location.href = "{{ url('/orders') }}";
                    }
                    else
                        console.log(response);
                },
                error: function(response){
                    console.log(response);
                }
            }); 
        }

        function deleteOrder(id)
        {
            var _delete = confirm('Delete this order?');

            if(_delete)
            {
                $("#overlay").addClass("overlay");
                $("#spinner").addClass("spinner");

                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: "{{ url('/order/') }}/" + id,
                    type: 'DELETE',
                    success: function(response){
                        if(response == 1)
                        {
                            $("#overlay").removeClass("overlay");
                            $("#spinner").removeClass("spinner");

                            alert('Deleted.');

                            document.getElementById("order-row" + id).remove();
                            // window.location.href = "{{ url('/orders') }}";
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

        function openUpdatePaymentDialogue(id) 
        {
            var modal = document.getElementById("update-payment-modal");
            modal.style.display = "block";

            document.getElementById("update-payment-submit-btn").setAttribute("data-id", id);
            document.getElementById("payment").setAttribute("max", parseFloat(document.getElementById("receiveable" + id).innerHTML));
            document.getElementById("receiveable").innerHTML = document.getElementById("receiveable" + id).innerHTML;
        }

        function closeUpdatePaymentnDialogue()
        {
            document.getElementById("payment").value = '';
            var modal = document.getElementById("update-payment-modal");
            modal.style.display = "none";
        }

        function updatePayment()
        {
            var id = document.getElementById("update-payment-submit-btn").getAttribute("data-id");
            var payment = parseFloat(document.getElementById("payment").value);
            var receiveable = parseFloat(document.getElementById("receiveable" + id).innerHTML);
            var alreadyReceived = parseFloat(document.getElementById("received" + id).innerHTML);
            var subTotal = parseFloat(document.getElementById("subtotal" + id).innerHTML);

            if(payment > 0 && payment <= receiveable)
            {
                $("#overlay").addClass("overlay");
                $("#spinner").addClass("spinner");

                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: "{{ url('/order/update-balance/') }}/" + id,
                    data: {payment: payment},
                    type: 'PUT',
                    success: function(response){
                        if(response == 1)
                        {
                            $("#overlay").removeClass("overlay");
                            $("#spinner").removeClass("spinner");

                            document.getElementById("received" + id).innerHTML = alreadyReceived + payment;

                            if(payment == receiveable)
                            {
                                document.getElementById("receiveable" + id).innerHTML = 0;
                                document.getElementById("receivebutton" + id).remove();
                            }
                            else
                            {
                                document.getElementById("receiveable" + id).innerHTML = subTotal - (alreadyReceived + payment);
                            }

                            alert("Payment Updated.");

                            closeUpdatePaymentnDialogue();
                            // window.location.href = "{{ url('/orders') }}";
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

        function updateStatus(id)
        {
            var markCollected = confirm('Mark order as Collected?');

            if(markCollected)
            {
                $("#overlay").addClass("overlay");
                $("#spinner").addClass("spinner");

                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: "{{ url('/order/collect') }}/" + id,
                    type: 'PUT',
                    success: function(response){
                        if(response == 1)
                        {
                            $("#overlay").removeClass("overlay");
                            $("#spinner").removeClass("spinner");

                            alert('Status updated');
                            document.getElementById("status" + id).innerHTML = `<span class="badge badge-success">Collected</span>`;
                            // window.location.href = "{{ url('/orders') }}";
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
            $("#selectedCustomers").select2({
                closeOnSelect: false
            });
        });
    </script>
@endsection