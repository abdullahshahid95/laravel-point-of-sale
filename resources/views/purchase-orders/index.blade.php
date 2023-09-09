@extends('master')

@section('content')
    <div id="spinner" class=""></div>
    <div id="overlay" class=""></div>
    <div class="container">
        <form action="{{ url('/purchase-order/filter') }}" method="GET">
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
                    <a href="{{ url('/purchase-orders') }}" class="btn btn-danger">Reset</a>
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
                    Total Purchase: {{ $total['totalOrders'] }}
                    <br>
                    <small>Without Paying: {{ $total['totalOrders'] - $total['totalBalance'] }}</small>
                </div>
                <div class="col-2">
                    Total Payable: {{ $total['totalBalance'] }}
                </div>
            @endif --}}
            </div>
        
            <div class="row">
                <div class="col-12">
                    <table class="table table-hover">
                        <tr>
                            <th>Receipt Number</th>
                            <th>Supplier</th>
                            <th>Total</th>
                            <th>Paid Amount</th>
                            <th>Payable</th>
                            <th>Order Date</th>
                            <th>Receiving Date</th>
                            <th>Status</th>
                            <th>Delete</th>
                        </tr>
                    @foreach ($orders as $order)
                        <tr id="order-row{{ $order->id }}">
                            <td><a href="{{ url('/purchase-order/') . '/' . $order->id }}">{{ $order->receipt_number }}</a></td>
                            <td>{{ $order->supplier->name }}</td>
                            <td id="total{{ $order->id }}">{{ $order->total }}</td>
                            <td id="paid{{ $order->id }}">{{ $order->payment }}</td>
                            <td>
                                <span id="payable{{ $order->id }}">{{ $order->total - $order->payment }}</span>
                                @if($order->total - $order->payment > 0)
                                <br>
                                    <a href="#" id="paybutton{{ $order->id }}" class="btn btn-primary no-print" onclick="openUpdatePaymentDialogue({{ $order->id }}, {{ $order->payment }}, {{ $order->balance }})">Pay</a>
                                @endif
                                {{-- {{ $order->balance }}
                                @if($order->balance > 0)
                                    <a href="#update" class="btn btn-primary no-print" rel="modal:open">Update</a>
                                    <div id="update" class="modal">
                                        <form action="{{ url('/purchase-order/update-balance') . '/' . $order->id }}" method="POST">
                                            @csrf
                                            @method("PUT")
                                            
                                            <div class="row">
                                                <div class="form-group col-8">
                                                    <label for="balance{{$order->id}}"><strong>Enter amount</strong></label>
                                                    <input id="balance{{$order->id}}" class="form-control" name="payment" type="number" min="1" max="{{ $order->balance }}" required/>
                                                    <br>
                                                    <button type="submit" rel="modal:close" class="btn btn-warning submit">Submit</a>
                                                </div>
                                                <div class="col-4">
                                                    Payable: {{ $order->balance }}
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                @endif --}}
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
                                <button type="button" class="btn btn-warning badge p-2" onclick="updateStatus({{ $order->id }})" title="Click to Receive">Not received</button>
                                @elseif($order->status == 2)
                                <span class="badge badge-success">Received</span>
                                @endif
                            </td>
                            <td>
                                <a href="#" class="btn btn-danger" onclick="deleteOrder({{ $order->id }})">Delete</a>
                            </td>
                        </tr>
                    @endforeach
                    </table>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12 d-flex justify-content-center">
                {{ $orders->appends(['fromDate' => $fromDate, 'toDate' => $toDate, 'selectedSuppliers' => $selectedSuppliers, 'receiptNumber' => $receiptNumber, 'status' => $status])->links() }}
            </div>
        </div>
    </div>

    <!-- The Modal -->
    <div id="update-payment-modal" class="update-payment-modal">
        <!-- Modal content -->
        <div class="update-payment-modal-content">
            <div class="row">
                <div class="col-12">
                    Make Payment
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
                    Payable: <span id="payable"></span>
                </div>
            </div>
        </div>
    </div>

    <script type="application/javascript">
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
                    url: "{{ url('/purchase-order/') }}/" + id,
                    type: 'DELETE',
                    success: function(response){
                        if(response == 1)
                        {
                            $("#overlay").removeClass("overlay");
                            $("#spinner").removeClass("spinner");

                            alert('Deleted.');

                            document.getElementById("order-row" + id).remove();
                            // window.location.href = "{{ url('/purchase-orders') }}";
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
            document.getElementById("payment").setAttribute("max", parseFloat(document.getElementById("payable" + id).innerHTML));
            document.getElementById("payable").innerHTML = document.getElementById("payable" + id).innerHTML;
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
            var payable = parseFloat(document.getElementById("payable" + id).innerHTML);
            var alreadyPaid = parseFloat(document.getElementById("paid" + id).innerHTML);
            var total = parseFloat(document.getElementById("total" + id).innerHTML);

            if(payment > 0 && payment <= payable)
            {
                $("#overlay").addClass("overlay");
                $("#spinner").addClass("spinner");

                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: "{{ url('/purchase-order/update-balance/') }}/" + id,
                    data: {payment: payment},
                    type: 'PUT',
                    success: function(response){
                        if(response == 1)
                        {
                            $("#overlay").removeClass("overlay");
                            $("#spinner").removeClass("spinner");

                            document.getElementById("paid" + id).innerHTML = alreadyPaid + payment;

                            if(payment == payable)
                            {
                                document.getElementById("payable" + id).innerHTML = 0;
                                document.getElementById("paybutton" + id).remove();
                            }
                            else
                            {
                                document.getElementById("payable" + id).innerHTML = total - (alreadyPaid + payment);
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
            var markCollected = confirm('Mark order as Received?');

            if(markCollected)
            {
                $("#overlay").addClass("overlay");
                $("#spinner").addClass("spinner");

                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: "{{ url('/purchase-order/receive') }}/" + id,
                    type: 'PUT',
                    success: function(response){
                        if(response == 1)
                        {
                            $("#overlay").removeClass("overlay");
                            $("#spinner").removeClass("spinner");

                            alert('Status updated');
                            document.getElementById("status" + id).innerHTML = `<span class="badge badge-success">Received</span>`;
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
            $("#selectedSuppliers").select2({
                closeOnSelect: false
            });
        });
    </script>
@endsection