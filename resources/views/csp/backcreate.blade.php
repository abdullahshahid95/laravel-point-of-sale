@extends('master')

@section('content')
    <div class="jumbotron" style="padding-left: 3%; padding-right: 3%;">
        <form action="{{ url('/specific-price/create-price') }}" enctype="multipart/form-data" method="POST">
            @csrf
            <input type="hidden" id="rowsCount" name="rows_count" value="1">
            <div class="row">
                <h2>Add CSP</h2>
            </div>
            <div class="row">
                <div class="col-12" style="padding-left: 0; padding-right: 0;">
                    <hr>
                    <div class="row mb-2">
                        <div class="col-12">
                            <button type="button" class="btn btn-primary" onclick="addRow()">Insert row</button>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12" style="padding-left: 0; padding-right: 0;">
                            <table class="table table-bordered table-hover add-item-table">
                                <thead>
                                    <th>Customer</th>
                                    <th>Items</th>
                                    <th>Remove</th>
                                </thead>
                                <tbody id="item-table-body">
                                    <tr id="row-1">
                                        <td width="40%" class="pl-2 pr-2">
                                            <select class="form-control customer-id" name="customer_id_1[]" multiple="multiple" required>
                                                <option value="">Customers</option>
                                                @foreach ($customers as $customer)
                                                <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td width="40%" class="pl-2 pr-2">
                                            <select class="form-control item-id" name="item_id_1[]" multiple="multiple" required>
                                                <option value="">Item</option>
                                                @foreach ($items as $item)
                                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td width="20%" class="pl-2 pr-2">
                                            <button class="btn btn-danger float-right" type="button" onclick="removeRow(event)" style="width: 50%;">X</button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">Next</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script type="text/javaScript">
        $(".customer-id").select2();
        $(".item-id").select2();
        var rowCount = 2;

        function addRow()
        {
            let row = `<tr id='row-` + rowCount + `'>
                    <td width="40%" class="pl-2 pr-2">
                        <select class="form-control customer-id" name="customer_id_` + rowCount + `[]" multiple="multiple" required>
                            <option value="">Customer</option>
                            @foreach ($customers as $customer)
                            <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td width="40%" class="pl-2 pr-2">
                        <select class="form-control item-id" name="item_id_` + rowCount + `[]" multiple="multiple" required>
                            <option value="">Item</option>
                            @foreach ($items as $item)
                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td width="20%" class="pl-2 pr-2">
                        <button class="btn btn-danger float-right" type="button" onclick="removeRow(event)" style="width: 50%;">X</button>
                    </td>
                </tr>`;

            $("#item-table-body").append(row);

            $(".customer-id").select2();
            $(".item-id").select2();

            document.getElementById("rowsCount").value = rowCount;

            rowCount++;
        }

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