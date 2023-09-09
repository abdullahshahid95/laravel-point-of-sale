@extends('master')

@section('content')
    <div id="spinner" class=""></div>
    <div id="overlay" class=""></div>
    <div class="jumbotron" style="padding-left: 3%; padding-right: 3%;">
        <div class="row">
            <div class="col-6">
                <h2>Add CSP</h2>
            </div>
            <div class="col-6">
                <button type="button" class="btn btn-primary float-right" id="back-button" style="display: none;" onclick="moveBack()">Back</button>
            </div>
        </div>
        <div class="row">
            <div class="col-12" id="customers-items-selection" style="padding-left: 0; padding-right: 0;">
                <hr>
                <div class="row mb-2">
                    <div class="col-12">
                        <button type="button" class="btn btn-primary" onclick="addRow()">Insert row</button>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12" style="padding-left: 0; padding-right: 0;">
                        <table class="table table-bordered table-hover add-item-table">
                            <thead>
                                <th>Customer</th>
                                <th>Items</th>
                                <th>Remove</th>
                            </thead>
                            <tbody id="item-table-body">
                            </tbody>
                        </table>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <button type="button" class="btn btn-primary" onclick="moveToNextStep()">Next</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12" id="next-step" style="padding-left: 0; padding-right: 0; display: none;">
                <hr>
                <div class="row">
                    <div class="col-12" style="padding-left: 0; padding-right: 0;">
                        <table class="table table-bordered table-hover add-item-table">
                            <thead>
                                <th>Customer</th>
                                <th>Item</th>
                                <th>Normal Price</th>
                                <th>Special Price</th>
                                <th>Remove</th>
                            </thead>
                            <tbody id="next-step-table-body">
                            </tbody>
                        </table>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <button type="button" class="btn btn-primary" onclick="saveCSP()">Save</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script type="text/javaScript">
        $(".customer-id").select2({
            closeOnSelect: false
        });
        $(".item-id").select2({
            closeOnSelect: false
        });
        var customers = [];
        var items = [];
        let customerSelect = `<select class="form-control customer-id" multiple="multiple" required>
                                <option value="">Customers</option>`;
        let customerOptions = ``;

        let itemsSelect = `<select class="form-control item-id" multiple="multiple" required>
                                <option value="">Item</option>`;
        let itemOptions = ``;

        var rowCount = 2;

        function addRow()
        {
            let row = `<tr id='row-` + rowCount + `'>
                    <td width="40%" class="pl-2 pr-2">
                        ` + customerSelect + `
                    </td>
                    <td width="40%" class="pl-2 pr-2">
                        ` + itemsSelect + `
                    </td>
                    <td width="20%" class="pl-2 pr-2">
                        <button class="btn btn-danger float-right" type="button" onclick="removeRow(event)" style="width: 50%;">X</button>
                    </td>
                </tr>`;

            $("#item-table-body").append(row);

            $(".customer-id").select2({
                closeOnSelect: false
            });
            $(".item-id").select2({
                closeOnSelect: false
            });

            rowCount++;
        }

        function removeRow(event)
        {
            if($("#item-table-body tr").length > 1)
            {
                $(event.target.parentElement.parentElement).remove();                
            }
        }

        function removeNextStepRow(event)
        {
            if($("#next-step-table-body tr").length > 1)
            {
                $(event.target.parentElement.parentElement).remove();                
            }
        }

        $(document).ready(function(){
            $("#overlay").addClass("overlay");
            $("#spinner").addClass("spinner");

            $.ajax({
                type: 'GET',
                url: "{{ url('/specific-price/create/update-csp-page') }}",
                success: function(response){
                    $("#overlay").removeClass("overlay");
                    $("#spinner").removeClass("spinner");

                    response = JSON.parse(response);
                    customers = response.customers;
                    items = response.items;
                    
                    for(let i = 0; i < customers.length; i++)
                    {
                        customerOptions += `<option value="` + customers[i].id + `">` + customers[i].name + `</option>`;
                    }

                    customerSelect = customerSelect + (customerOptions + `</select>`);

                    for(let i = 0; i < items.length; i++)
                    {
                        itemOptions += `<option value="` + items[i].item_id + `">` + items[i].item_name + `</option>`;
                    }

                    itemsSelect = itemsSelect + (itemOptions + `</select>`);

                    let row = `<tr id="row-1">
                                    <td width="40%" class="pl-2 pr-2">
                                        ` + customerSelect + `
                                    </td>
                                    <td width="40%" class="pl-2 pr-2">
                                        ` + itemsSelect + `
                                    </td>
                                    <td width="20%" class="pl-2 pr-2">
                                        <button class="btn btn-danger float-right" type="button" onclick="removeRow(event)" style="width: 50%;">X</button>
                                    </td>
                                </tr>`;
                    $("#item-table-body").empty().append(row);

                    $(".customer-id").select2({
                        closeOnSelect: false
                    });
                    $(".item-id").select2({
                        closeOnSelect: false
                    });
                },
                error: function(response){
                    console.log(response);
                }
            });
        });

        var selectedCustomers = [];

        function moveToNextStep()
        {
            var customersInput = [];
            var itemsInput = [];
            selectedCustomers = [];

            $('#item-table-body tr').each(function (i, row) {
                // reference all the stuff you need first
                var row = $(row);
                customersInput = row.find('.customer-id').val();
                itemsInput = row.find('.item-id').val();

                if(customersInput.length > 0 && itemsInput.length > 0)
                {
                    for (let j = 0; j < customersInput.length; j++)
                    {
                        for (let k = 0; k < itemsInput.length; k++)
                        {
                            selectedCustomers.push({
                                                    customerId: customersInput[j], 
                                                    customerName: customers.find(c => c.id == customersInput[j]).name, 
                                                    itemId: itemsInput[k], 
                                                    itemName: items.find(it => it.item_id == itemsInput[k]).item_name, 
                                                    normalPrice: parseFloat(items.find(it => it.item_id == itemsInput[k]).normal_price), 
                                                    specialPrice: parseFloat(items.find(it => it.item_id == itemsInput[k]).normal_price), 
                                                });                        
                        }
                    }
                }
            });

            console.log(selectedCustomers);
            let rows = ``;

            if(selectedCustomers.length > 0)
            {
                for (let i = 0; i < selectedCustomers.length; i++)
                {
                    rows += `<tr id='next-step-row-` + (i + 1) + `'>
                                <td width="20%" class="pl-2 pr-2">
                                    ` + selectedCustomers[i].customerName + `
                                    <input type="hidden" class="hidden-customer-id" value="` + selectedCustomers[i].customerId + `">
                                </td>
                                <td width="20%" class="pl-2 pr-2">
                                    ` + selectedCustomers[i].itemName + `
                                    <input type="hidden" class="hidden-item-id" value="` + selectedCustomers[i].itemId + `">
                                </td>
                                <td width="20%" class="pl-2 pr-2">
                                    ` + selectedCustomers[i].normalPrice + `
                                </td>
                                <td width="20%" class="pl-2 pr-2">
                                    <input type="number" min="0" class="form-control special-price" value="` + selectedCustomers[i].specialPrice + `" step="0.01">
                                </td>
                                <td width="20%" class="pl-2 pr-2">
                                    <button class="btn btn-danger float-right" type="button" onclick="removeNextStepRow(event)" style="width: 50%;">X</button>
                                </td>
                            </tr>`;
                }

                document.getElementById("customers-items-selection").style.display = "none";
                document.getElementById("next-step").style.display = "block";
                document.getElementById("back-button").style.display = "block";

                $("#next-step-table-body").empty().append(rows);   
            }
        }

        function moveBack()
        {
            document.getElementById("customers-items-selection").style.display = "block";
            document.getElementById("next-step").style.display = "none";
            document.getElementById("back-button").style.display = "none";
        }

        function saveCSP()
        {
            $("#overlay").addClass("overlay");
            $("#spinner").addClass("spinner");

            let allFieldsSet = true;

            $('.special-price').each(function (i, input) {
                if($(input).val() == null || $(input).val() == '' || parseFloat($(input).val()) < 0)
                {
                    allFieldsSet = false;
                    return false;
                }
            });

            if(allFieldsSet)
            {
                $('#next-step-table-body tr').each(function (i, row) {
                let specialPrice = $(row).find('.special-price').val();
                let customerId = $(row).find('.hidden-customer-id').val();
                let itemId = $(row).find('.hidden-item-id').val();

                    selectedCustomers.find(s => s.customerId == customerId && s.itemId == itemId).specialPrice = specialPrice;
                });

                let duplicateEntriesPresent = false;
                loop1:
                for (let i = 0; i < selectedCustomers.length; i++) 
                {
                    let customerId = selectedCustomers[i].customerId;
                    let itemId = selectedCustomers[i].itemId;

                    if((i + 1) < selectedCustomers.length)
                    {
                        loop2:
                        for (let j = i + 1; j < selectedCustomers.length; j++)
                        {
                            if(selectedCustomers[j].customerId == customerId && 
                            selectedCustomers[j].itemId == itemId)
                            {
                                duplicateEntriesPresent = true;
                                break loop1;
                            }
                        }
                    }
                }

                if(duplicateEntriesPresent)
                {
                    alert("Remove duplicate entries.");

                    $("#overlay").removeClass("overlay");
                    $("#spinner").removeClass("spinner");

                    return;
                }

                $.ajax({
                    type: 'POST',
                    url: "{{ url('/specific-price') }}",
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    data: JSON.stringify({selectedCustomers: selectedCustomers}),
                    success: function(response){
                        response = JSON.parse(response);
                        if(response.message == 1)
                        {
                            alert("CSP Created Successfully.");

                            window.location.href = "{{ url('/specific-prices') }}";
                        }
                        else if(response.message == 110)
                        {
                            alert('Some entries are already present.');
                        }
                        else
                        {
                            alert("An error occurred.");
                            console.log(response);
                        }
                    },
                    error: function(response){
                        console.log(response);
                    }
                });
            }
        }
    </script>
@endsection