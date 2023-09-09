@extends('master')

@section('content')
<div id="spinner" class=""></div>
<div id="overlay" class=""></div>

<div id="my-jumbotron" class="jumbotron">
    <div class="row">
        <div class="col-2">
            <a class="btn btn-success" onclick="refreshPurchase()">Refresh</a>
        </div>
        <div class="col-3">
            <div class="row">
                <div class="col-12">
                    <select id="supplier" class="form-control" style="width: 90%;">
                        {{-- Suppliers options appear here --}}
                    </select>
                    <button type="button" class="btn btn-primary pt-0 pb-1 pl-1 pr-1" onclick="openAddSupplierDialogue()" title="Add Supplier">+</button>
                </div>
            </div>
            <div class="row d-none" id="not-collected-row">
                <div class="col-6" style="padding-right: 0;">
                    <input id="receiving-date" type="date" class="form-control" style="width: 95%; font-size: smaller;">
                </div>
                <div class="col-6" style="padding-left: 0;">
                    <input id="receiving-time" type="time" class="form-control" style="width: 80%; font-size: smaller;">
                </div>
            </div>
        </div>
        <div class="col-3">
            <div class="row">
                <div class="col-12">
                    <input type="radio" id="received" name="status" value="2" checked>
                    <label for="received">Received</label>
                    |
                    <input type="radio" id="not-received" name="status" value="1">
                    <label for="not-received">Not Received</label>
                </div>
            </div>
        </div>
        <!-- <div class="col-4">
            <a class="btn btn-success float-right" href="{{ url('/purchase/edit') }}">Exchange/Return</a>
        </div> -->
    </div>
    <br>
    <div id="no-print">
        <div class="row">
            <div class="col-8">
                <div class="row mb-2">
                    <div class="col-6">
                        <input type="text" id="barcode" class="form-control" placeholder="Barcode"/>
                    </div>
                    <div class="col-6">
                        <button type="button" title="List" class="btn btn-primary float-right" id="table-view-button" onclick="openTableView()">
                            <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-list-ul" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" d="M5 11.5a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5zm-3 1a1 1 0 1 0 0-2 1 1 0 0 0 0 2zm0 4a1 1 0 1 0 0-2 1 1 0 0 0 0 2zm0 4a1 1 0 1 0 0-2 1 1 0 0 0 0 2z"/>
                            </svg>
                        </button>
                        <button type="button" title="Grid" class="btn btn-primary float-right mr-2 " id="grid-view-button" style="background-color: #6574cd; border: 1px solid #6574cd;" onclick="openGridView()">
                            <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-grid-3x3-gap-fill" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                <path d="M1 2a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v2a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V2zm5 0a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v2a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V2zm5 0a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v2a1 1 0 0 1-1 1h-2a1 1 0 0 1-1-1V2zM1 7a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v2a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V7zm5 0a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v2a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V7zm5 0a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v2a1 1 0 0 1-1 1h-2a1 1 0 0 1-1-1V7zM1 12a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v2a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1v-2zm5 0a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v2a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1v-2zm5 0a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v2a1 1 0 0 1-1 1h-2a1 1 0 0 1-1-1v-2z"/>
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="row items-row-inactive" id="items-table-view">
                    <div class="col-12">
                        <table class="table table-hover" id="items-table">
                            <thead>
                                <th>
                                    Item
                                </th>
                            </thead>
                            <tbody id="items-table-body">
                                {{-- Items list appear here --}}
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="row" id="items-grid-view">
                    <div class="col-12">
                        <div class="tab" id="category-tabs">
                            {{-- Category tabs appear here --}}
                        </div>
                        {{-- Items related to each category here --}}
                    </div>
                </div>
            </div>
            <div class="col-4 sale-items-list">
                <div class="row mb-3">
                    <div class="col-3 payment-fields-cols">
                        <label for="subTotal">Total</label>
                        <input type="number" id="subTotal" class="form-control" value="" readonly disabled>
                    </div>
                    <div class="col-3 payment-fields-cols">
                        <label for="payment">Payment</label>
                        <input type="number" min="-10000000" id="payment" class="form-control"  name="payment" oninput="onPaymentInput()" required/>
                        <br>
                        <label for="balance">Balance</label>
                        <input type="number" id="balance" class="form-control" readonly disabled>
                    </div>
                    <div class="col-6 pt-0 pb-0 border-top border-bottom">
                        <br>
                        <div style="float: right; margin-left: 100%;">
                            <button type="button" id="print-button" class="submitBtn no-print btn btn-primary" onclick="checkout()">
                                <svg xmlns="http://www.w3.org/2000/svg" width="2em" height="2em" fill="currentColor" class="bi bi-save-fill" viewBox="0 0 16 16">
                                    <path d="M8.5 1.5A1.5 1.5 0 0 1 10 0h4a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2h6c-.314.418-.5.937-.5 1.5v7.793L4.854 6.646a.5.5 0 1 0-.708.708l3.5 3.5a.5.5 0 0 0 .708 0l3.5-3.5a.5.5 0 0 0-.708-.708L8.5 9.293V1.5z"/>
                                </svg>
                            </button>
                            <br>
                            <small class="d-none" id="loading-text">
                                Saving <?xml version="1.0" encoding="UTF-8" standalone="no"?><svg xmlns:svg="http://www.w3.org/2000/svg" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.0" width="20%" height="20%" viewBox="0 0 128 128" xml:space="preserve"><g><path d="M78.75 16.18V1.56a64.1 64.1 0 0 1 47.7 47.7H111.8a49.98 49.98 0 0 0-33.07-33.08zM16.43 49.25H1.8a64.1 64.1 0 0 1 47.7-47.7V16.2a49.98 49.98 0 0 0-33.07 33.07zm33.07 62.32v14.62A64.1 64.1 0 0 1 1.8 78.5h14.63a49.98 49.98 0 0 0 33.07 33.07zm62.32-33.07h14.62a64.1 64.1 0 0 1-47.7 47.7v-14.63a49.98 49.98 0 0 0 33.08-33.07z" fill="#000000" fill-opacity="1"/><animateTransform attributeName="transform" type="rotate" from="0 64 64" to="-90 64 64" dur="400ms" repeatCount="indefinite"></animateTransform></g></svg>
                            </small>
                        </div>
                    </div>
                </div>
                <div class="row" style="height: 500px;overflow: scroll">
                    <div class="col-12">
                        <table class="table table-hover table-bordered" id="item-sales-table">
                            <thead>
                                <tr>
                                    <th>Item</th>
                                    <th>Quantity</th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th>Price</th>
                                    <th class="no-print">Delete</th>
                                </tr>
                            </thead>
                            <tbody id="purchases">

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>   
    </div>
