@extends('master')

@section('content')
    <div class="container">
        <div class="row pb-3">
            <div class="col-10">
                <h3>Departments Raw Material Usage</h3>
            </div>
            <div class="col-2 no-print">
                <a href="{{ url('/usage/create') }}" class="btn btn-primary">Add a Usage</a>
            </div>
        </div>

        <form action="{{ url('/usage/filter') }}" method="GET" class="no-print">
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

                <div class="col-4">
                    <label for="selectedItems">Item(s)</label>
                    <select id="selectedItems" class="form-control selectedItems" name="selectedItems[]" multiple="multiple">
                        @foreach ($items as $item)
                            <option value="{{ $item->id }}" @if(in_array($item->id, $selectedItems)) selected @endif>{{ $item->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-4">
                    <label for="selectedDepartments">Department(s)</label>
                    <select id="selectedDepartments" class="form-control selectedDepartments" name="selectedDepartments[]" multiple="multiple">
                        @foreach ($departments as $department)
                            <option value="{{ $department->id }}" @if(in_array($department->id, $selectedDepartments)) selected @endif>{{ $department->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="row pt-2 pb-2">
                <div class="col-1">
                    <button type="submit" class="btn btn-primary">Search</button>
                </div>
                <div class="col-4">
                    <a href="{{ url('/usage') }}" class="btn btn-danger">Reset</a>
                </div>
            </div>
        </form>

        <div id="section-to-print">
            <div class="row pb-2">
                <div class="col-12">
                    <button class="btn btn-primary no-print" id="print-btn" onclick="window.print()">Print</button>
                </div>
            </div>
            @if($fromDate)
            <div class="row">
                <div class="col-12 offset-7">
                    <h4>
                        From {{ date('d M Y', strtotime($fromDate)) }} - To {{ date('d M Y', strtotime($toDate)) }}
                    </h4>
                </div>
            </div>
            @endif

            @if($individualTotal)
                <hr>
                <h4><u><strong>Total Individual Quantity of Selected Raw Materials</strong></u></h4>
                <div class="row">
                    <div class="col-12">
                        <table class="table">
                            <tr>
                                <th>Raw Material</th>
                                <th>Department</th>
                                <th>Total Quantity</th>
                            </tr>
                        @foreach ($individualTotal as $usage)
                            <tr>
                                <td>{{$usage->itemName}}</td>
                                <td>{{$usage->departmentName}}</td>
                                <td>{{$usage->totalQuantity}}</td>
                            </tr>
                        @endforeach
                        </table>
                    </div>
                </div>
            @endif
            <div>
                
            </div>

            <h4><u><strong>All Quantity of @if($individualTotal) Selected @else All @endif Raw Materials</strong></u></h4>

            <div class="row">
                <div class="col-12">
                    <table class="table">
                        <tr>
                            <th>Raw Material</th>
                            <th>Department</th>
                            <th>Quantity</th>
                            <th>Date</th>
                            <th class="no-print">Delete</th>
                        </tr>
                    @foreach ($usages as $usage)
                        <tr>
                            <td>{{ $usage->item->name }}</td>
                            <td>{{ $usage->department->name }}</td>
                            <td>
                                @if($usage->purchaseUnit->type == 'darjan')
                                    @if($usage->purchaseUnit->value % 12 == 0)
                                        {{ $usage->purchaseUnit->value/12 . ' ' . $usage->purchaseUnit->type }}
                                    @else
                                        {{ (int)($usage->purchaseUnit->value / 12) > 0? 
                                            (string)((int)($usage->purchaseUnit->value / 12)) . ' ' . $usage->purchaseUnit->type . ' ' 
                                            .$usage->purchaseUnit->value % 12 . ' ' . $usage->purchaseUnit->name . '(' . (int)$usage->purchaseUnit->value . ')': 
                                            $usage->purchaseUnit->value % 12 . ' ' . $usage->purchaseUnit->name }}
                                    @endif
                                @else
                                    {{ $usage->purchaseUnit->value . ' ' . $usage->purchaseUnit->type }}
                                @endif
                            </td>
                            <td>{{ date('d M Y, h:i A', strtotime($usage->created_at)) }}</td>
                            <td class="no-print"><button class="btn btn-danger" onclick="deleteUsage({{ $usage->id }})">Delete</button></td>
                        </tr>
                    @endforeach
                    </table>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12 d-flex justify-content-center">
                @if($fromDate && $toDate)
                    {{ $usages->appends(['fromDate' => $fromDate, 'toDate' => $toDate, 'selectedItems' => $selectedItems])->links() }}
                @elseif($fromDate && $toDate && $selectedDepartments)
                    {{ $usages->appends(['fromDate' => $fromDate, 'toDate' => $toDate, 'selectedDepartments' => $selectedDepartments, 'selectedItems' => $selectedItems])->links() }}
                @else
                    {{ $usages->links() }}
                @endif
            </div>
        </div>
    </div>

    <script type="application/javascript">
            $(".selectedItems").select2();
            $(".selectedDepartments").select2();
        function deleteUsage(id)
        {
            var _delete = confirm('Delete this Usage?');

            if(_delete)
            {
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: "{{ url('/usage/') }}/" + id,
                    type: 'DELETE',
                    success: function(response){
                        if(response == 'deleted')
                            window.location.href = "{{ url('/usage') }}";
                        else
                            console.log(response);
                    },
                    failure: function(response){
                        console.log(response);
                    }
                });
            }
        }
        $(document).ready(function(){

        });
    </script>
@endsection