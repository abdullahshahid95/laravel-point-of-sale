<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ posConfigurations()->title }}</title>
        <link rel="shortcut icon" href="{{ url('uploads/' . posConfigurations()->logo) }}" />

        <!-- Scripts -->
        <script src="{{ url('assets/js/app.js') }}"></script> {{-- includes jQuery --}}

        <script src="{{ url('assets/jquery-validation-1.19.1/dist/jquery.validate.min.js') }}"></script>
        <script src="{{ url('assets/jquery-validation-1.19.1/dist/additional-methods.min.js') }}"></script>

        <link href="{{ url('assets/select2/dist/css/select2.min.css') }}" rel="stylesheet" />
        <script src="{{ url('assets/select2/dist/js/select2.min.js') }}"></script>
        
        <link href="{{ url('assets/modal/jquery.modal.min.css') }}" rel="stylesheet" />
        <script src="{{ url('assets/modal/jquery.modal.min.js') }}"></script>
        <script src="{{ url('assets/Chartjs/dist/Chart.js') }}"></script>
        <link href="{{ url('assets/TableExport/src/stable/css/tableexport.css') }}" rel="stylesheet">
        <script src="{{ url('assets/TableExport/src/v1/v1.2/js/FileSaver.js/FileSaver.js') }}"></script>
        <script src="{{ url('assets/TableExport/src/stable/js/tableexport.js') }}"></script>
        <!-- For older browsers -->
        <script src="{{ url('assets/TableExport/src/v1/v1.2/js/Blob.js/Blob.js') }}"></script>
        <script src="{{ url('assets/TableExport/src/v1/v1.2/js/js-xlsx/xlsx.core.min.js') }}"></script>
        <!-- To Import Excel -->
        <script src="{{ url('assets/papaparse.min.js') }}"></script>
        <!-- <script src="{{ url('assets/tableToCSV.js') }}"></script> -->
        <!-- <script src="{{ url('assets/tableToExcel.js') }}"></script> -->

        <!-- Fonts -->
        {{-- <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">
        <link rel="dns-prefetch" href="//fonts.gstatic.com'"> --}}

        <!-- Styles -->
        <link href="{{ url('assets/css/app.css') }}" rel="stylesheet">

        <!-- Datatables -->
        <link rel="stylesheet" type="text/css" href="{{ url('assets/DataTables/datatables.min.css') }}"/>
    </head>
    <body style="background-image: url({{ url('/uploads/' . posConfigurations()->background_image) }});">
        <div id="app">
            <div id="collapsible" class="topnav">
                @if(allowed(16, 'view'))
                <a class="nav-link" href="{{url('/')}}">Dashboard</a>
                @endif
                @if(allowed(10, 'make'))
                <a class="nav-link bg-white text-dark font-weight-bold" href="{{ url('/sale/create') }}">POS</a>
                @endif
                <!-- @if(allowed(10, 'view'))
                <div class="nav-menu-item-dropdown">
                    <a href="{{ url('/sales') }}" class="nav-link">Sales
                        <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-caret-down-fill" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                            <path d="M7.247 11.14L2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z"/>
                        </svg>
                    </a>
                    <div class="nav-menu-item-dropdown-content">
                        @if(allowed(9, 'view'))
                        <a href="{{ url('/orders') }}">Orders</a>
                        @endif
                    </div>
                </div>
                @endif -->
                @if(allowed(9, 'view'))
                <a href="{{ url('/orders') }}" class="nav-link">Sales</a>
                @endif
                @if(allowed(14, 'view') || allowed(6, 'make'))
                {{-- <a href="{{ url('/purchases') }}" class="nav-link">Purchases</a> --}}
                <div class="nav-menu-item-dropdown">
                    <a href="{{ url('/purchase-orders') }}" class="nav-link">Purchases
                        @if(allowed(6, 'make'))
                        <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-caret-down-fill" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                            <path d="M7.247 11.14L2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z"/>
                        </svg>
                        @endif
                    </a>
                    <div class="nav-menu-item-dropdown-content">
                        @if(allowed(6, 'make'))
                        <a href="{{ url('/purchase/create') }}">Add Purchases</a>
                        @endif
                    </div>
                </div>
                @endif
                @if(posConfigurations()->maintain_inventory == 1)
                @if(!allowed(7, 'view') && allowed(8, 'view'))
                <a href="{{ url('/raw-waste') }}" class="nav-link">Wastage</a>
                @elseif(allowed(7, 'view') || allowed(8, 'view'))
                <div class="nav-menu-item-dropdown">
                    <a href="{{ url('/raw-inventory') }}" class="nav-link">Inventory
                        @if(allowed(8, 'view'))
                        <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-caret-down-fill" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                            <path d="M7.247 11.14L2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z"/>
                        </svg>
                        @endif
                    </a>
                    <div class="nav-menu-item-dropdown-content">
                        @if(allowed(8, 'view'))
                        <a href="{{ url('/raw-waste') }}">Wastage</a>
                        @endif
                    </div>
                </div>
                @endif
                @endif
                @if(allowed(4, 'view') && !allowed(5, 'view'))
                <a href="{{ url('/categories') }}" class="nav-link">Categories</a>
                @elseif(allowed(4, 'view') || allowed(5, 'view'))
                <div class="nav-menu-item-dropdown">
                    <a href="{{ url('/items') }}" class="nav-link">Items
                        @if(allowed(4, 'view'))
                        <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-caret-down-fill" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                            <path d="M7.247 11.14L2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z"/>
                        </svg>
                        @endif
                    </a>
                    <div class="nav-menu-item-dropdown-content">
                        @if(allowed(4, 'view'))
                        <a href="{{ url('/categories') }}">Categories</a>
                        @endif
                    </div>
                </div>
                @endif
                <!-- @if(allowed(11, 'view'))
                <a href="{{ url('/rates') }}" class="nav-link" target="_blank">Rates</a>
                @endif -->
                @if(allowed(3, 'view'))
                <a href="{{ url('/expenses') }}" class="nav-link">Expenses</a>
                @endif
                @if(allowed(15, 'view'))
                <a href="{{ url('/specific-prices') }}" class="nav-link">CSP</a>
                @endif
                @if(allowed(1, 'view'))
                <a href="{{ url('/customers') }}" class="nav-link">Customers</a>
                @endif
                @if(allowed(13, 'view'))
                <a href="{{ url('/suppliers') }}" class="nav-link">Suppliers</a>
                @endif
                @if(allowed(12, 'view'))
                <div class="nav-menu-item-dropdown">
                    <a href="#" class="nav-link">Reports
                        <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-caret-down-fill" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                            <path d="M7.247 11.14L2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z"/>
                        </svg>
                    </a>
                    <div class="nav-menu-item-dropdown-content">
                        <a href="{{ url('/sale-orders-report') }}">Sale Report</a>
                        <a href="{{ url('/sales-report') }}">Item Sale Report</a>
                        <a href="{{ url('/purchase-orders-report') }}">Purchase Report</a>
                        <a href="{{ url('/purchases-report') }}">Item Purchase Report</a>
                        <a href="{{ url('/expenses-report') }}">Expense Report</a>
                        <a href="{{ url('/earnings') }}">Summary</a>
                    </div>
                </div>
                @endif
                
                <div class="custom-dropdown">
                    <span><img src="{{url('/uploads/user.jpg')}}" alt="User Image" width="54"></span>
                    <div class="custom-dropdown-content">
                        <span style="width: 20px;">
                            {{ '('. Auth::user()->role->name . ') ' . Auth::user()->name }}
                        </span>
                        <ul style="list-style-type: none; padding: 0px;">
                            @if(Auth::user()->role->id == 1)
                            <li>
                                <a href="{{ url('/configuration') }}">Configuration</a>
                            </li>
                            @endif
                            @if(allowed(17, 'view'))
                            @endif
                            @if(allowed(18, 'view'))
                            <li>
                                <a href="{{ url('/users') }}">Users</a>
                            </li>
                            @endif
                            @if(allowed(19, 'view'))
                            <li>
                                <a href="{{ url('/roles') }}">Roles</a>
                            </li>
                            @endif
                            <li>
                                <a href="{{ url('/logout') }}">Logout</a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="notifications">
                    <span>🔔</span>
                    <span id="badge" class="badge"></span>
                    <div class="notification-content">
                        <ul style="list-style-type: none; padding: 0px;" id="notification-list">
                            @if(posConfigurations()->maintain_inventory == 1)
                            <li>
                                <a href="#" id="inventory-warning" class="inventory-warning" onclick="onLowInventory()">&#9888; Low Inventory</a>
                            </li>
                            @endif
                            @if(strtotime('+30 days', strtotime(date('Y-m-d'))) >= strtotime(posConfigurations()->expiry_date))
                            <li>
                                <a href="#" id="expiry-warning" class="expiry-warning">&#9888; Expiry on {{ posConfigurations()->expiry_date }}</a>
                            </li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
            <!-- The Modal -->
            <div id="low-inventory-modal" class="low-inventory-modal">
                <!-- Modal content -->
                <div class="low-inventory-modal-content">
                    <div class="row bg-dark mb-2">
                        <div class="col-12 text-white">
                            Low Inventory
                            <span id="low-inventory-modal-close" class="low-inventory-modal-close" onclick="closeModal()">&times;</span>
                            <button type="button" class="btn btn-success mt-1 float-right" style="height: 80%; margin-right: 67px;" onclick="createPurchaseOrder()">Create PO</button>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <table id="low-inventory-table" class="table table-bordered table-hover" style="width: 100%;">
                                <thead>
                                    <th>Item</th>
                                    <th>Inventory quantity</th>
                                    <th>PO</th>
                                </thead>
                                <tbody id="low-inventory-list">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <main class="py-4">
                @yield('content')
            </main>
        </div>

        <!-- jQuery -->
        <!-- <script src="{{ url('assets/js/jquery3_4_1.js') }}"></script> -->
        <script type="text/javascript" src="{{ url('assets/DataTables/datatables.min.js') }}"></script>
        <script>
            var notifications = 0;
            @if(strtotime('+30 days', strtotime(date('Y-m-d'))) >= strtotime(posConfigurations()->expiry_date))
            notifications++;         
            @endif

            if(notifications > 0)
                $("#badge").empty().text(notifications);
            // setInterval(function(){
            //     let notifications = $("#notification-list li").length;

            //     $("#badge").empty().text(notifications);
            // }, 3000);

            @if(posConfigurations()->maintain_inventory == 1)
            var lowInventoryList = [];
            var lowInventoryTable = null;
            var itemsToPurchaseM = [];

            $.ajax({
                type: 'GET',
                url: "{{ url('/raw-inventory/check/') }}",
                success: function(response){
                    if(response.length > 0)
                    {
                        lowInventoryList = response;
                        document.getElementById("inventory-warning").style.display = "inline";

                        notifications++;
                        // document.getElementById("inventory-warning").classList.add("blinking");
                    }
                    else
                    {
                        document.getElementById("inventory-warning").style.display = "none";
                    }

                    if(notifications > 0)
                        $("#badge").empty().text(notifications);
                },
                error: function(response){
                    console.log(response);
                }
            });
            @endif
            function openNav()
            {
                var coll = document.getElementById("collapsible");
                var navContent = document.getElementById("nav-content");

                document.getElementById("visible-nav").classList.toggle("d-none");
                document.getElementById("hidden-nav").classList.toggle("d-none");
                coll.classList.toggle("active-collapsible");

                if(navContent.style.display === "block") 
                {
                    navContent.style.display = "none";
                    // document.getElementById("my-jumbotron").style.marginTop = "5%";
                } 
                else 
                {
                    navContent.style.display = "block";
                    // document.getElementById("my-jumbotron").style.marginTop = "30%";
                }
            }

            @if(posConfigurations()->maintain_inventory == 1)
            function onLowInventory()
            {
                $.ajax({
                    type: 'GET',
                    url: "{{ url('/raw-inventory/check/') }}",
                    success: function(response){
                        if(response.length > 0)
                        {
                            lowInventoryList = response;
                            console.clear();
                            console.log(lowInventoryList);
                            document.getElementById("inventory-warning").style.display = "inline";
                            // document.getElementById("inventory-warning").classList.add("blinking");

                            for(let i = 0; i < lowInventoryList.length; i++) 
                            {
                                lowInventoryList[i].quantity = parseFloat(lowInventoryList[i].quantity);
                                lowInventoryList[i] = Object.values(lowInventoryList[i]);
                            }
                            console.log(lowInventoryList);

                            lowInventoryTable.clear().draw();
                            lowInventoryTable.rows.add(lowInventoryList); // Add new data
                            lowInventoryTable.columns.adjust().draw(); // Redraw the DataTable
                        }
                        else
                        {
                            document.getElementById("inventory-warning").style.display = "none";
                        }

                        if(notifications > 0)
                            $("#badge").empty().text(notifications);
                    },
                    error: function(response){
                        console.log(response);
                    }
                });

                var modal = document.getElementById("low-inventory-modal");
                modal.style.display = "block";

                /*var toAppend = ``;

                lowInventoryList.forEach(item => {

                    let unit = '';
                    if(item.unitId == 1)
                    {
                        unit = ' kg';
                    }
                    else if(item.unitId == 2)
                    {
                        unit = ' (' + parseInt(item.quantity / 12) + ' Dozen' + (item.quantity % 12 > 0? ' ' + item.quantity % 12: '') + ')';
                    }
                    else
                    {
                        unit = '';
                    }

                    toAppend += `<tr>
                                    <td>` + item.name + `</td>
                                    <td>` + item.quantity + unit + `</td>
                                </tr>`;
                });

                $("#low-inventory-list").empty().append(toAppend);*/
            }
            @endif

            function closeModal()
            {
                itemsToPurchaseM = [];
                var modal = document.getElementById("low-inventory-modal");
                modal.style.display = "none";
            }

            window.onclick = function(event) {
                var lowInventoryModal = document.getElementById("low-inventory-modal");
                var returnSaleModal = document.getElementById("return-sale-modal");
                var subtractInventoryModal = document.getElementById("subtract-inventory-modal");

                if (event.target == lowInventoryModal || event.target == returnSaleModal || event.target == subtractInventoryModal) {
                    itemsToPurchaseM = [];

                    lowInventoryModal.style.display = "none";
                    if(returnSaleModal != undefined && returnSaleModal != null)
                        returnSaleModal.style.display = "none";

                    if(subtractInventoryModal != undefined && subtractInventoryModal != null)
                        subtractInventoryModal.style.display = "none";
                }
            }

            $(document).ready(function() {
                @if(posConfigurations()->maintain_inventory == 1)
                lowInventoryTable = $('#low-inventory-table').DataTable({
                    data: lowInventoryList,
                    columns: [
                        { title: "Item" },
                        { title: "Quantity", "render": function ( data, type, full, meta ) {
                            return  (full[3] == 1? (full[1] + ' kg'): (full[3] == 3? full[1]: full[1]));
                        }},
                        { title: "PO", 
                            sortable: false,
                            "render": function ( data, type, full, meta ) {
                                return '<input type="checkbox" onchange="addItemForPurchase(this, ' + full[2] + ')">';
                                // return '<button onclick="makePurchaseOrder(' + full[2] + ')" class="btn btn-success" type="button">Purchase</button>';
                            }
                        }
                    ],
                    "lengthMenu": [ [10, 25, 50, -1], [10, 25, 50, "All"] ]
                });


                $('#low-inventory-table tbody').on('click', 'tr', function (event) {
                    if (event.target.type !== 'checkbox') {

                        var data = lowInventoryTable.row(this).data();
                        let checkbox = $(this).find("td:eq(2) input");

                        $(':checkbox', this).trigger('click');
                    }
                });

                $('#low-inventory-table tbody').on('mousedown', 'tr', function (event) {
                    if (event.target.type !== 'checkbox') {
                        $(this).css('background-color', '#dee2e6').siblings().css('background-color', '#ffffff');
                    }
                });

                $('#low-inventory-table tbody').on('mouseup', 'tr', function (event) {
                    if (event.target.type !== 'checkbox') {
                        $(this).css('background-color', '#ffffff').siblings().css('background-color', '#ffffff');
                    }
                });
                @endif
            });

            function addItemForPurchase(checkbox, itemId) 
            {
                if(checkbox.checked == true)
                {
                    itemsToPurchaseM.push(itemId);
                }
                else
                {
                    itemsToPurchaseM.splice(itemsToPurchaseM.indexOf(itemId), 1);
                }

                console.log(itemsToPurchaseM);
            }

            function createPurchaseOrder()
            {
                if(itemsToPurchaseM.length < 1)
                {
                    alert("Please select item(s).");
                }
                else
                {
                    window.location.href = "{{url('/purchase/create')}}?items=" + itemsToPurchaseM;
                }
            }
        </script>
    </body>
</html>
