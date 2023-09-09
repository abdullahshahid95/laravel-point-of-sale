@extends('master')

@section('content')
    <div class="container">
        <form action="{{ url('/earnings') }}" method="POST">
            @csrf
            
            <div class="row">
                <div class="col-12">
                    <div class="row">
                        <div class="col-5 pl-5">
                            <div class="form-group">
                                <label for="begin" class="col-md-4 col-form-label text-md-right">Start Date</label>
                                    <input id="begin" type="date" class="form-control @error('begin') is-invalid @enderror" name="begin" value="{{ old('begin') }}" required autocomplete="begin">                                    
                                    
                                    @error('begin')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                            </div>
                        </div>

                        <div class="col-5 pl-5">
                            <div class="form-group">
                                <label for="end" class="col-md-4 col-form-label text-md-right">End Date</label>
                                    <input id="end" type="date" class="form-control @error('end') is-invalid @enderror" name="end" value="{{ old('end') }}" required autocomplete="end">                                    
                                    
                                    @error('end')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                            </div>
                        </div>

                        <div class="col-10 pl-5">
                            <button class="btn btn-primary float-right" type="submit">Find</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <div class="row pb-2">
            <div class="col-12">
                <button class="btn btn-primary" onclick="window.print()">Print</button>
            </div>
        </div>
            @if($purchaseBetweenDates && $saleBetweenDates)
            <div id="section-to-print">
                <div class="row">
                    <div class="col-12">
                        <h5>From {{ date('d F Y', strtotime($interval['begin'])) }} to 
                            {{ date('d F Y', strtotime($interval['end'])) }}
                        </h5>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <table class="table table-hover table-striped">
                            <thead>
                                <tr>
                                    <th>Profit/Loss with Expenses</th>
                                    <th>Sale</th>
                                    <th>Expenses</th>
                                    <th>Purchase</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Sales - Purchases - Expenses = {{ $saleBetweenDates . ' - ' . $purchaseBetweenDates . '-' . $expenseBetweenDates . ' = ' . ($saleBetweenDates - $purchaseBetweenDates - $expenseBetweenDates) }}</td>
                                    <td>{{ $saleBetweenDates }}</td>
                                    <td>{{ $expenseBetweenDates }}</td>
                                    <td>{{ $purchaseBetweenDates }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @else
            <div class="row">
                No record found.
            </div>
            @endif
    </div>

    <script type="application/javascript">

    </script>
@endsection