</div>

<!-- Add Supplier Modal -->
<div id="add-supplier-modal" class="add-supplier-modal">
    <!-- Modal content -->
    <div class="add-supplier-modal-content">
        <div class="row">
            <div class="col-6">
                <h3>Add Supplier</h3>
            </div>
            <div class="col-6">
                <span id="add-supplier-modal-close" class="add-supplier-modal-close" onclick="closeAddSupplierDialogue()">&times;</span>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="form-group col-12">
                <div class="col-6 pl-5">
                    <form id="add-supplier-form" action="#" onsubmit="addSupplier(event)">
                    @csrf
                    <div class="form-group row">
                        <label for="name" class="col-md-4 col-form-label text-md-right">Name</label>
                        <div class="col-md-6">
                            <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name">                                    
                            
                            @error('name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="phone" class="col-md-4 col-form-label text-md-right">Phone</label>
                        <div class="col-md-6">
                            <input id="phone" type="text" class="form-control @error('phone') is-invalid @enderror" name="phone" value="{{ old('phone') }}" autocomplete="phone">
            
                            @error('phone')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="address" class="col-md-4 col-form-label text-md-right">Address</label>
                        <div class="col-md-6">
                            <textarea rows="10" cols="12" id="address" class="form-control @error('address') is-invalid @enderror" name="address" value="{{ old('address') }}" autocomplete="address"></textarea>
            
                            @error('address')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-md-4"></div>
                        <div class="col-md-6">
                            <button class="btn btn-primary float-right" type="submit">Submit</button>
                        </div>
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javaScript">

    function openTab(evt, tabName) 
    {
        var i, tabcontent, tablinks;
        tabcontent = document.getElementsByClassName("tabcontent");
        for (i = 0; i < tabcontent.length; i++) {
            tabcontent[i].style.display = "none";
        }
        tablinks = document.getElementsByClassName("tablinks");
        for (i = 0; i < tablinks.length; i++) {
            tablinks[i].className = tablinks[i].className.replace(" active", "");
        }
        document.getElementById(tabName).style.display = "block";
        evt.currentTarget.className += " active";
    }

    var allItems = "";

    var status = 2;
    var type = 2;

    var selectedItems = [];
    var checkOutData = {
        total: 0,
        subTotal: 0,
        payment: 0,
        cash: 0,
        change: 0,
        date: null,
        receiptNumber: null
    }
    var selectedSupplierId = 1;
    var currentItemId = 0;
    var purchaseRows = ``;
    var quantityHtml = ``;

    var checkOutLoading = false;
    var itemIds = [];
    var selectedItemsLength = 0;
    var ItemIdsLength = 0;

    for (let i = 0; i < document.getElementsByName('status').length; i++) 
    {
        document.getElementsByName('status')[i].addEventListener('change', function(){
            if(this.value == 1)
            {
                document.getElementById('not-collected-row').classList.remove('d-none');
            }
            else
            {
                document.getElementById('not-collected-row').classList.add('d-none');
            }
            
            status = this.value;
        });
    }

    for (let i = 0; i < document.getElementsByName('type').length; i++) 
    {
        document.getElementsByName('type')[i].addEventListener('change', function(){
            type = this.value;
        });
    }

    document.getElementById('barcode').addEventListener('input', function(){
        let barcode = this.value.trim();

        if(barcode != '' && barcode != null)
        {
            if(allItems.findIndex(item => item.label == barcode) > 0)
            {
                let item = allItems.find(item => item.label == barcode);
                selectItem(item.id);

                document.getElementById("barcode").value = '';
            }
        }
    });

    function openGridView()
    {
        document.getElementById("items-table-view").style.display = 'none';
        document.getElementById("items-grid-view").style.display = 'block';

        document.getElementById("grid-view-button").style.backgroundColor = '#6574cd';
        document.getElementById("grid-view-button").style.border = '1px solid #6574cd';

        document.getElementById("table-view-button").style.backgroundColor = '#3490dc';
        document.getElementById("table-view-button").style.border = '1px solid #3490dc';
    }

    function openTableView()
    {
        document.getElementById("items-table-view").style.display = 'block';
        document.getElementById("items-grid-view").style.display = 'none';

        document.getElementById("table-view-button").style.backgroundColor = '#6574cd';
        document.getElementById("table-view-button").style.border = '1px solid #6574cd';

        document.getElementById("grid-view-button").style.backgroundColor = '#3490dc';
        document.getElementById("grid-view-button").style.border = '1px solid #3490dc';
    }

    function addRows(itemId, type = null)
    {
        checkOutData.total = 0;
        checkOutData.subTotal = 0;
        purchaseRows = ``;
        quantityHtml = ``;
        selectedItems.forEach(item => {
            item.quantity = parseFloat(parseFloat(item.quantity).toFixed(2));
            if(type == null && item.id == itemId)
            {
                item.totalPrice = parseFloat((item.quantity * item.price).toFixed(2));
            }

            // checkOutData.total += parseFloat(item.totalPrice);
            checkOutData.subTotal += parseFloat(item.totalPrice);

            if(item.unitId == 1)
            {
                quantityHtml = `    <input type="number" id="quantity` + item.id + `" value="` + parseFloat(item.quantity) + `" class="form-control" oninput="onQuantityInput(event, ` + item.id + `)" required>
                                    <strong class="ml-1">kg</strong>
                                </td>
                                <td width="25%">
                                    <input type="number" id="price` + item.id + `" value="` + parseFloat(item.totalPrice) + `" class="form-control" oninput="onPriceInput(event, ` + item.id + `)" required>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-primary change-quantity-btn" onclick="add1Kg(` + item.id + `)"><span class="quantity-arrow">↑</span> Kg</button><br>
                                    <button type="button" class="btn btn-primary change-quantity-btn" onclick="subtract1Kg(` + item.id + `)"><span class="quantity-arrow">↓</span> Kg</button>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-primary change-quantity-btn" onclick="add1Pao(` + item.id + `)"><span class="quantity-arrow">↑</span> Pao</button>
                                    <button type="button" class="btn btn-primary change-quantity-btn" onclick="subtract1Pao(` + item.id + `)"><span class="quantity-arrow">↓</span> Pao</button>`;
            }
            else if(item.unitId == 2)
            {
                quantityHtml = `    <input type="number" id="quantity` + item.id + `" min="-100000" max="` + item.maxQuantity + `" step="1" value="` + (item.quantity % 1 === 0? item.quantity: item.quantity.toFixed(2)) + `"
                                                onkeypress="return event.charCode >= 48 && event.charCode <= 57" 
                                                class="form-control" oninput="onQuantityInput(event, ` + item.id + `)" required>
                                    <strong id="dozen-quantity` + item.id + `">` + (parseInt(item.quantity / 12) > 0? (parseInt(item.quantity / 12) + ` Dozen ` + (item.quantity % 12 > 0? item.quantity % 12: ``)): ``) + `</strong>
                                </td>
                                <td>
                                    <input type="number" id="price` + item.id + `" value="` + parseFloat(item.totalPrice) + `" class="form-control" oninput="onPriceInput(event, ` + item.id + `)" required>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-primary change-quantity-btn" onclick="add1Dozen(` + item.id + `)"><span class="quantity-arrow">↑</span> Dz</button>
                                    <button type="button" class="btn btn-primary change-quantity-btn" onclick="subtract1Dozen(` + item.id + `)"><span class="quantity-arrow">↓</span> Dz</button>
                                </td>
                                <td> 
                                    <button type="button" class="btn btn-primary change-quantity-btn" onclick="add1Piece(` + item.id + `, ` + 1 + `)"><span class="quantity-arrow">↑</span></button><br>
                                    <button type="button" class="btn btn-primary change-quantity-btn" onclick="subtract1Piece(` + item.id + `, ` + 1 + `)"><span class="quantity-arrow">↓</span></button>`;
            }
            else
            {
                quantityHtml = `    <input type="number" id="quantity` + item.id + `" min="-100000" max="` + item.maxQuantity + `" step="1" value="` + item.quantity + `"
                                                onkeypress="return event.charCode >= 48 && event.charCode <= 57" 
                                                class="form-control" oninput="onQuantityInput(event, ` + item.id + `)" required>
                                </td>
                                <td>
                                    <input type="number" id="price` + item.id + `" value="` + parseFloat(item.totalPrice) + `" class="form-control" oninput="onPriceInput(event, ` + item.id + `)" required>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-primary change-quantity-btn" onclick="add1Piece(` + item.id + `, ` + 0 + `)"><span class="quantity-arrow">↑</span></button><br>
                                    <button type="button" class="btn btn-primary change-quantity-btn" onclick="subtract1Piece(` + item.id + `, ` + 0 + `)"><span class="quantity-arrow">↓</span></button>
                                </td><td>`;
            }

            purchaseRows += `<tr>
                            <td>
                            ` + item.name + `
                            </td>
                            <td width="25%">
                            ` + quantityHtml + `
                            </td>
                            <td>
                            ` + item.totalPrice +
                            `</td>
                            <td class="no-print">
                                <button type="button" class="btn btn-danger delete" onclick="removeItem(` + item.id + `)"><strong>X</strong></button>
                            </td>
                        </tr>`;
        });

        $("#purchases").empty().append(purchaseRows);

        checkOutData.payment = checkOutData.subTotal;
        checkOutData.balance = parseFloat((checkOutData.payment - checkOutData.subTotal).toFixed(2));
        // checkOutData.payment = checkOutData.total;
        // checkOutData.balance = parseFloat((checkOutData.payment - checkOutData.total).toFixed(2));
        // $("#total").val(checkOutData.total);
        $("#subTotal").val(checkOutData.subTotal);
        $("#payment").val(checkOutData.payment);
        $("#balance").val(checkOutData.balance);
    }

    function selectItem(id)
    {
        if(selectedItems.findIndex(item => item.id == id) < 0)
        {
            currentItemId = id;

            let currentItem = allItems.find(item => item.id == id);

            selectedItems.push(
                                {
                                    id: currentItem.id, 
                                    name: currentItem.name, 
                                    originalPrice: currentItem.price, 
                                    price: currentItem.price, 
                                    averageUnitCost: currentItem.average_unit_cost, 
                                    taxType: currentItem.tax_type,
                                    tax: parseFloat(currentItem.tax), 
                                    quantity: 1, 
                                    totalPrice: currentItem.price, 
                                    unitId: currentItem.unit_id, 
                                }
                            );

            document.getElementById("tick-mark" + id).classList.remove("hidden-item");

            addRows(id);

            allItems.find(item => item.id == id).quantity++;
            console.log(allItems.find(item => item.id == id).quantity);
        }
        else
        {
            let item = selectedItems.find(item => item.id == id);

            selectedItems.find(item => item.id == id).quantity++;
            addRows(id);
            
            allItems.find(item => item.id == id).quantity++;
            console.log(allItems.find(item => item.id == id).quantity);
            
            // var quantity = selectedItems.find(item => item.id == id).quantity; 
            // var price = selectedItems.find(item => item.id == id).price;
            // selectedItems.find(item => item.id == id).totalPrice = quantity * price; 
        }
    }

    function removeItem(itemId)
    {
        let quantity = selectedItems.find(item => item.id == itemId).quantity;

        selectedItems.splice(selectedItems.findIndex(item => item.id == itemId), 1);
        
        document.getElementById("tick-mark" + itemId).classList.add("hidden-item");

        addRows(itemId);

        allItems.find(item => item.id == itemId).quantity -= quantity;
        console.log(allItems.find(item => item.id == itemId).quantity);
    }

    function onQuantityInput(event, itemId)
    {
        if(event.target.value != selectedItems.find(item => item.id == itemId).quantity)
        {
            if(event.target.value != '')
            {
                var quantity = selectedItems.find(item => item.id == itemId).quantity;

                selectedItems.find(item => item.id == itemId).quantity = parseFloat(event.target.value);

                allItems.find(item => item.id == itemId).quantity += parseFloat(event.target.value);
            }
            else
            {
                selectedItems.find(item => item.id == itemId).quantity = 0;

                allItems.find(item => item.id == itemId).quantity += 0;
            }
            addRows(itemId);
            console.log(allItems.find(item => item.id == itemId).quantity);

            $("#quantity" + itemId).focus();
            var temp = $("#quantity" + itemId).val();
            $("#quantity" + itemId).val('');
            $("#quantity" + itemId).val(temp);
        }
    }

    function onPriceInput(event, itemId) 
    {
        let item = selectedItems.find(item => item.id == itemId);

        if(event.target.value != '')
        {
            selectedItems.find(item => item.id == itemId).totalPrice = parseFloat(event.target.value);
            selectedItems.find(item => item.id == itemId).quantity = parseFloat((parseFloat(event.target.value)/item.price).toFixed(2));
            // selectedItems.find(item => item.id == itemId).quantity = parseFloat((parseFloat(event.target.value)/item.price).toString().match(/^-?\d+(?:\.\d{0,2})?/)[0]);

            allItems.find(item => item.id == itemId).quantity += selectedItems.find(item => item.id == itemId).quantity; //allitems lines have no function in this page
        }
        else
        {
            selectedItems.find(item => item.id == itemId).totalPrice = 0;
            selectedItems.find(item => item.id == itemId).quantity = 0;

            allItems.find(item => item.id == itemId).quantity += 0
        }

        addRows(itemId, 1);
        console.log(allItems.find(item => item.id == itemId).quantity);

        $("#price" + itemId).focus();
        var temp = $("#price" + itemId).val();
        $("#price" + itemId).val('');
        $("#price" + itemId).val(temp);
    }

    function onPaymentInput()
    {
        if(document.getElementById("payment").value != null && document.getElementById("payment").value != '')
        {
            checkOutData.payment = document.getElementById("payment").value;
        }
        else
        {
            checkOutData.payment = 0;
        }

        checkOutData.balance =  (checkOutData.subTotal - checkOutData.payment) > 0? (checkOutData.subTotal - checkOutData.payment): 0;
        $("#balance").val(checkOutData.balance);
    }

    function add1Pao(itemId)
    {
        let item = selectedItems.find(item => item.id == itemId);
    
        selectedItems.find(item => item.id == itemId).quantity += 0.25;
        addRows(itemId);

        allItems.find(item => item.id == itemId).quantity += 0.25;
        console.log(allItems.find(item => item.id == itemId).quantity);
    }

    function subtract1Pao(itemId)
    {
        var quantity = selectedItems.find(item => item.id == itemId).quantity;
            
        if(quantity - 0.25 >= 0)
        {
            quantity -= 0.25;

            selectedItems.find(item => item.id == itemId).quantity = quantity;

            allItems.find(item => item.id == itemId).quantity -= 0.25;
        }
        else
        {
            selectedItems.find(item => item.id == itemId).quantity = 0;

            allItems.find(item => item.id == itemId).quantity -= 0;
        }

        addRows(itemId);
        console.log(allItems.find(item => item.id == itemId).quantity);
    }

    function add1Kg(itemId)
    {
        let item = selectedItems.find(item => item.id == itemId);

        selectedItems.find(item => item.id == itemId).quantity++;
        addRows(itemId);

        allItems.find(item => item.id == itemId).quantity++;
        console.log(allItems.find(item => item.id == itemId).quantity);
    }

    function subtract1Kg(itemId)
    {
        var quantity = selectedItems.find(item => item.id == itemId).quantity;
        if(quantity - 1 >= 0)
        {
            quantity--;

            selectedItems.find(item => item.id == itemId).quantity = quantity;

            addRows(itemId);

            allItems.find(item => item.id == itemId).quantity--;
            console.log(allItems.find(item => item.id == itemId).quantity);
        }
    }

    function add1Dozen(itemId)
    {
        let item = selectedItems.find(item => item.id == itemId);

        selectedItems.find(item => item.id == itemId).quantity += 12;
        var quantity = selectedItems.find(item => item.id == itemId).quantity;

        var dozenQuantity = 0;
        $("#dozen-quantity" + itemId).html('');
        if(quantity >= 12)
        {
            dozenQuantity = parseInt(quantity / 12) + ' Dozen' + (quantity % 12 > 0? ' ' + quantity % 12: '');
            $("#dozen-quantity" + itemId).html(dozenQuantity);
        }

        addRows(itemId);

        allItems.find(item => item.id == itemId).quantity += 12;
        console.log(allItems.find(item => item.id == itemId).quantity);
    }

    function subtract1Dozen(itemId)
    {
        var quantity = selectedItems.find(item => item.id == itemId).quantity;
        if(quantity - 12 >= 0)
        {
            quantity -= 12;

            selectedItems.find(item => item.id == itemId).quantity = quantity;

            var dozenQuantity = 0;
            $("#dozen-quantity" + itemId).html('');
            if(quantity >= 12)
            {
                dozenQuantity = parseInt(quantity / 12) + ' Dozen' + (quantity % 12 > 0? ' ' + quantity % 12: '');
                $("#dozen-quantity" + itemId).html(dozenQuantity);
            }

            addRows(itemId);

            allItems.find(item => item.id == itemId).quantity -= 12;
            console.log(allItems.find(item => item.id == itemId).quantity);
        }
    }

    function add1Piece(itemId, type)
    {
        let item = selectedItems.find(item => item.id == itemId);

        selectedItems.find(item => item.id == itemId).quantity++;
        var quantity = selectedItems.find(item => item.id == itemId).quantity;

        if(type == 1)
        {
            var dozenQuantity = 0;
            $("#dozen-quantity" + itemId).html('');
            if(quantity >= 12)
            {
                dozenQuantity = parseInt(quantity / 12) + ' Dozen' + (quantity % 12 > 0? ' ' + quantity % 12: '');
                $("#dozen-quantity" + itemId).html(dozenQuantity);
            }
        }

        addRows(itemId);

        allItems.find(item => item.id == itemId).quantity++;
        console.log(allItems.find(item => item.id == itemId).quantity);
    }

    function subtract1Piece(itemId, type)
    {
        var quantity = selectedItems.find(item => item.id == itemId).quantity;
        if(quantity - 1 >= 0)
        {
            quantity--;

            selectedItems.find(item => item.id == itemId).quantity = quantity;

            if(type == 1)
            {
                var dozenQuantity = 0;
                $("#dozen-quantity" + itemId).html('');
                if(quantity >= 12)
                {
                    dozenQuantity = parseInt(quantity / 12) + ' Dozen' + (quantity % 12 > 0? ' ' + quantity % 12: '');
                    $("#dozen-quantity" + itemId).html(dozenQuantity);
                }
            }

            addRows(itemId);

            allItems.find(item => item.id == itemId).quantity--;
            console.log(allItems.find(item => item.id == itemId).quantity);
        }
    }

    function refreshPurchase()
    {
        itemIds = selectedItems.map(item => item.id);

        ItemIdsLength = itemIds.length;
        for(let i = 0; i < ItemIdsLength; i++)
        {
            removeItem(itemIds[i]);
        }

        status = 2;
        type = 2;

        for (let i = 0; i < document.getElementsByName('status').length; i++) 
        {
            if(document.getElementsByName('status')[i].value == 2)
            {
                document.getElementsByName('status')[i].checked = true;
            }
            else
            {
                document.getElementsByName('status')[i].checked = false;
            }
        }

        for (let i = 0; i < document.getElementsByName('type').length; i++) 
        {
            if(document.getElementsByName('type')[i].value == 2)
            {
                document.getElementsByName('type')[i].checked = true;
            }
            else
            {
                document.getElementsByName('type')[i].checked = false;
            }
        }

        document.getElementById("receiving-date").value = '';
        document.getElementById("receiving-time").value = '';

        document.getElementById('not-collected-row').classList.add('d-none');

        checkOutData = {
            payment: 0,
            cash: 0,
            change: 0,
            total: 0,
            subTotal: 0
        }

        selectedItemsLength = 0;
        
        $("#payment").val(null);
        $("#balance").val(null);
        $("#cash").val(null);
        $("#change").val(null);
        $("#total").val(null);
        $("#subTotal").val(null);

        $('#supplier').val('1');
        $('#supplier').trigger('change');
        selectedSupplierId = 1;
        $('#receiving-date').val('');
        $("#third-row").remove();
    }

    function formatAMPM(date) 
    {
        var hours = date.getHours();
        var minutes = date.getMinutes();
        var ampm = hours >= 12 ? 'PM' : 'AM';
        hours = hours % 12;
        hours = hours ? hours : 12; // the hour '0' should be '12'
        minutes = minutes < 10 ? '0'+minutes : minutes;
        var strTime = hours + ':' + minutes + ' ' + ampm;
        return strTime;
    }

    function checkout()
    {
        itemIds = selectedItems.filter(item => item.quantity == 0).map(item => item.id);

        ItemIdsLength = itemIds.length;
        for(let i = 0; i < ItemIdsLength; i++)
        {
            removeItem(itemIds[i]);
        }

        selectedItemsLength = selectedItems.length;

        let receivingDate = status == 1? document.getElementById("receiving-date").value: null;
        let receivingTime = status == 1? document.getElementById("receiving-time").value: null;

        if(selectedItemsLength > 0 && !checkOutLoading && ((status == 1 && receivingDate != null && receivingDate != '' && receivingTime != null && receivingTime != '') || status == 2))
        {
            checkOutData.receiptNumber = Date.now() + "-" + ("{{ auth()->user()->username }}").substr(1, 3);
            let today = new Date();
            let date = (today.getDate() < 10? '0' + today.getDate(): today.getDate()) + '-' + ((today.getMonth()+1) < 10? '0' + (today.getMonth()+1): (today.getMonth()+1)) + '-' + today.getFullYear();
            // let time = today.getHours() + ":" + (today.getMinutes() < 10? '0' + today.getMinutes(): today.getMinutes()) + ":" + (today.getSeconds() < 10? '0' + today.getSeconds(): today.getSeconds());
            let time = formatAMPM(new Date());
            let dateTime = time + ' ' + date;

            checkOutData.date = dateTime;

            checkOutLoading = true;
            $("#loading-text").removeClass('d-none');

            selectedSupplierId = document.getElementById("supplier").value;
            let selectedSupplierName = selectedSupplierId != 1? $('#supplier').select2('data')[0].text.substring(0, $('#supplier').select2('data')[0].text.indexOf('-')): null;

            $.ajax({
                type: 'POST',
                url: "{{ url('/purchase') }}",
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                data: JSON.stringify({
                    purchases: selectedItems,
                    payment: checkOutData.payment,
                    balance: checkOutData.balance,
                    subTotal: checkOutData.subTotal,
                    total: checkOutData.total,
                    receiptNumber: checkOutData.receiptNumber,
                    supplierId: selectedSupplierId,
                    supplierName: selectedSupplierName,
                    status: status,
                    type: type,
                    receivingDate: receivingDate,
                    receivingTime: receivingTime,
                }),
                success: function(response){
                    response = JSON.parse(response);
                    if(response.message == 1)
                    {
                        location.reload();
                        /*selectedItems.forEach(currentItem => {
                            allItems.find(item => item.id == currentItem.id).quantity -= currentItem.quantity;
                        });

                        checkOutLoading = false;
                        $("#loading-text").addClass('d-none');

                        refreshPurchase();*/
                    }
                    else
                    {
                        console.log(response);
                    }
                },
                error: function(response){
                    console.log(response);
                }
            });

            //Removed code from below

            //Removed code from above
        }
    }

    $(document).on('keyup', function(e){
        if(e.which == 13)
            checkout();
        else if(e.key === "Escape")
        {
            closeAddSupplierDialogue();
        }
    });

    function openAddSupplierDialogue() 
    {
        var modal = document.getElementById("add-supplier-modal");

        modal.style.display = "block";
    }

    function closeAddSupplierDialogue()
    {
        var modal = document.getElementById("add-supplier-modal");
        modal.style.display = "none";
    }

    function addSupplier(event)
    {
        event.preventDefault();

        if(document.getElementById("name").value != null)
        {
            $("#overlay").addClass("overlay");
            $("#spinner").addClass("spinner");

            $.ajax({
                type: 'POST',
                url: "{{ url('/supplier') }}",
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                data: {name: document.getElementById("name").value, 
                        phone: document.getElementById("phone").value,
                        address: document.getElementById("address").value,
                        fromAjax: true},
                success: function(response){
                    $("#supplier").append(new Option(document.getElementById("name").value + ' - ' + document.getElementById("phone").value, response, true, true)).trigger('change');
                    
                    $("#overlay").removeClass("overlay");
                    $("#spinner").removeClass("spinner");

                    closeAddSupplierDialogue();
                },
                error: function(response){
                    console.log(response);
                }
            });
        }
    }

    var itemsToPurchase = [];
    $(document).ready(function(){
        $("#overlay").addClass("overlay");
        $("#spinner").addClass("spinner");

        var url = new URL(window.location.href);
        if(url.searchParams.get("items") != null)
        {
            itemsToPurchase = url.searchParams.get("items").split(",");
            console.log(itemsToPurchase);

            window.history.pushState("object or string", "Title", "{{url('/purchase/create')}}");
        }

        $.ajax({
            type: 'GET',
            url: "{{ url('/purchase/create/update-pop-page') }}",
            success: function(response){
                $("#overlay").removeClass("overlay");
                $("#spinner").removeClass("spinner");

                response = JSON.parse(response);
                let suppliers = response.suppliers;
                let categories = response.categories;
                allItems = response.items;

                let supplierOptions = `<option value="1">No supplier</option>`;
                let itemsRows = ``;
                let categoryLinks = ``;
                let tabContents = ``;
                let itemGrids = ``;

                for(let i = 0; i < suppliers.length; i++)
                {
                    supplierOptions += `<option value="` + suppliers[i].id + `">` + suppliers[i].name + ' - ' + suppliers[i].phone + `</option>`;
                }
                $("#supplier").append(supplierOptions);
                $("#supplier").select2();

                for(let i = 0; i < allItems.length; i++)
                {
                    allItems[i].average_unit_cost = parseFloat(allItems[i].average_unit_cost);
                    allItems[i].category_id = parseInt(allItems[i].category_id);
                    allItems[i].id = parseInt(allItems[i].id);
                    allItems[i].itemsCount = parseInt(allItems[i].itemsCount);
                    allItems[i].price = parseFloat(allItems[i].price);
                    allItems[i].quantity = parseFloat(allItems[i].quantity);
                    allItems[i].reorder_level = parseInt(allItems[i].reorder_level);
                    allItems[i].tax = parseFloat(allItems[i].tax);
                    allItems[i].tax_type = parseInt(allItems[i].tax_type);
                    allItems[i].unit_fraction_value = parseFloat(allItems[i].unit_fraction_value);
                    allItems[i].unit_id = parseInt(allItems[i].unit_id);
                    
                    itemsRows += `<tr onclick="selectItem(` + allItems[i].id + `)">
                                        <td>` + allItems[i].name + `</td>
                                </tr>`;
                }
                $("#items-table-body").append(itemsRows);
                $("#items-table").DataTable();

                for(let i = 0; i < categories.length; i++)
                {
                    categoryLinks += `<button class="tablinks" id="category-` + categories[i].name + `" onclick="openTab(event, '` + categories[i].name + `')">` + categories[i].name + `</button>`;
                }
                $("#category-tabs").append(categoryLinks);

                for(let i = 0; i < categories.length; i++)
                {
                    itemGrids += `<div id="` + categories[i].name + `" class="tabcontent" style="height: 500px; overflow: scroll;">
                                            <div class="row sale-item-row">`;
                    for(let j = 0; j < allItems.length; j++)
                    {
                        if(categories[i].id == allItems[j].category_id)
                        {
                            itemGrids += `<div class="col-2 sale-item-col" id="item-col-` + allItems[j].id + `" data-id="` + allItems[j].id + `" onclick="selectItem(` + allItems[j].id + `)">
                                            <a class="sale-item-link" data-id="` + allItems[j].id + `" data-price="` + allItems[j].price + `" data-unit="` + allItems[j].unit_name + `">
                                                <!--<figure class="figure">-->
                                                    <img src="{{ url('/uploads/') . '/' .'` + (allItems[j].image ?? `noimage.png`) + `.'}}" width="120" class="figure-img img-fluid rounded" alt="` + allItems[j].name + `">
                                                    <figcaption class="figure-caption text-center" id="figcaption-` + allItems[j].id + `" data-category="` + categories[i].name + `" data-item="` + allItems[j].name + `" data-id="` + allItems[j].id + `">` + allItems[j].name + `</figcaption>
                                                <!--</figure>-->
                                                <span id="tick-mark` + allItems[j].id + `" class="selection-mark hidden-item">✔</span>
                                            </a>
                                        </div>`;
                        }
                    }
                    itemGrids += `</div>
                                        </div>`;
                }
                $("#category-tabs").after(itemGrids);
                document.getElementsByClassName("tabcontent")[0].style.display = "block";
                document.getElementsByClassName("tablinks")[0].classList.add("active");


                for(let i = 0; i < itemsToPurchase.length; i++)
                {
                    selectItem(itemsToPurchase[i]);
                }
            },
            error: function(response){
                console.log(response);
            }
        });

        var e = $.Event("keydown", { keyCode: 13}); 
        $("body").trigger(e);
    });
</script>
    
@endsection