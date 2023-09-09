@extends('master')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-12">
                <table id="table" class="table">
                    <thead>
                        <tr>
                            <th>Item name</th>
                            <th>Deducted Quantity</th>
                            <th>Undo</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($wastage as $waste)
                        <tr>
                            <td>{{ $waste->item->name }}</td>
                            <td>
                                @if($waste->item->unit_id == 1)
                                {{ $waste->quantity }} kg
    
                                @elseif($waste->item->unit_id == 3)
                                {{ $waste->quantity }}
    
                                @elseif($waste->item->unit_id == 2)
                                {{ $waste->quantity . '(' . ((int)($waste->quantity / 12)) . ' Dozen' . ($waste->quantity % 12 > 0? ' ' . $waste->quantity % 12: '') . ')'}}
                                @endif
                            </td>
                            <td><button class="btn btn-danger" onclick="undoDeduction({{ $waste->id }})">Undo</button></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="row">
            <div class="col-12 d-flex justify-content-center">
                {{ $wastage->links() }}
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
                    url: "{{ url('/raw-waste/') }}/" + id,
                    type: 'PUT',
                    success: function(response){
                        if(response == 'changed')
                            window.location.href = "{{ url('/raw-waste') }}";
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
            $("#table").DataTable();
        });
    </script>
@endsection