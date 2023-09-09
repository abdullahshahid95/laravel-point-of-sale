@extends('master')

@section('content')
    <div class="container">
        <form action="{{ url('/expenses-report/filter') }}" method="GET">
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
                    <a href="{{ url('/expenses-report') }}" class="btn btn-danger">Reset</a>
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
            </div>
            
            <div id="section-to-export">
                @if($fromDate)
                <div class="row pb-2">
                    <div class="col-12">
                        <table class="table table-bordered w-50 bg-white">
                            <tr>
                                <th width="10%">From</th>
                                <th>{{ $fromDate }}</th>
                                <th width="10%">To</th>
                                <th>{{ $toDate }}</th>
                            </tr>
                            <tr style="display: none;">
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                            </tr>
                        </table>
                    </div>
                </div>
                @endif
                @if($total)
                <div class="row pb-2">
                    <div class="col-12">
                        <table class="table table-bordered w-50 bg-white">
                            <tr>
                                <th width="25%">Total Expenses</th>
                                <th>{{ $total->totalExpense ?? 0 }}</th>
                            </tr>
                            <tr style="display: none;">
                                <th></th>
                                <th></th>
                            </tr>
                        </table>
                    </div>
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
                                {{-- <th class="no-print">Delete</th> --}}
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
                                    {{-- <td class="no-print"><button class="btn btn-danger" onclick="deleteExpense({{ $expense->id }})">Delete</button></td> --}}
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        @if(sizeof($expenses) > 0)
        <div class="row">
            <div class="col-12 d-flex justify-content-center">
                {{ $expenses->appends(['fromDate' => $fromDate, 'toDate' => $toDate])->links() }}
            </div>
        </div>
        @endif
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
            $("#section-to-export").tableExport({
                headers: true,
                footers: true,
                formats: ['csv', 'xls'],
                // formats: ['xls'],
                filename: 'Expenses',
                // filename: 'id',
                bootstrap: true,
                exportButtons: true,
                position: 'top',
                ignoreRows: null,
                ignoreCols: null,
                trimWhitespace: true,
                RTL: false,
                sheetname: 'Expenses'
                // sheetname: 'id'
            });
        });
    </script>
@endsection