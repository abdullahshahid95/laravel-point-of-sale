@extends('master')

@section('content')
    <div class="container">
        @if(allowed(3, 'make'))
        <div class="row pb-3">
            <a href="{{ url('/expense/create') }}" class="btn btn-primary offset-10">Add expense</a>
        </div>
        @endif
        <form action="{{ url('/expense/filter') }}" method="GET">
            <div class="row">
                <div class="col-4">
                    <label for="fromDate">From</label>
                    <input id="fromDate" type="date" class="form-control @error('fromDate') is-invalid @enderror" name="fromDate" value="@if($fromDate){{$fromDate}}@endif" autocomplete="fromDate">                                    
                    
                    @error('fromDate')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="col-4">
                    <label for="toDate">To</label>
                    <input id="toDate" type="date" class="form-control @error('toDate') is-invalid @enderror" name="toDate" value="@if($toDate){{$toDate}}@endif" autocomplete="toDate">

                    @error('toDate')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>

            <div class="row pt-2 pb-2">
                <div class="col-1">
                    <button type="submit" class="btn btn-primary">Search</button>
                </div>
                <div class="col-4">
                    <a href="{{ url('/expenses') }}" class="btn btn-danger">Reset</a>
                </div>
            </div>
        </form>
        

        <div id="section-to-print">
            <div class="row">
                <div class="col-12">
                    <h3><u>Expenses</u></h3>
                </div>
            </div>

            <div class="row pb-2">
                <div class="col-2">
                    <button class="btn btn-primary no-print" id="print-btn" onclick="window.print()">Print</button>
                </div>
                @if($fromDate)
                <div class="col-2">
                    From : {{ $fromDate }}
                </div>
                <div class="col-2">
                    To : {{ $toDate }}
                </div>
                @endif
            </div>
            <div class="row">
                <div class="col-12">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Cost</th>
                                {{-- <th>Spent ?</th> --}}
                                <th>Date</th>
                                @if(allowed(3, 'remove'))
                                <th class="no-print">Delete</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($expenses as $expense)
                                <tr>
                                    <td>{{ $expense->title }}</td>
                                    <td>{{ $expense->cost }}</td>
                                    {{-- <td>{{ $expense->spent }}</td> --}}
                                    <td>
                                        {{-- {{ $expense->date->format('d-m-y l') }} --}}
                                        @php
                                            $new_datetime = DateTime::createFromFormat ( "Y-m-d H:i:s", $expense->created_at);
                                            echo $new_datetime->format('d-m-y l');
                                        @endphp
                                    </td>
                                    @if(allowed(3, 'remove'))
                                    <td class="no-print"><button class="btn btn-danger" onclick="deleteExpense({{ $expense->id }})">Delete</button></td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-12 d-flex justify-content-center">
                {{ $expenses->appends(['fromDate' => $fromDate, 'toDate' => $toDate])->links() }}
            </div>
        </div>
    </div>

    <script type="application/javascript">
        function deleteExpense(id)
        {
            var _delete = confirm('Delete this expense?');

            if(_delete)
            {
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: "{{ url('/expense/') }}/" + id,
                    type: 'DELETE',
                    success: function(response){
                        if(response = 'deleted')
                        {
                            alert('Deleted.');
                            window.location.href = "{{ url('/expenses') }}";
                        }
                    },
                    error: function (jqXHR, status, err) {
                        console.log(err);
                    }
                });
            }
        }

        $(document).ready(function(){
            // $("#table").DataTable();

            // $("#table_length").addClass("no-print");
            // $("#table_filter").addClass("no-print");
            // $("#table_info").addClass("no-print");
            // $("#table_paginate").addClass("no-print");
        })
    </script>
@endsection