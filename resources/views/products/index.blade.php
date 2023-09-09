@extends('master')

@section('content')
    <div class="container">
        <div class="row pb-3">
            <div class="col-10">
                <button class="btn btn-primary no-print" onclick="window.print()">Print</button>
            </div>
            <div class="col-2">
                <a href="/product/create" class="btn btn-primary offset-5">Add Item</a>
            </div>
        </div>

        <div id="section-to-print">
            <div class="row">
                <div class="col-12">
                    <h2>Items</h2>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <table class="table" id="table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Department<th>
                                <th>Unit</th>
                                <th class="no-print">Delete</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($products as $product)
                            <tr>
                                <td>{{ $product->name }}</td>
                                <td>{{ $product->department->name }}</td>
                                <td></td>
                                <td>{{ $product->unit }}</td>
                                <td class="no-print"><button class="btn btn-danger" onclick="deleteProduct({{ $product->id }})">Delete</button></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script type="application/javascript">
        function deleteProduct(id)
        {
            var _delete = confirm('Delete this product?');

            if(_delete)
            {
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: '/product/' + id,
                    type: 'DELETE',
                    success: function(response){
                        console.log(response);
                        if(response == 'deleted')
                        {
                            window.location.href = '/products';
                        }
                        else
                        {
                            console.log(response);
                        }
                    },
                    error: function (jqXHR, status, err) {
                        console.log(jqXHR);
                    }
                });
            }
        }

        $(document).ready(function() {
            $('#table').DataTable();
            
            $("#table_length").addClass("no-print");
            $("#table_filter").addClass("no-print");
            $("#table_info").addClass("no-print");
            $("#table_paginate").addClass("no-print");
        });
    </script>
@endsection