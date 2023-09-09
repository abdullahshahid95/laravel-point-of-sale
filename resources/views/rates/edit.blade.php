@extends('master')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-12">
                <form action="{{ url('/rates') }}" enctype="multipart/form-data" method="POST">
                    @csrf
                    @method('PUT')
                    <table class="table" id="rates">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th>Sale price</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($rates as $rate)
                            <tr>
                                <td>{{ $rate->item->name }}</td>
                                <td>
                                    <input type="hidden" value="{{ $rate->item->id }}" name="item_id[]">
                                    <input type="number" class="form-control @error('sale_price') is-invalid @enderror" 
                                            name="sale_price[]"
                                            value="{{ $rate->sale_price }}"
                                                    required autocomplete="sale_price">
                                    @if($rate->item->unit_id == 1)
                                        per kg
                                    @else
                                        per piece
                                    @endif
                    
                                    @error('sale_price')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="form-group row">
                        <div class="col-md-4"></div>
                        <div class="col-md-6">
                            <button class="btn btn-primary float-right" type="submit">Update</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script type="application/javascript">
        $(document).ready(function(){
            // $("#rates").DataTable({
            //     "paging": false,
            // });
        });
    </script>
@endsection