@extends('master')

@section('content')
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
                                <th>Quantity</th>
                                <th class="no-print">Edit</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($inventory as $product)
                            <tr>
                                <td>{{ $product->product->name }}</td>
                                <td>
                                    @if($product->product->unit == 'darjan')
                                        @if($product->quantity % 12 == 0)
                                            {{ $product->quantity/12 . ' ' . $product->product->unit }}
                                        @else
                                            {{ (int)($product->quantity / 12) > 0? 
                                                (string)((int)($product->quantity / 12)) . ' ' . $product->product->unit . ' ' 
                                                .$product->quantity % 12 . ' ' . $product->product->name . '(' . (int)$product->quantity . ')': 
                                                $product->quantity % 12 . ' ' . $product->product->name }}
                                        @endif
                                    @else
                                        {{ $product->quantity . ' ' . $product->product->unit }}
                                    @endif
                                </td>
                                <td class="no-print"><button class="btn btn-primary edit-inventory" data-id="{{ $product->id }}" data-name="{{ $product->product->name }}" data-quantity="{{ $product->quantity }}">Edit</button></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script type="application/javascript">
        $(document).ready(function(){
            $("#table").DataTable();

            $("#table_length").addClass("no-print");
            $("#table_filter").addClass("no-print");
            $("#table_info").addClass("no-print");
            $("#table_paginate").addClass("no-print");

            $(".edit-inventory").on('click', function(){
                var id = $(this).data('id');
                var item = $(this).data('name');
                var quantity = $(this).data('quantity');

                let toDeduct = prompt("Selected Item: " + item +"\nCurrent quantity: " + quantity + "\nEnter quantity to deduct.");

                if(toDeduct != null)
                {
                    if(toDeduct == '' || toDeduct.length <= 0 || isNaN(toDeduct) || parseFloat(toDeduct) > parseFloat(quantity))
                    {
                        alert('Please enter valide quantity.');
                    }
                    else
                    {
                        $.ajax({
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            url: "{{ url('/inventory/') }}/" + id,
                            data: {quantity: toDeduct},
                            type: 'PUT',
                            success: function(response){
                                if(response == 'edited')
                                {
                                    alert('Quantity chnaged.');
                                    window.location.href = "{{ url('/inventory') }}";
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
            });
        });
    </script>
@endsection