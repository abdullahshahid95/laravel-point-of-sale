@extends('master')
@section('content')
    <div id="spinner" class=""></div>
    <div id="overlay" class=""></div>
    <div class="container">
        <div class="row">
            <div class="col-12">
                <button class="btn btn-primary no-print" id="print-btn" onclick="pirntBill({{ $order->id }})">Print</button>
            </div>
        </div>
        <div class="customer-invoice" id="section-to-print">
            <div class="row">
                <div class="col-12">
                    {{-- <p class="text-center h3 font-weight-bold border-bottom">
                        <img src="{{'/uploads/' . posConfigurations()->logo }}" width="50" class="figure-img img-fluid rounded">
                    </p> --}}
                    <p class="text-center h3 font-weight-bold">
                        {{ posConfigurations()->title }}
                    </p>
                    @if(posConfigurations()->contact)
                    <p class="text-center h6">
                        Contact# <span class="font-weight-bold">{{ posConfigurations()->contact }}</span><br>
                    </p>
                    @endif
                    @if(posConfigurations()->address)
                    <p class="text-center h6  border-bottom">
                        {{ posConfigurations()->address }}
                    </p>
                    @endif
                </div>
            </div>
            <div class="row">
                <div class="col-6">
                    <p class="text-left h6">{{ date("h:i A d-m-Y", strtotime($order->created_at)) }}</p>
                </div>
                <div class="col-6">
                    <p class="text-right h6">Invoice No #{{ $order->receipt_number }}</p>
                </div>
            </div>
            @if($order->customer_id != 1)
            <div class="row">
                <div class="col-12">
                    <p class="text-left">Customer: <span class="h6">{{ $order->customer->name }}</span></p>
                </div>
            </div>
            @endif
            <div class="row">
                <div class="col-12">
                    <table class="table invoice-table">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th>Qty.</th>
                                <th>Price</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($sales as $sale)
                            <tr>
                                <td>{{ $sale->item->name }}</td>
                                <td>
                                    @if($sale->item->unit_id == 1)
                                    {{ $sale->quantity }} kg
    
                                    @elseif($sale->item->unit_id == 3)
                                    {{ $sale->quantity }}
    
                                    @elseif($sale->item->unit_id == 2)
                                    {{ $sale->quantity . '(' . ((int)($sale->quantity / 12)) . ' Dozen' . ($sale->quantity % 12 > 0? ' ' . $sale->quantity % 12: '') . ')' }}
                                    @endif
                                </td>
                                <td>
                                    {{ $sale->unit_price }}
                                </td>
                                <td>{{ $sale->price }} @if($sale->discount_amount > 0)<span class="badge badge-success">{{'(Disc. ' . $sale->discount_amount . ')' }}</span>@endif</td>
                            </tr>
                            @endforeach
                            <tr>
                                <td></td>
                                <td></td>
                                <td><strong>Sub Total</strong></td>
                                <td><strong>{{ $order->total }}</strong></td>
                            </tr>
                            @if($order->discount_amount > 0)
                            <tr>
                                <td></td>
                                <td></td>
                                <td><strong>Discount</strong></td>
                                <td><strong>{{ $order->discount_amount }}</strong></td>
                            </tr>
                            @endif
                            <tr>
                                <td></td>
                                <td></td>
                                <td><strong>Total</strong></td>
                                <td><strong>{{ $order->sub_total }}</strong></td>
                            </tr>
                            <tr>
                                <td></td>
                                <td></td>
                                <td><strong>Payment</strong></td>
                                <td><strong>{{ $order->payment }}</strong></td>
                            </tr>
                            @if($order->balance > 0)
                            <tr>
                                <td></td>
                                <td></td>
                                <td><strong>Balance</strong></td>
                                <td><strong>{{ $order->balance }}</strong></td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <p class="h6">
                        User: {{ $order->user->name }}
                    </p>
                </div>
            </div>
            <hr>
            <p class="text-center" style="font-size: 10px; font-weight: normal;">
                Powered by: Bantach Applications <br>
                @if(posConfigurations()->footer_number)
                    {{ posConfigurations()->footer_number }}
                @endif
                <br><br>
            </p>
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

    @if(true)
    @endif

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
                url: "{{ url('/sale/return/') }}/" + id,
                data: {deduct: deduct},
                type: 'PUT',
                success: function(response){
                    if(response == 'updated')
                    {
                        alert("Sale Returned.");
                        location.reload();
                    }
                    else
                        console.log(response);
                },
                error: function(response){
                    console.log(response);
                }
            }); 
        }

        function pirntBill(id)
        {
            $("#overlay").addClass("overlay");
            $("#spinner").addClass("spinner");

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{ url('/order/print/') }}/" + id,
                type: 'GET',
                success: function(response){
                    if(response == 1)
                    {
                        $("#overlay").removeClass("overlay");
                        $("#spinner").removeClass("spinner");
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

            var print = "{!! $print !!}";

            if(print == 1)
            {
                window.print();
            }
        });
    </script>
@endsection