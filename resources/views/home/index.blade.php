@extends('master')

@section('content')
    <div class="container" style="background-color: rgba(255, 255, 255, 0.9);">
        {{-- <div class="home-page">
            <p class="heading">{{ posConfigurations()->title }}</p>
        </div> --}}
        <div class="row d-flex justify-content-center">
            <div class="col-2 card m-1" style="background-color: #74d252; color: hsl(0, 0%, 100%); height: 150px;">
                <h6 class="font-weight-bold">Net Profit</h6>
                <div style="height: 28%;">
                    <span id="net-profit" style="font-size: 1.35rem;">
                        {{ $netProfitThisMonth }}
                    </span>
                    <span id="percent-difference" class="float-right" style="font-size: 80%; font-weight: bolder; padding-bottom: 5px; margin-bottom: 200px;">
                        {{ $percentDifference . '%' }}
                    </span>
                    <span id="indicator" class="float-right" style="font-size: 70%; font-weight: bolder; padding-bottom: 5px;">
                        {{ $netProfitThisMonth > $netProfitLastMonth? '↑': ($netProfitThisMonth < $netProfitLastMonth? '↓': '-') }}
                    </span>
                </div>
                <br>
                <select id="net-profit-type" class="form-control" onchange="onNetProfitTypeChange(event)" style="position: absolute; top: 73%; right: 0%;">
                    <option value="1">This year</option>
                    <option value="2" selected>This month</option>
                    <option value="3">This week</option>
                    <option value="4">This day</option>
                    <option value="5">This hour</option>
                    <option value="6">All time</option>
                </select>
            </div>
            <div class="col-3 card m-1" style="background-color: #469cec; color: hsl(0, 0%, 100%); height: 150px;">
                <h6 class="font-weight-bold">Top Selling Item</h6>
                <table>
                    <tr>
                        <td>
                            <h6 id="top-selling-item-orders" class="" style="font-weight: bold;">{{ $topSellingItem? 'Orders: ' . $topSellingItem->itemCount: '--None--' }}</h6>
                        </td>
                        <td>
                            <h6 id="top-selling-item-total" class="" style="font-weight: bold;">{{ $topSellingItem? 'Total: ' . $topSellingItem->totalPrice: '--None--' }}</h6>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <h6 id="top-selling-item-quantity" class="" style="font-weight: bold;">{{ $topSellingItem? 'Quantity: ' . $topSellingItem->totalQuantity: '--None--' }}</h6>
                        </td>
                        <td>
                            <h5 id="top-selling-item-name" class="text-center" style="width: 100%; background: rgba(0, 0, 0, 0.3);">{{ $topSellingItem? $topSellingItem->item: '--None--' }}</h5>
                        </td>
                    </tr>
                </table>
                <select id="top-selling-type" class="form-control" onchange="onTopSaleTypeChange(event)" style="position: absolute; top: 73%; right: 0%;">
                    <option value="1">This year</option>
                    <option value="2" selected>This month</option>
                    <option value="3">This week</option>
                    <option value="4">This day</option>
                    <option value="5">This hour</option>
                    <option value="6">All time</option>
                </select>
            </div>
            <div class="col-3 card m-1" style="background-color: #fc5b5b; color: hsl(0, 0%, 100%); height: 150px;">
                <h6 class="font-weight-bold">Lowest Selling Item</h6>
                <table>
                    <tr>
                        <td>
                            <h6 id="worst-selling-item-orders" class="" style="font-weight: bold;">{{ $worstSellingItem? 'Orders: ' . $worstSellingItem->itemCount: '--None--' }}</h6>
                        </td>
                        <td>
                            <h6 id="worst-selling-item-total" class="" style="font-weight: bold;">{{ $worstSellingItem? 'Total: ' . $worstSellingItem->totalPrice: '--None--' }}</h6>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <h6 id="worst-selling-item-quantity" class="" style="font-weight: bold;">{{ $worstSellingItem? 'Quantity: ' . $worstSellingItem->totalQuantity: '--None--' }}</h6>
                        </td>
                        <td>
                            <h5 id="worst-selling-item-name" class="text-center" style="width: 100%; background: rgba(0, 0, 0, 0.3);">{{ $worstSellingItem? $worstSellingItem->item: '--None--' }}</h5>
                        </td>
                    </tr>
                </table>
                <select id="worst-selling-type" class="form-control" onchange="onWorstSaleTypeChange(event)" style="position: absolute; top: 73%; right: 0%;">
                    <option value="1">This year</option>
                    <option value="2" selected>This month</option>
                    <option value="3">This week</option>
                    <option value="4">This day</option>
                    <option value="5">This hour</option>
                    <option value="6">All time</option>
                </select>
            </div>
            <div class="col-2 card m-1" style="background-color: #8f20e1; color: hsl(0, 0%, 100%); height: 150px;">
                <h6 class="font-weight-bold">Total Expenses</h6>
                <div style="height: 28%;">
                    <span id="total-expenses" style="font-size: 1.35rem;">
                        {{ $expensesThisMonth->totalExpenses ?? '0' }}
                    </span>
                </div>
                <br>
                <select id="total-expenses-type" class="form-control" onchange="onTotalExpensesTypeChange(event)" style="position: absolute; top: 73%; right: 0%;">
                    <option value="1">This year</option>
                    <option value="2" selected>This month</option>
                    <option value="3">This week</option>
                    <option value="4">This day</option>
                    <option value="5">This hour</option>
                    <option value="6">All time</option>
                </select>
            </div>
            <div class="col-5 card m-1">
                <div class="row">
                    <div class="col-6">
                        <h3>Sale Chart</h3>
                    </div>
                    <div class="col-6 pt-1 pb-1">
                        <select id="sales-graph-type" class="form-control" onchange="onSalesGraphTypeChange(event)">
                            <option value="1">Yearly</option>
                            <option value="2" selected>Monthly</option>
                            <option value="3">Daily</option>
                        </select>
                    </div>
                </div>
                <canvas id="sales-graph" width="400" height="400"></canvas>
            </div>
            <div class="col-5 m-1">
                <div class="row">
                    <div class="col-lg-12" style="background-color: #f4941b; color: hsl(0, 0%, 100%); height: 140px;">
                        <h3>Weekly Peak Time</h3>
                        <div class="row d-flex justify-content-between">
                            @foreach ($occurrences as $key => $occurrence)
                            <div class="col-sm-3 border">{{ $key }} <br> {{ $occurrence }}</div>                    
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="row d-flex justify-content-center">
                    <div class="col-lg-5 card m-1" style="background-color: #fc5b5b; color: hsl(0, 0%, 100%); height: 120px;">
                        <div style="height: 28%;">
                            <span id="total-payable" style="font-size: 1.35rem;">
                                {{ $totalPayable->amount ?? '0' }}
                            </span>
                        </div>
                        <h6 class="">Total Payable</h6>
                        {{-- <br>
                        <select id="total-expenses-type" class="form-control" onchange="onTotalExpensesTypeChange(event)">
                            <option value="1">This year</option>
                            <option value="2" selected>This month</option>
                            <option value="3">This week</option>
                            <option value="4">This day</option>
                            <option value="5">This hour</option>
                            <option value="6">All time</option>
                        </select> --}}
                    </div>
                    <div class="col-lg-5 card m-1" style="background-color: #469cec; color: hsl(0, 0%, 100%); height: 120px;">
                        <div style="height: 28%;">
                            <span id="total-payable" style="font-size: 1.35rem;">
                                {{ $totalReceivable->amount ?? '0' }}
                            </span>
                        </div>
                        <h6 class="">Total Receivable</h6>
                        {{-- <br>
                        <select id="total-expenses-type" class="form-control" onchange="onTotalExpensesTypeChange(event)">
                            <option value="1">This year</option>
                            <option value="2" selected>This month</option>
                            <option value="3">This week</option>
                            <option value="4">This day</option>
                            <option value="5">This hour</option>
                            <option value="6">All time</option>
                        </select> --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javaScript">
        $(document).ready(function(){
            $.ajax({
                type: 'GET',
                url: "{{ url('/dashboard/get-sales-graph') }}/" + 2,
                success: function(response){
                    if(JSON.parse(response) != null && JSON.parse(response) != '')
                    {
                        var sales = JSON.parse(response);

                        var intervals = [];
                        var lableData = [];

                        sales.forEach(sale => {
                            intervals.push(sale.interval)
                        });

                        sales.forEach(sale => {
                            lableData.push(sale.total)
                        });

                        var ctx = document.getElementById('sales-graph').getContext('2d');
                        var myChart = new Chart(ctx, {
                            type: 'line',
                            data: {
                                labels: intervals,
                                datasets: [{
                                    label: 'Sales Amount',
                                    data: lableData,
                                    backgroundColor: [
                                        'rgba(255, 99, 132, 0.2)',
                                        'rgba(54, 162, 235, 0.2)',
                                        'rgba(255, 206, 86, 0.2)',
                                        'rgba(75, 192, 192, 0.2)',
                                        'rgba(153, 102, 255, 0.2)',
                                        'rgba(255, 159, 64, 0.2)'
                                    ],
                                    borderColor: [
                                        'rgba(255, 99, 132, 1)',
                                        'rgba(54, 162, 235, 1)',
                                        'rgba(255, 206, 86, 1)',
                                        'rgba(75, 192, 192, 1)',
                                        'rgba(153, 102, 255, 1)',
                                        'rgba(255, 159, 64, 1)'
                                    ],
                                    borderWidth: 1
                                }]
                            },
                            options: {
                                scales: {
                                    yAxes: [{
                                        ticks: {
                                            beginAtZero: true
                                        }
                                    }]
                                }
                            }
                        });

                    }
                    else
                    {
                        
                    }
                },
                error: function(response){
                    console.log(response);
                }
            });
        });

        function onTopSaleTypeChange(event)
        {
            $.ajax({
                type: 'GET',
                url: "{{ url('/dashboard/topsale/') }}/" + event.target.value,
                success: function(response){
                    if(JSON.parse(response) != null && JSON.parse(response) != '')
                    {
                        console.log(response);
                        
                        $("#top-selling-item-name").empty().text(JSON.parse(response).item);
                        $("#top-selling-item-orders").empty().text('Orders: ' + JSON.parse(response).itemCount);
                        $("#top-selling-item-total").empty().text('Total: ' + JSON.parse(response).totalPrice);
                        $("#top-selling-item-quantity").empty().text('Quantity: ' + JSON.parse(response).totalQuantity);
                    }
                    else
                    {
                        $("#top-selling-item-name").empty().text('--None--');
                        $("#top-selling-item-orders").empty().text('--None--');
                        $("#top-selling-item-total").empty().text('--None--');
                        $("#top-selling-item-quantity").empty().text('--None--');
                    }
                },
                error: function(response){
                    console.log(response);
                }
            });
        }

        function onWorstSaleTypeChange(event)
        {
            $.ajax({
                type: 'GET',
                url: "{{ url('/dashboard/worstsale/') }}/" + event.target.value,
                success: function(response){
                    if(JSON.parse(response) != null && JSON.parse(response) != '')
                    {
                        $("#worst-selling-item-name").empty().text(JSON.parse(response).item);
                        $("#worst-selling-item-orders").empty().text('Orders: ' + JSON.parse(response).itemCount);
                        $("#worst-selling-item-total").empty().text('Total: ' + JSON.parse(response).totalPrice);
                        $("#worst-selling-item-quantity").empty().text('Quantity: ' + JSON.parse(response).totalQuantity);
                    }
                    else
                    {
                        $("#worst-selling-item-name").empty().text('--None--');
                        $("#worst-selling-item-orders").empty().text('--None--');
                        $("#worst-selling-item-total").empty().text('--None--');
                        $("#worst-selling-item-quantity").empty().text('--None--');
                    }
                },
                error: function(response){
                    console.log(response);
                }
            });
        }

        function onNetProfitTypeChange(event)
        {
            $.ajax({
                type: 'GET',
                url: "{{ url('/dashboard/netprofit/') }}/" + event.target.value,
                success: function(response){
                    if(JSON.parse(response) != null && JSON.parse(response) != '')
                    {
                        data = JSON.parse(response);
                        $("#net-profit").empty().text(data.netProfitThisMonth);
                        $("#percent-difference").empty().text(data.percentDifference + '%');
                        $("#indicators").empty().text(data.netProfitThisMonth > data.netProfitLastMonth? '↑': (data.netProfitThisMonth < data.netProfitLastMonth? '↓': '-'));
                    }
                    else
                    {
                        $("#net-profit").empty().text('--None--');
                        $("#percent-difference").empty().text('--None--');
                        $("#indicators").empty().text('--None--');
                    }
                },
                error: function(response){
                    console.log(response);
                }
            });
        }

        function onSalesGraphTypeChange(event)
        {
            $.ajax({
                type: 'GET',
                url: "{{ url('/dashboard/get-sales-graph') }}/" + event.target.value,
                success: function(response){
                    if(JSON.parse(response) != null && JSON.parse(response) != '')
                    {
                        var sales = JSON.parse(response);

                        var intervals = [];
                        var lableData = [];

                        sales.forEach(sale => {
                            intervals.push(sale.interval)
                        });

                        sales.forEach(sale => {
                            lableData.push(sale.total)
                        });

                        var ctx = document.getElementById('sales-graph').getContext('2d');
                        var myChart = new Chart(ctx, {
                            type: 'line',
                            data: {
                                labels: intervals,
                                datasets: [{
                                    label: 'Sales Amount',
                                    data: lableData,
                                    backgroundColor: [
                                        'rgba(255, 99, 132, 0.2)',
                                        'rgba(54, 162, 235, 0.2)',
                                        'rgba(255, 206, 86, 0.2)',
                                        'rgba(75, 192, 192, 0.2)',
                                        'rgba(153, 102, 255, 0.2)',
                                        'rgba(255, 159, 64, 0.2)'
                                    ],
                                    borderColor: [
                                        'rgba(255, 99, 132, 1)',
                                        'rgba(54, 162, 235, 1)',
                                        'rgba(255, 206, 86, 1)',
                                        'rgba(75, 192, 192, 1)',
                                        'rgba(153, 102, 255, 1)',
                                        'rgba(255, 159, 64, 1)'
                                    ],
                                    borderWidth: 1
                                }]
                            },
                            options: {
                                scales: {
                                    yAxes: [{
                                        ticks: {
                                            beginAtZero: true
                                        }
                                    }]
                                }
                            }
                        });

                    }
                    else
                    {
                        
                    }
                },
                error: function(response){
                    console.log(response);
                }
            });
        }

        function onTotalExpensesTypeChange(event)
        {
            $.ajax({
                type: 'GET',
                url: "{{ url('/dashboard/totalexpenses/') }}/" + event.target.value,
                success: function(response){
                    if(JSON.parse(response) != null && JSON.parse(response) != '')
                    {
                        data = JSON.parse(response);
                        $("#total-expenses").empty().text((data.totalExpenses != null && data.totalExpenses != '')? data.totalExpenses: '0' );
                    }
                    else
                    {
                        $("#total-expenses").empty().text('--None--');
                    }
                },
                error: function(response){
                    console.log(response);
                }
            });
        }

    </script>
@endsection