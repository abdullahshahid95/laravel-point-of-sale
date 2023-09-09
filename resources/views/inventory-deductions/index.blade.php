@extends('master')

@section('content')
    <div class="container">
        <!-- <div class="row pb-3">
            <a href="/sale/create" class="btn btn-primary offset-10">Start selling</a>
        </div> -->
        <div class="row">
            <div class="col-12">
                <table class="table">
                    <tr>
                        <th>Item name</th>
                        <th>Deducted Quantity</th>
                        <th>Undo</th>
                    </tr>
                @foreach ($inventoryDeductions as $inventoryDeduction)
                    <tr>
                        <td>{{ $inventoryDeduction->item->name }}</td>
                        <td>{{ $inventoryDeduction->unit->name }}</td>
                        <td><button class="btn btn-danger" onclick="undoDeduction({{ $inventoryDeduction->id }})">Undo</button></td>
                    </tr>
                @endforeach
                </table>
            </div>
        </div>
        <div class="row">
            <div class="col-12 d-flex justify-content-center">
                {{ $inventoryDeductions->links() }}
            </div>
        </div>
    </div>

    <script type="application/javascript">
        function undoDeduction(id)
        {
            var _delete = confirm('Undo this deduction? It will be added to inventory.');

            if(_delete)
            {
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: "{{ url('/inventory-deduction/') }}/" + id,
                    type: 'PUT',
                    success: function(response){
                        if(response == 'changed')
                            window.location.href = "{{ url('/inventory-deduction') }}";
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

        });
    </script>
@endsection