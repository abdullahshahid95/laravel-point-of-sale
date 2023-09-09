@extends('master')

@section('content')
<div id="spinner" class=""></div>
<div id="overlay" class=""></div>
    <div class="container">
        <div class="row pb-3">
            <div class="col-12">
                <button class="btn btn-primary" onclick="window.print()">Print</button>
            </div>
        </div>
        <div id="section-to-print">
            <div class="row pb-3">
                <div class="col-12">
                    <h3>Stock</h3>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <table class="table" id="table">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th>SKU</th>
                                <th>Quantity</th>
                                <th>Cost</th>
                                @if(allowed(7, 'edit'))
                                <th class="no-print">Add</th>
                                @endif
                                @if(allowed(7, 'edit'))
                                <th class="no-print">Waste</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($inventory as $item)
                            @if($item->item->status == 1)
                            <tr>
                                <td>{{ $item->item->name }}</td>
                                <td>{{ $item->item->sku }}</td>
                                <td>
                                    @if($item->item->unit_id == 1)
                                    {{ $item->quantity }} kg
    
                                    @elseif($item->item->unit_id == 3)
                                    {{ $item->quantity }}
    
                                    @elseif($item->item->unit_id == 2)
                                    {{ $item->quantity . '(' . ((int)($item->quantity / 12)) . ' Dozen' . ($item->quantity % 12 > 0? ' ' . $item->quantity % 12: '') . ')'}}
                                    @endif
                                </td>
                                <td>{{ $item->cost }}</td>
                                @if(allowed(7, 'edit'))
                                <td class="no-print"><button class="btn btn-primary edit-inventory" data-id="{{ $item->id }}" data-name="{{ $item->item->name }}" data-quantity="{{ $item->quantity }}" onclick="openUpdateQuantityDialogue({{ $item->id }}, '{{ $item->item->name }}', {{ $item->item->unit_id }}, {{ $item->quantity }}, 1)">+</button></td>
                                @endif
                                @if(allowed(7, 'edit'))
                                <td class="no-print"><button class="btn btn-primary edit-inventory" data-id="{{ $item->id }}" data-name="{{ $item->item->name }}" data-quantity="{{ $item->quantity }}" onclick="openUpdateQuantityDialogue({{ $item->id }}, '{{ $item->item->name }}', {{ $item->item->unit_id }}, {{ $item->quantity }}, 0)">-</button></td>
                                @endif
                            </tr>
                            @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- The Modal -->
            <div id="subtract-inventory-modal" class="subtract-inventory-modal">
                <!-- Modal content -->
                <div class="subtract-inventory-modal-content">
                    <div class="row bg-dark">
                        <div class="col-12" style="color: #ffffff;">
                            <span id="heading-text">Add to Waste</span>
                            <span id="subtract-inventory-modal-close" class="subtract-inventory-modal-close" onclick="closeSubtractDialogue()">&times;</span>
                        </div>
                    </div>
                    <div class="row">
                        <div id="inventory-update-text" class="col-12">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <label for="quantity">Quantity</label>
                            <div id="quantity-container">

                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-8">
                            <label for=""></label><br>
                            <button id="update-quantity-btn" type="button" class="btn btn-primary" onclick="updateQuantity()">Update</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script type="application/javascript">

        var outerItemId = 0;
        var outerQuantity = 0;
        function openUpdateQuantityDialogue(itemId, itemName, unitId, quantity, flag) 
        {
            outerItemId = itemId;
            outerQuantity = quantity;

            var modal = document.getElementById("subtract-inventory-modal");
            modal.style.display = "block";

            var toAppend = "";

            var maxQuantity = flag == 1? 10000000: quantity;
            if(unitId == 1)
            {
                toAppend = `<input type="number" min="1" max="` + maxQuantity + `" id="quantity" class="form-control col-3" name="quantity" required>
                            <strong class="ml-1">kg</strong>
                            <button type="button" class="btn btn-primary ml-1" onclick="add1Pao()">+ 1 Pao</button>
                            <button type="button" class="btn btn-primary ml-1" onclick="subtract1Pao()">- 1 Pao</button>`;
            }
            else if(unitId == 2)
            {
                toAppend = `<input type="number" min="1" max="` + maxQuantity + `" step="1"
                                        onkeypress="return event.charCode >= 48 && event.charCode <= 57" 
                                        id="quantity" class="form-control col-3" name="quantity" required>
                            <strong id="dozen-quantity" class="ml-1"></strong>
                            <button type="button" class="btn btn-primary ml-1" onclick="add1Dozen()">+ 1 Dozen</button>
                            <button type="button" class="btn btn-primary ml-1" onclick="subtract1Dozen()">- 1 Dozen</button>
                            <button type="button" class="btn btn-primary ml-1" onclick="add1Piece()">+ 1</button>
                            <button type="button" class="btn btn-primary ml-1" onclick="subtract1Piece()">- 1</button>`;   
            }
            else if(unitId == 3)
            {
                toAppend = `<input type="number" min="1" max="` + maxQuantity + `" step="1"
                                        onkeypress="return event.charCode >= 48 && event.charCode <= 57" 
                                        id="quantity" class="form-control col-3" name="quantity" required>
                            <button type="button" class="btn btn-primary ml-1" onclick="add1Piece()">+ 1</button>
                            <button type="button" class="btn btn-primary ml-1" onclick="subtract1Piece()">- 1</button>`;
            }

            if(flag == 1)
            {
                $("#heading-text").text('Add inventory');
            }
            else
            {
                $("#heading-text").text('Add to waste');
            }
            $("#inventory-update-text").empty().append("Selected Item: " + itemName +"<br>Current quantity: " + quantity + "<br>Enter quantity to " + (flag == 1? "add.": "deduct."));

            document.getElementById("update-quantity-btn").setAttribute("data-flag", flag);

            $("#quantity-container").empty().append(toAppend);
        }

        function closeSubtractDialogue()
        {
            var modal = document.getElementById("subtract-inventory-modal");
            modal.style.display = "none";
        }

        function updateQuantity()
        {
            var inputQuantity = parseFloat(document.getElementById("quantity").value);

            var flag = document.getElementById("update-quantity-btn").getAttribute("data-flag");

            if(flag == 1)
            {
                if(inputQuantity > 0)
                {
                    $("#overlay").addClass("overlay");
                    $("#spinner").addClass("spinner");

                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        url: "{{ url('/raw-inventory/') }}/" + outerItemId + '/' + flag,
                        data: {quantity: inputQuantity},
                        type: 'PUT',
                        success: function(response){
                            if(response == 'edited')
                            {
                                $("#overlay").removeClass("overlay");
                                $("#spinner").removeClass("spinner");

                                alert('Quantity updated.');
                                window.location.href = "{{ url('/raw-inventory') }}";
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
            else
            {
                if(inputQuantity <= outerQuantity)
                {
                    $("#overlay").addClass("overlay");
                    $("#spinner").addClass("spinner");
                    
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        url: "{{ url('/raw-inventory/') }}/" + outerItemId + '/' + flag,
                        data: {quantity: inputQuantity},
                        type: 'PUT',
                        success: function(response){
                            if(response == 'edited')
                            {
                                $("#overlay").removeClass("overlay");
                                $("#spinner").removeClass("spinner");
                                
                                alert('Quantity updated.');
                                window.location.href = "{{ url('/raw-inventory') }}";
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
        }

        function add1Pao()
        {
            var quantity = $("#quantity").val() != ''? parseFloat($("#quantity").val()): 0;
            $("#quantity").val(quantity + 0.25);
        }

        function subtract1Pao()
        {
            var quantity = $("#quantity").val() != ''? parseFloat($("#quantity").val()): 0;
            if(quantity - 0.25 >= 0)
            {
                $("#quantity").val(quantity - 0.25);
            }
        }

        function add1Dozen()
        {
            var quantity = $("#quantity").val() != ''? parseFloat($("#quantity").val()): 0;
            $("#quantity").val(quantity + 12);

            var dozenQuantity = 0;
            $("#dozen-quantity").html('');
            if(parseFloat($("#quantity").val()) >= 12)
            {
                quantity = $("#quantity").val() != ''? parseFloat($("#quantity").val()): 0;
                dozenQuantity = parseInt(quantity / 12) + ' Dozen' + (quantity % 12 > 0? ' ' + quantity % 12: '');
                console.log(dozenQuantity);
                $("#dozen-quantity").html(dozenQuantity);
            }
        }

        function subtract1Dozen()
        {
            var quantity = $("#quantity").val() != ''? parseFloat($("#quantity").val()): 0;
            if(quantity - 12 >= 0)
            {
                $("#quantity").val(quantity - 12);
            }

            var dozenQuantity = 0;
            $("#dozen-quantity").html('');
            if(parseFloat($("#quantity").val()) >= 12)
            {
                quantity = $("#quantity").val() != ''? parseFloat($("#quantity").val()): 0;
                dozenQuantity = parseInt(quantity / 12) + ' Dozen' + (quantity % 12 > 0? ' ' + quantity % 12: '');
                console.log(dozenQuantity);
                $("#dozen-quantity").html(dozenQuantity);
            }
        }

        function add1Piece()
        {
            var quantity = $("#quantity").val() != ''? parseInt($("#quantity").val()): 0;
            $("#quantity").val(quantity + 1);

            var dozenQuantity = 0;
            $("#dozen-quantity").html('');
            if(parseFloat($("#quantity").val()) >= 12)
            {
                quantity = $("#quantity").val() != ''? parseFloat($("#quantity").val()): 0;
                dozenQuantity = parseInt(quantity / 12) + ' Dozen' + (quantity % 12 > 0? ' ' + quantity % 12: '');
                console.log(dozenQuantity);
                $("#dozen-quantity").html(dozenQuantity);
            }
        }

        function subtract1Piece()
        {
            var quantity = $("#quantity").val() != ''? parseInt($("#quantity").val()): 0;
            if(quantity - 1 >= 0)
            {
                $("#quantity").val(quantity - 1);
            }

            var dozenQuantity = 0;
            $("#dozen-quantity").html('');
            if(parseFloat($("#quantity").val()) >= 12)
            {
                quantity = $("#quantity").val() != ''? parseFloat($("#quantity").val()): 0;
                dozenQuantity = parseInt(quantity / 12) + ' Dozen' + (quantity % 12 > 0? ' ' + quantity % 12: '');
                console.log(dozenQuantity);
                $("#dozen-quantity").html(dozenQuantity);
            }
        }

        $(document).ready(function(){
            $("#table").DataTable({
            "lengthMenu": [ [10, 25, 50, -1], [10, 25, 50, "All"] ]
            });

            $("#table_length").addClass("no-print");
            $("#table_filter").addClass("no-print");
            $("#table_info").addClass("no-print");
            $("#table_paginate").addClass("no-print");
        });
    </script>
@endsection