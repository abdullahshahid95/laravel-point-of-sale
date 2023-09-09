@extends('master')
@section('content')
    <div id="spinner" class=""></div>
    <div id="overlay" class=""></div>
    <div class="container">
        <div class="row">
            <div class="col-12">
                <button class="btn btn-primary no-print" id="print-btn" onclick="window.print()">Print</button>
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
                    <p class="text-left h6">{{ date("h:i A d-m-Y", strtotime($purchaseOrder->created_at)) }}</p>
                </div>
                <div class="col-6">
                    <p class="text-right h6">Invoice No #{{ $purchaseOrder->receipt_number }}</p>
                </div>
            </div>
            @if($purchaseOrder->supplier_id != 1)
            <div class="row">
                <div class="col-12">
                    <p class="text-left">Supplier: <span class="h6">{{ $purchaseOrder->supplier->name }}</span></p>
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
                                <th class="no-print">Delete</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($purchases as $purchase)
                            <tr>
                                <td>{{ $purchase->item->name }}</td>
                                <td>
                                    @if($purchase->item->unit_id == 1)
                                    {{ $purchase->quantity }} kg
    
                                    @elseif($purchase->item->unit_id == 3)
                                    {{ $purchase->quantity }}
    
                                    @elseif($purchase->item->unit_id == 2)
                                    {{ $purchase->quantity . '(' . ((int)($purchase->quantity / 12)) . ' Dozen' . ($purchase->quantity % 12 > 0? ' ' . $purchase->quantity % 12: '') . ')' }}
                                    @endif
                                </td>
                                <td>
                                    {{ $purchase->unit_cost }}
                                </td>
                                <td>{{ $purchase->price }}</td>
                                <td class="no-print">
                                    <a href="#" class="btn btn-danger mt-1 mb-2" onclick="deletePurchase({{ $purchase->id }}, {{ sizeof($purchase->purchaseOrder->purchases) }})">Delete</a>
                                    {{-- @if($purchase->status == 1)
                                    <a href="#" class="btn btn-danger" onclick="openReturnDialogue({{ $purchase->id }})">Return</a>
                                    @elseif($purchase->status == 2)
                                        Returned
                                    @endif --}}
                                </td>
                            </tr>
                            @endforeach

                            <tr>
                                <td></td>
                                <td></td>
                                <td>Total</td>
                                <td>{{ $purchaseOrder->total }}</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td></td>
                                <td></td>
                                <td>Payment</td>
                                <td>{{ $purchaseOrder->payment }}</td>
                                <td></td>
                            </tr>
                            @if($purchaseOrder->balance > 0)
                            <tr>
                                <td></td>
                                <td></td>
                                <td>Balance</td>
                                <td>{{ $purchaseOrder->balance }}</td>
                                <td></td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <p class="h6">
                        User: {{ $purchaseOrder->user->name }}
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
                    Return Purchase
                    <span id="return-sale-modal-close" class="return-sale-modal-close" onclick="closeReturnDialogue()">&times;</span>
                </div>
            </div>
            <div class="row">
                <div class="form-group col-12">
                    Return this purchase? Please check below if purchase should not be subtracted from inventory.
                    <br>
                    <label for="deduct"><strong>Do not subtract from inventory</strong></label>
                    <input type="checkbox" id="deduct"/>
                    <br>
                    <a href="#" id="return-sale-submit-btn" onclick="returnPurchase()" class="btn btn-danger submit">Submit</a>
                </div>
            </div>
        </div>
    </div>

    @if(true)
    @endif

    <script type="application/javascript">
        function deletePurchase(id, purchaseCount)
        {
            var _delete = confirm('Delete this item from Purchase Order?');

            if(_delete)
            {
                $("#overlay").addClass("overlay");
                $("#spinner").addClass("spinner");

                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: "{{ url('/purchase') }}/" + id,
                    type: 'DELETE',
                    success: function(response){
                        if(response == 1)
                        {
                            $("#overlay").removeClass("overlay");
                            $("#spinner").removeClass("spinner");

                            alert('Deleted.');

                            // document.getElementById("order-row" + id).remove();

                            if(purchaseCount == 1)
                            {
                                window.location.href = "{{ url('/purchase-orders') }}";
                            }
                            else
                            {
                                location.reload();
                            }
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

        function returnPurchase()
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
                url: "{{ url('/purchase/return/') }}/" + id,
                data: {deduct: deduct},
                type: 'PUT',
                success: function(response){
                    if(response == 'updated')
                    {
                        alert("Purchase Returned.");
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