@extends('master')

@section('content')
    <div class="jumbotron" style="padding-left: 3%; padding-right: 3%;">
        <form action="{{ url('/specific-price') }}" enctype="multipart/form-data" method="POST">
            @csrf
            <input type="hidden" id="rowsCount" name="rows_count" value="1">
            <div class="row">
                <h2>Add Price</h2>
            </div>
            <div class="row">
                <div class="col-12" style="padding-left: 0; padding-right: 0;">
                    <div class="row">
                        <div class="col-12" style="padding-left: 0; padding-right: 0;">
                            <table class="table table-bordered table-hover add-item-table">
                                <thead>
                                    <th>Customer</th>
                                    <th>Item</th>
                                    <th>Normal Price</th>
                                    <th>Customer Price</th>
                                    <th>Remove</th>
                                </thead>
                                <tbody id="item-table-body">
                                @foreach ($customerItems as $csp)
                                    <tr>
                                        @php
                                            $key++;
                                        @endphp
                                        <td width="20%" class="pl-2 pr-2">
                                            <input type="hidden" name="customer_id_{{ $key }}" value="{{ $csp['customer_id'] }}">
                                            {{ $csp['customer_name'] }}
                                        </td>
                                        <td width="20%" class="pl-2 pr-2">
                                            <input type="hidden" name="item_id_{{ $key }}" value="{{ $csp['item_id'] }}">
                                            {{ $csp['item_name'] }}
                                        </td>
                                        <td width="20%" class="pl-2 pr-2">
                                            {{ $csp['normal_price'] }}
                                        </td>
                                        <td width="20%" class="pl-2 pr-2">
                                            <input type="number" min="0" class="form-control" name="sale_price_{{ $key }}" step="0.01">
                                        </td>
                                        <td width="20%" class="pl-2 pr-2">
                                            <button class="btn btn-danger float-right" type="button" onclick="removeRow(event)" style="width: 50%;">X</button>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">Save</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script type="text/javaScript">
        function removeRow(event)
        {
            if($("#item-table-body tr").length > 1)
            {
                $(event.target.parentElement.parentElement).remove();                
            }
        }

        $(document).ready(function(){

        });
    </script>
@endsection