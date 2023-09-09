@extends('master')

@section('content')
    <div class="container">
        <div class="row pb-3">
            <a href="{{ url('/rates/edit') }}" class="btn btn-secondary offset-10">Update list</a>
        </div>
        <div class="row">
            <div class="col-12">
                <table class="table" id="table">
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th>Sale price</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rates as $rate)
                        <tr>
                            <td>{{ $rate->item->name }}</td>
                            <td>
                                @if($rate->item->unit_id == 2)
                                    {{ round($rate->sale_price * 12) . ' /Dozen' }}  
                                @elseif($rate->item->unit_id == 1)
                                    {{ $rate->sale_price . ' /kg' }}
                                @else
                                    {{ $rate->sale_price . ' /piece' }}
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script type="application/javascript">
        $(document).ready(function(){
            $("#table").DataTable();
        });
    </script>
@endsection