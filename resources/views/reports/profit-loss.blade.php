@extends('master')

@section('content')
    <div class="container">
        <form action="{{ url('/earnings') }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-4">
                    <label for="fromDate" class="col-md-4 col-form-label text-md-right">From</label>
                    <input id="fromDate" type="date" class="form-control @error('fromDate') is-invalid @enderror" name="fromDate" value="@if($fromDate){{$fromDate}}@endif" autocomplete="fromDate">                                    
                    
                    @error('fromDate')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="col-4">
                    <label for="toDate" class="col-md-4 col-form-label text-md-right">To</label>
                    <input id="toDate" type="date" class="form-control @error('toDate') is-invalid @enderror" name="toDate" value="@if($toDate){{$toDate}}@endif" autocomplete="toDate">

                    @error('toDate')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="col-2">
                    <label for="fixed-intervals" class="col-md-2 col-form-label text-md-right">Intervals</label>
                    <select id="fixed-intervals" class="form-control" onchange="onIntervalChange(event)" name="fixed_intervals">
                        <option value="1" @if($fixedIntervals == 1) selected @endif>This year</option>
                        <option value="2" @if($fixedIntervals == 2) selected @endif>This month</option>
                        <option value="3" @if($fixedIntervals == 3) selected @endif>Last month</option>
                        <option value="4" @if($fixedIntervals == 4) selected @endif>This week</option>
                        <option value="5" @if($fixedIntervals == 5) selected @endif>Last week</option>
                        <option value="6" @if($fixedIntervals == 6) selected @endif>This day</option>
                        <option value="7" @if($fixedIntervals == 7) selected @endif>All time</option>
                        <option value="8" @if($fixedIntervals == 8) selected @endif>Custom</option>
                    </select>
                </div>
            </div>
            <div class="row pt-2 pb-2">
                <div class="col-1">
                    <button type="submit" id="search-button" class="btn btn-primary">Search</button>
                </div>
                <div class="col-4">
                    <a href="{{ url('/earnings') }}" class="btn btn-danger">Reset</a>
                </div>
            </div>
        </form>

        <div id="section-to-print">
            <div class="row pb-2">
                <div class="col-2">
                    <button class="btn btn-primary no-print" id="print-btn" onclick="window.print()">Print</button>
                </div>
            </div>
            @if($fromDate)
            <div class="row pb-2">
                <div class="col-12">
                    <table class="table table-bordered w-50 bg-white">
                    @if($fixedIntervals == 7)
                    <tr>
                        <th width="10%">All time</th>
                    </tr>
                    @else
                    <tr>
                        <th width="10%">From</th>
                        <th>{{ $fromDate }}</th>
                        <th width="10%">To</th>
                        <th>{{ $toDate }}</th>
                    </tr>
                    @endif
                    </table>
                </div>
            </div>
            <div>
                <div class="row pb-2" style="margin-left: 0px;">
                    <div class="col-6 bg-white p-0" style="border: 1px solid #000000;">
                        <table style="width:100%;">
                            <tr style="border-bottom: 1px solid #000000;">
                                <td class="pl-2">
                                    <h5 class="font-weight-bold" style="color: goldenrod">SUMMARY</h5>
                                </td>
                                <td></td>
                            </tr>
                            <tr style="border-bottom: 1px solid #000000;">
                                <td class="pl-2">
                                    <strong>Total Purchase: </strong>
                                </td>
                                <td class="pr-2">
                                    <span>(<small>Incl.</small> Payable: {{ $totalPurchaseOrders->totalBalance }})</span><span class="float-right">{{ $totalPurchaseOrders->totalOrders }}</span>
                                </td>
                            </tr>
                            <tr style="border-bottom: 1px solid #000000;">
                                <td class="pl-2">
                                    <span><strong>Total Sale: </strong></span>
                                </td>
                                <td class="pr-2">
                                    <span>(<small>Incl.</small> Receivable: {{ $totalSaleOrders->totalBalance }})</span><span class="float-right">{{ $totalSaleOrders->totalOrders }}</span>
                                </td>
                            </tr>
                            <tr style="border-bottom: 1px solid #000000;">
                                <td class="pl-2">
                                    <span><strong>Bill Discount:</strong></span>
                                </td>
                                <td class="pr-2">
                                    <span class="float-right">{{ $totalSaleOrders->totalDiscount }}</span>
                                </td>
                            </tr>
                            <tr style="border-bottom: 1px solid #000000;">
                                <td class="pl-2">
                                    <span><strong>Net Sale Amount:</strong></span>
                                </td>
                                <td class="pr-2">
                                    <span class="float-right">{{ $totalSaleOrders->totalOrders - $totalSaleOrders->totalDiscount }}</span>
                                </td>
                            </tr>
                            <tr style="border-bottom: 1px solid #000000;">
                                <td class="pl-2">
                                    <span><strong>Profit:</strong></span>
                                </td>
                                <td class="pr-2">
                                    <span class="float-right">{{ ($totalSaleOrders->totalOrders - $totalSaleOrders->totalDiscount) - $totalPurchaseOrders->totalOrders }}</span>
                                </td>
                            </tr>
                            <tr style="border-bottom: 1px solid #000000;">
                                <td class="pl-2">
                                    <span><strong>Expense:</strong></span>
                                </td>
                                <td class="pr-2">
                                    <span class="float-right">{{ $expenses->totalExpense }}</span>
                                </td>
                            </tr>
                            <tr>
                                <td class="pl-2">
                                    <span><strong>Net Profit:</strong></span>
                                </td>
                                <td class="pr-2">
                                    <span class="float-right">{{ (($totalSaleOrders->totalOrders - $totalSaleOrders->totalDiscount) - $totalPurchaseOrders->totalOrders) - $expenses->totalExpense }}</span>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <table class="table table-hover bg-white">
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr style="background-color: silver;">
                                <th>Total</th>
                                <th></th>
                                <th></th>
                                <th>Avg. Sale Price</th>
                                <th>Avg. Discount</th>
                                <th>Net Sale Price</th>
                                <th>Avg. Cost Price</th>
                                <th>Sale Amount</th>
                                <th>Cost Amount</th>
                                <th>Profit</th>
                            </tr>
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td>{{ round($itemsTotalSale->average_unit_price, 2) }}</td>
                                <td>{{ round($itemsTotalSale->average_discount, 2) }}</td>
                                <td>{{ round($itemsTotalSale->average_unit_price - $itemsTotalSale->average_discount, 2) }}</td>
                                <td>{{ round($itemsTotalSale->average_unit_cost, 2) }}</td>
                                <td>{{ $itemsTotalSale->totalSale - $totalSaleOrders->totalDiscount }}</td>
                                <td>{{ $itemsTotalSale->totalPurchase }}</td>
                                <td>{{ $itemsTotalSale->totalSale - $itemsTotalSale->totalPurchase }}</td>
                            </tr>
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                        </table>
                        <table class="table table-hover bg-white">
                            @foreach($categoriesindividualTotalSale as $category)
                            <tr>
                                <th></th>
                                <th>Item Name</th>
                                <th>Sold Qty.</th>
                                <th>Avg. Sale Price</th>
                                <th>Avg. Discount</th>
                                <th>Net Sale Price</th>
                                <th>Avg. Cost Price</th>
                                <th>Sale Amount</th>
                                <th>Cost Amount</th>
                                <th>Profit</th>
                            </tr>
                            <tr class="category-top-row" style="background-color: silver;">
                                <th>{{ $category->category_name }}</th>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <th>{{ $category->totalSale }}</th>
                                <th>{{ $category->totalPurchase }}</th>
                                <th>{{ $category->totalSale - $category->totalPurchase }}</th>
                            </tr>
                            @foreach($itemsindividualTotalSale as $item)
                            @if($category->id == $item->category_id)
                            <tr>
                                <td></td>
                                <td>{{ $item->item_name }}</td>
                                <td>
                                    @if($item->unit_id == 1)
                                    {{ $item->totalQuantity }} kg
                                    @elseif($item->unit_id == 3)
                                    {{ $item->totalQuantity }} 
                                    @elseif($item->unit_id == 2)
                                    {{ $item->totalQuantity . '(' . ((int)($item->totalQuantity / 12)) . ' Dozen' . ($item->totalQuantity % 12 > 0? ' ' . $item->totalQuantity % 12: '') . ')' }}
                                    @endif
                                </td>
                                <td>{{ round($item->average_unit_price, 2) }}</td>
                                <td>{{ round($item->average_discount, 2) }}</td>
                                <td>{{ round($item->average_unit_price - $item->average_discount, 2) }}</td>
                                <td>{{ round($item->average_unit_cost, 2) }}</td>
                                <td>{{ $item->totalSale }}</td>
                                <td>{{ $item->totalPurchase }}</td>
                                <td>{{ $item->totalSale - $item->totalPurchase }}</td>
                            </tr>
                            @endif
                            @endforeach
                            @endforeach
                        </table>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>

    <script type="application/javascript">
        function onIntervalChange(event)
        {
            if(event.target.value != 8)
            {
                var date = new Date();
                var dd = String(date.getDate()).padStart(2, '0');
                var mm = String(date.getMonth() + 1).padStart(2, '0'); //January is 0!
                var yyyy = date.getFullYear();

                var today = yyyy + '-' + mm + '-' + dd; //today's date

                document.getElementById("toDate").value = today;

                let interval = event.target.value;
                let toDate = '';
                let fromDate = '';

                if(interval == 1)
                {
                    today = yyyy + '-' + '01' + '-' + '01';
                    fromDate = today;
                }
                else if(interval == 2)
                {
                    today = yyyy + '-' + mm + '-' + '01';
                    fromDate = today;
                }
                else if(interval == 3)
                {
                    dd = String(date.getDate()).padStart(2, '0');
                    mm = String((date.getMonth()) > 0? (date.getMonth()): 12).padStart(2, '0'); //January is 0!
                    yyyy = date.getFullYear();

                    today = yyyy + '-' + mm + '-' + '01';
                    fromDate = today;

                    date.setDate(1);
                    date.setHours(-1);

                    var dd = String(date.getDate()).padStart(2, '0');
                    var mm = String(date.getMonth() + 1).padStart(2, '0'); //January is 0!
                    var yyyy = date.getFullYear();
                    
                    document.getElementById("toDate").value = yyyy + '-' + mm + '-' + dd;
                }
                else if(interval == 4)
                {
                    /*let first = date.getDate() - date.getDay() + 1; // First day is the  day of the month - the day of the week  
                    let firstday = new Date(date.setDate(first));

                    let dd = String(firstday.getDate()).padStart(2, '0');
                    let mm = String(firstday.getMonth() + 1).padStart(2, '0'); //January is 0!
                    let yyyy = firstday.getFullYear();

                    today = yyyy + '-' + mm + '-' + dd;

                    fromDate = today;*/

                    let thisWeek = new Date(date.getFullYear(), date.getMonth(), date.getDate() - 6);

                    let first = thisWeek.getDate() - thisWeek.getDay() + 1; // First day is the  day of the month - the day of the week  
                    let last = thisWeek + 6; // last day is the first day + 6   
                    let firstday = new Date(thisWeek.setDate(first));   
                    let lastday = new Date(thisWeek.setDate(thisWeek.getDate()+6));

                    let dd = String(firstday.getDate()).padStart(2, '0');
                    let mm = String(firstday.getMonth() + 1).padStart(2, '0'); //January is 0!
                    let yyyy = firstday.getFullYear();

                    today = yyyy + '-' + mm + '-' + dd;

                    fromDate = today;

                    dd = String(lastday.getDate()).padStart(2, '0');
                    mm = String(lastday.getMonth() + 1).padStart(2, '0'); //January is 0!
                    yyyy = lastday.getFullYear();

                    today = yyyy + '-' + mm + '-' + dd;

                    document.getElementById("toDate").value = today;
                }
                else if(interval == 5)
                {
                    let lastWeek = new Date(date.getFullYear(), date.getMonth(), date.getDate() - 13);

                    let first = lastWeek.getDate() - lastWeek.getDay() + 1; // First day is the  day of the month - the day of the week  
                    let last = lastWeek + 6; // last day is the first day + 6   
                    let firstday = new Date(lastWeek.setDate(first));   
                    let lastday = new Date(lastWeek.setDate(lastWeek.getDate()+6));

                    let dd = String(firstday.getDate()).padStart(2, '0');
                    let mm = String(firstday.getMonth() + 1).padStart(2, '0'); //January is 0!
                    let yyyy = firstday.getFullYear();

                    today = yyyy + '-' + mm + '-' + dd;

                    fromDate = today;

                    dd = String(lastday.getDate()).padStart(2, '0');
                    mm = String(lastday.getMonth() + 1).padStart(2, '0'); //January is 0!
                    yyyy = lastday.getFullYear();

                    today = yyyy + '-' + mm + '-' + dd;
                    
                    document.getElementById("toDate").value = today;
                }
                else if(interval == 6)
                {
                    fromDate = today;
                }
                else if(interval == 7)
                {
                    // document.getElementById("toDate").value = '';
                    fromDate = '';
                }

                document.getElementById("fromDate").value = fromDate;

                document.getElementById("search-button").click();
            }
            else
            {
                document.getElementById("fromDate").value = '';
                document.getElementById("toDate").value = '';
            }
        }

        $(document).ready(function(){
            $("#section-to-print").tableExport({
                    headers: true,
                    footers: true,
                    formats: ['csv', 'xls'],
                    // formats: ['xls'],
                    filename: 'Summary',
                    // filename: 'id',
                    bootstrap: true,
                    exportButtons: true,
                    position: 'top',
                    ignoreRows: null,
                    ignoreCols: null,
                    trimWhitespace: true,
                    RTL: false,
                    sheetname: 'Summary'
                    // sheetname: 'id'
            });

            if("{{$fromDate}}" == null || "{{$fromDate}}" == '')
            {
                var date = new Date();
                var dd = String(date.getDate()).padStart(2, '0');
                var mm = String(date.getMonth() + 1).padStart(2, '0'); //January is 0!
                var yyyy = date.getFullYear();

                var today = yyyy + '-' + mm + '-' + dd; //today's date

                document.getElementById("toDate").value = today;
                document.getElementById("fromDate").value = today;

                document.getElementById("search-button").click();
            }
        });
    </script>
@endsection