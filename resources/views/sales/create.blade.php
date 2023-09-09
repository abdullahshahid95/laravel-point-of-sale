@extends('master')

@section('content')
<div id="spinner" class=""></div>
<div id="overlay" class=""></div>

<div id="my-jumbotron" class="jumbotron">
    <div class="row">
        <div class="col-2 pr-0">
            <a class="btn btn-success" onclick="refreshSale()">Refresh</a>
            <a href="#" style="font-size: 0.9em; font-weight: bold; margin-left: 9%; display: inline-block;" onclick="openCustomerHistoryDialogue()"><u>Customer History</u></a>
        </div>
        <div class="col-3">
            <div class="row">
                <div class="col-12">
                    <select id="customer" class="form-control" style="width: 90%;">
                        {{-- Customers options appear here --}}
                    </select>
                    <button type="button" class="btn btn-primary pt-0 pb-1 pl-1 pr-1" onclick="openAddCustomerDialogue()" title="Add customer">+</button>
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
                    <label for="received">Collected</label>
                    |
                    <input type="radio" id="not-received" name="status" value="1">
                    <label for="not-received">Not Collected</label>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <input type="radio" id="take-away" name="type" value="2" checked>
                    <label for="take-away">Take Away</label>
                    |
                    <input type="radio" id="home-delivery" name="type" value="1">
                    <label for="home-delivery">Home Delivery</label>
                    |
                    <input type="radio" id="dine-in" name="type" value="3">
                    <label for="dine-in">Dine-In</label>
                </div>
            </div>
        </div>
        <div class="col-4">
            <a class="btn btn-success float-right" href="{{ url('/sale/edit') }}">Exchange/Return</a>
            <a class="btn btn-secondary float-right mr-1" href="#" onclick="openDraftOrdersDialogue()">Draft</a>
        </div>
    </div>
    <br>
    <div class="row">
        <div class="col-12">
            <div class="d-inline-block ml-5 bg-secondary text-white pl-1 pr-1 float-right">
                <input type="checkbox" id="toggle-print" checked />
                <label for="toggle-print">Print</label>
                |
                <input type="checkbox" id="toggle-drawer" checked />
                <label for="toggle-drawer">Open Drawer</label>
            </div>
        </div>
    </div>
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
                        <label for="total">S.Total</label>
                        <input type="text" id="total" name="total" class="form-control" readonly>
                    </div>
                    <div class="col-3 payment-fields-cols">
                        <label for="cash">Cash</label>
                        <input type="number" min="1" id="cash" class="form-control"  name="cash" oninput="onCashInput()" required/>
                    </div>
                    <div class="col-3 payment-fields-cols">
                        <label for="payment">Payment</label>
                        <input type="number" min="-10000000" id="payment" class="form-control"  name="payment" oninput="onPaymentInput()" required/>
                    </div>
                    <div class="col-3 pt-0 pb-0 border-top border-bottom">
                        <select id="number-of-copies" class="form-control mb-1" style="width:100%;">
                            {{-- <option value="0">Copies</option> --}}
                            <option value="1" selected>1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="4">4</option>
                            <option value="5">5</option>
                        </select>
                        <button type="button" id="print-button" class="submitBtn no-print btn btn-primary float-right" onclick="checkout()">
                            <svg width="2em" height="2em" viewBox="0 0 16 16" class="bi bi-printer-fill" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                <path d="M5 1a2 2 0 0 0-2 2v1h10V3a2 2 0 0 0-2-2H5z"/>
                                <path fill-rule="evenodd" d="M11 9H5a1 1 0 0 0-1 1v3a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1v-3a1 1 0 0 0-1-1z"/>
                                <path fill-rule="evenodd" d="M0 7a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2h-1v-2a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v2H2a2 2 0 0 1-2-2V7zm2.5 1a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1z"/>
                            </svg>
                        </button>
                        <small class="float-right d-none" id="loading-text">
                            Saving <?xml version="1.0" encoding="UTF-8" standalone="no"?><svg xmlns:svg="http://www.w3.org/2000/svg" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.0" width="20%" height="20%" viewBox="0 0 128 128" xml:space="preserve"><g><path d="M78.75 16.18V1.56a64.1 64.1 0 0 1 47.7 47.7H111.8a49.98 49.98 0 0 0-33.07-33.08zM16.43 49.25H1.8a64.1 64.1 0 0 1 47.7-47.7V16.2a49.98 49.98 0 0 0-33.07 33.07zm33.07 62.32v14.62A64.1 64.1 0 0 1 1.8 78.5h14.63a49.98 49.98 0 0 0 33.07 33.07zm62.32-33.07h14.62a64.1 64.1 0 0 1-47.7 47.7v-14.63a49.98 49.98 0 0 0 33.08-33.07z" fill="#000000" fill-opacity="1"/><animateTransform attributeName="transform" type="rotate" from="0 64 64" to="-90 64 64" dur="400ms" repeatCount="indefinite"></animateTransform></g></svg>
                        </small>
                    </div>
                </div>
                <div class="row">
                    <div class="col-3 payment-fields-cols">
                        <label for="subTotal">Total</label>
                        <input type="number" id="subTotal" class="form-control" value="" readonly disabled>
                    </div>
                    <div class="col-3 payment-fields-cols">
                        <label for="change">Change</label>
                        <input type="number" id="change" class="form-control" readonly disabled>
                    </div>
                    <div class="col-3 payment-fields-cols">
                        <label for="balance">Balance</label>
                        <input type="number" id="balance" class="form-control" readonly disabled>
                    </div>
                    <div class="col-3 payment-fields-cols text-center">
                        <span>
                            Discount
                            <br>
                            <button type="button" id="discount-button" class="btn btn-primary" onclick="openDiscountDialogue()"></button>
                        </span>
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
                            <tbody id="sales">

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>   
    </div>
</div>

<!-- Add Customer Modal -->
<div id="add-customer-modal" class="add-customer-modal">
    <!-- Modal content -->
    <div class="add-customer-modal-content">
        <div class="row">
            <div class="col-6">
                <h3>Add Customer</h3>
            </div>
            <div class="col-6">
                <span id="add-customer-modal-close" class="add-customer-modal-close" onclick="closeAddCustomerDialogue()">&times;</span>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="form-group col-12">
                <div class="col-6 pl-5">
                    <form id="add-customer-form" action="#" onsubmit="addCustomer(event)">
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

<!-- Customer History Modal -->
<div id="customer-history-modal" class="add-customer-modal">
    <!-- Modal content -->
    <div class="add-customer-modal-content">
        <div class="row">
            <div class="col-6">
                <h3>Customer History</h3>
            </div>
            <div class="col-6">
                <span id="customer-history-modal-close" class="add-customer-modal-close" onclick="closeCustomerHistoryDialogue()">&times;</span>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-4">
                <input type="text" class="form-control" placeholder="Name or Number" id="name-or-number" oninput="searchCustomer()">
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <table class="table table-bordered" id="searched-customer-table" style="width: 100%;">
                    <thead>
                        <th>
                            Name
                        </th>
                        <th>
                            Phone
                        </th>
                        <th>
                            Receipt No.
                        </th>
                        <th>
                            Total
                        </th>
                        <th>
                            Date
                        </th>
                    </thead>
                    <tbody id="searched-customer-rows">
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Draft Orders Modal -->
<div id="draft-orders-modal" class="add-customer-modal">
    <!-- Modal content -->
    <div class="add-customer-modal-content">
        <div class="row">
            <div class="col-6">
                <h3>Draft Orders</h3>
            </div>
            <div class="col-6">
                <span id="draft-orders-modal-close" class="add-customer-modal-close" onclick="closeDraftOrdersDialogue()">&times;</span>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-4">
                <input type="text" class="form-control" placeholder="Order ID/Invoice No." id="order-id" oninput="searchDraftOrder()">
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <table class="table table-bordered" id="draft-orders-table" style="width: 100%;">
                    <thead>
                        <th>
                            Receipt No.
                        </th>
                        <th>
                            Total
                        </th>
                        <th>
                            Discount
                        </th>
                        <th>
                            Sub Total
                        </th>
                        <th>
                            Received
                        </th>
                        <th>
                            Receivable
                        </th>
                        <th>
                            Date
                        </th>
                        <th>
                            Complete
                        </th>
                    </thead>
                    <tbody id="draft-order-rows">
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Bill discount Modal -->
<div id="discount-modal" class="discount-modal">
    <!-- Modal content -->
    <div class="discount-modal-content">
        <div class="row">
            <div class="col-6">
                <h3>Discounts</h3>
            </div>
            <div class="col-6">
                <span id="discount-modal-close" class="discount-modal-close" onclick="closeDiscountDialogue()">&times;</span>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <label for="discount">Bill Discount</label>
                <input type="number" min="0" value="" id="discount" name="discount" class="form-control">
                <input type="radio" id="discount-percentage" name="discount_type" value="1">
                <label for="discount-percentage">%</label>
                |
                <input type="radio" id="discount-amount" name="discount_type" value="2" checked>
                <label for="discount-amount">Amnt.</label>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-5">
                <button type="button" id="save-discount-button" class="btn btn-primary" onclick="closeDiscountDialogue(1)">Save</button>
                <button type="button" id="cancel-discount-button" class="btn btn-danger" onclick="closeDiscountDialogue()">Cancel</button>
            </div>
        </div>
    </div>
</div>

<!-- Item discount Modal -->
<div id="item-discount-modal" class="item-discount-modal">
    <!-- Modal content -->
    <div class="item-discount-modal-content">
        <div class="row">
            <div class="col-6">
                <h3>Item Discount</h3>
            </div>
            <div class="col-6">
                <span id="item-discount-modal-close" class="item-discount-modal-close" onclick="closeItemDiscountDialogue()">&times;</span>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <label for="item-discount" id="item-discount-label"></label>
                <input type="number" min="0" value="" id="item-discount" class="form-control">
                <input type="radio" id="item-discount-percentage" name="item_discount_type" value="1">
                <label for="item-discount-percentage">%</label>
                |
                <input type="radio" id="item-discount-amount" name="item_discount_type" value="2" checked>
                <label for="item-discount-amount">Amnt.</label>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-5">
                <button type="button" id="save-item-discount-button" class="btn btn-primary" onclick="">Save</button>
                <button type="button" id="cancel-item-discount-button" class="btn btn-danger" onclick="closeItemDiscountDialogue()">Cancel</button>
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
        discountType: 2,
        discount: 0,
        total: 0,
        subTotal: 0,
        payment: 0,
        cash: 0,
        change: 0,
        date: null,
        receiptNumber: null
    }
    var selectedCustomerId = 1;
    var currentItemId = 0;
    var saleRows = ``;
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
            if(allItems.findIndex(item => item.label == barcode) > -1)
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
        saleRows = ``;
        quantityHtml = ``;
        selectedItems.forEach(item => {
            item.quantity = parseFloat(parseFloat(item.quantity).toFixed(2));
            if(type == null && item.id == itemId)
            {
                item.totalPrice = parseFloat((item.quantity * item.price).toFixed(2));
                item.actualDiscountAmount = parseFloat(((item.originalPrice - item.price) * item.quantity).toFixed(2));
            }

            checkOutData.total += parseFloat(item.totalPrice);

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
                                <td></td>
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
                                <td></td>
                                <td>
                                    <button type="button" class="btn btn-primary change-quantity-btn" onclick="add1Piece(` + item.id + `, ` + 0 + `)"><span class="quantity-arrow">↑</span></button><br>
                                    <button type="button" class="btn btn-primary change-quantity-btn" onclick="subtract1Piece(` + item.id + `, ` + 0 + `)"><span class="quantity-arrow">↓</span></button>
                                </td><td>`;
            }

            let discountText = ``;
            if(item.discount > 0)
            {
                discountText = `<br><button type='button' class='btn badge btn-success' onclick='openItemDiscountDialogue(` + item.id + `)'>Disc.<br>` + (item.discountType == 1? item.discount + `%`: item.discount) + `</button>`;
            }
            else
            {
                discountText = `<br><button type='button' class='btn badge btn-warning' onclick='openItemDiscountDialogue(` + item.id + `)'>Add<br>Disc.</button>`;
            }

            saleRows += `<tr>
                            <td>
                            ` + item.name + `
                            </td>
                            <td width="25%">
                            ` + quantityHtml + `
                            </td>
                            <td>
                            ` + item.totalPrice + discountText +
                            `</td>
                            <td class="no-print">
                                <button type="button" class="btn btn-danger delete" onclick="removeItem(` + item.id + `)"><strong>X</strong></button>
                            </td>
                        </tr>`;
        });

        $("#sales").empty().append(saleRows);

        if(checkOutData.discountType == 2)
        {
            checkOutData.subTotal = checkOutData.total - checkOutData.discount;
        }
        else
        {
            let amount = checkOutData.total * (checkOutData.discount / 100);
            checkOutData.subTotal = parseFloat((checkOutData.total - amount).toFixed(2));
        }
        // checkOutData.subTotal = parseFloat((checkOutData.total - checkOutData.discount).toFixed(2)) > 0? parseFloat((checkOutData.total - checkOutData.discount).toFixed(2)): 0;
        checkOutData.payment = checkOutData.subTotal;
        checkOutData.balance = parseFloat((checkOutData.payment - checkOutData.subTotal).toFixed(2));
        $("#total").val(checkOutData.total);
        $("#subTotal").val(checkOutData.subTotal);
        $("#payment").val(checkOutData.payment);
        $("#balance").val(checkOutData.balance);

        onCashInput();
    }

    function selectItem(id)
    {
        if(selectedItems.findIndex(item => item.id == id) < 0)
        {
            currentItemId = id;

            let currentItem = allItems.find(item => item.id == id);
            let discountedPrice = parseFloat((currentItem.discount_type == 1? currentItem.price - (currentItem.price * (currentItem.discount/100)): currentItem.price - currentItem.discount).toFixed(2));

            selectedItems.push(
                                {
                                    id: currentItem.id, 
                                    name: currentItem.name, 
                                    originalPrice: currentItem.price, 
                                    price: discountedPrice, 
                                    actualDiscountAmount: parseFloat((currentItem.price - discountedPrice).toFixed(2)), 
                                    averageUnitCost: currentItem.average_unit_cost, 
                                    discountType: currentItem.discount_type, 
                                    discount: parseFloat(currentItem.discount), 
                                    taxType: currentItem.tax_type, 
                                    tax: parseFloat(currentItem.tax), 
                                    quantity: (currentItem.quantity >= 1? 1: currentItem.quantity), 
                                    totalPrice: discountedPrice, 
                                    unitId: currentItem.unit_id, 
                                    maxQuantity: currentItem.quantity
                                }
                            );

            document.getElementById("tick-mark" + id).classList.remove("hidden-item");

            addRows(id);

            allItems.find(item => item.id == id).quantity--;
            console.log(allItems.find(item => item.id == id).quantity);
        }
        else
        {
            let item = selectedItems.find(item => item.id == id);

            if(item.quantity + 1 <= item.maxQuantity)
            {
                selectedItems.find(item => item.id == id).quantity++
                addRows(id);
                
                allItems.find(item => item.id == id).quantity--;
                console.log(allItems.find(item => item.id == id).quantity);
            }
            else
                alert("Maximum quantity allowed: " + item.maxQuantity);
            
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

        allItems.find(item => item.id == itemId).quantity += quantity;
        console.log(allItems.find(item => item.id == itemId).quantity);

        onDiscountInput();
        onCashInput();
    }

    function onQuantityInput(event, itemId)
    {
        if(event.target.value != selectedItems.find(item => item.id == itemId).quantity)
        {
            if(event.target.value != '')
            {
                var quantity = selectedItems.find(item => item.id == itemId).quantity;

                if(parseFloat(event.target.value) <= selectedItems.find(item => item.id == itemId).maxQuantity)
                {
                    selectedItems.find(item => item.id == itemId).quantity = parseFloat(event.target.value);

                    allItems.find(item => item.id == itemId).quantity = selectedItems.find(item => item.id == itemId).maxQuantity - parseFloat(event.target.value);
                }
                else
                {
                    selectedItems.find(item => item.id == itemId).quantity = parseFloat(selectedItems.find(item => item.id == itemId).maxQuantity);

                    allItems.find(item => item.id == itemId).quantity = 0;
                }
            }
            else
            {
                selectedItems.find(item => item.id == itemId).quantity = 0;

                allItems.find(item => item.id == itemId).quantity = parseFloat(selectedItems.find(item => item.id == itemId).maxQuantity);
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
            if(parseFloat(event.target.value)/item.price <= item.maxQuantity)
            {
                selectedItems.find(item => item.id == itemId).totalPrice = parseFloat(event.target.value);
                selectedItems.find(item => item.id == itemId).quantity = parseFloat((parseFloat(event.target.value)/item.price).toFixed(2));
                // selectedItems.find(item => item.id == itemId).quantity = parseFloat((parseFloat(event.target.value)/item.price).toString().match(/^-?\d+(?:\.\d{0,2})?/)[0]);

                allItems.find(item => item.id == itemId).quantity = (selectedItems.find(item => item.id == itemId).maxQuantity - parseFloat((parseFloat(event.target.value)/item.price).toFixed(2))).toFixed(2);
            }
            else
            {
                alert("Max price allowed: " + item.price * item.maxQuantity);
            }
        }
        else
        {
            selectedItems.find(item => item.id == itemId).totalPrice = 0;
            selectedItems.find(item => item.id == itemId).quantity = 0;

            allItems.find(item => item.id == itemId).quantity = selectedItems.find(item => item.id == itemId).maxQuantity;
        }

        addRows(itemId, 1);
        console.log(allItems.find(item => item.id == itemId).quantity);

        $("#price" + itemId).focus();
        var temp = $("#price" + itemId).val();
        $("#price" + itemId).val('');
        $("#price" + itemId).val(temp);
    }

    function onDiscountInput()
    {
        checkOutData.discountType = document.getElementById("discount-percentage").checked? 1: 2;

        if(parseFloat(document.getElementById("discount").value) > 0)
        {
            checkOutData.discount = parseFloat(document.getElementById("discount").value);

        }
        else
        {
            checkOutData.discount = 0;
        }

        if(checkOutData.discountType == 2)
        {
            document.getElementById("discount-button").innerHTML = checkOutData.discount;

            checkOutData.subTotal = checkOutData.total - checkOutData.discount;
        }
        else
        {
            document.getElementById("discount-button").innerHTML = checkOutData.discount + '%';

            let amount = checkOutData.total * (checkOutData.discount / 100);
            checkOutData.subTotal = parseFloat((checkOutData.total - amount).toFixed(2));
        }

        $("#subTotal").val(checkOutData.subTotal);

        if(document.getElementById("payment").value != null && document.getElementById("payment").value != '')
        {
            checkOutData.payment = checkOutData.subTotal;
            $("#payment").val(checkOutData.payment);
            // checkOutData.payment = document.getElementById("payment").value;
        }
        else
        {
            checkOutData.payment = 0;
        }

        checkOutData.balance =  (checkOutData.subTotal - checkOutData.payment) > 0? (checkOutData.subTotal - checkOutData.payment): 0;
        $("#balance").val(checkOutData.balance);   

        if(parseFloat(document.getElementById("cash").value) >= checkOutData.payment)
        {
            checkOutData.cash = document.getElementById("cash").value;
        }
        else
        {
            checkOutData.cash = checkOutData.payment;
        }

        checkOutData.change = (checkOutData.cash - checkOutData.payment) > 0? (checkOutData.cash - checkOutData.payment): 0;
        $("#change").val(checkOutData.change);     
    }

    function onCashInput()
    {
        if(checkOutData.payment > 0)
        {
            if(parseFloat(document.getElementById("cash").value) >= checkOutData.payment)
            {
                checkOutData.cash = document.getElementById("cash").value;
            }
            else
            {
                checkOutData.cash = checkOutData.payment;
            }
        }
        else
        {
            checkOutData.cash = 0;
        }

        checkOutData.change = (checkOutData.cash - checkOutData.payment) > 0? (checkOutData.cash - checkOutData.payment): 0;
        $("#change").val(checkOutData.change);
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

        onCashInput();
    }

    function add1Pao(itemId)
    {
        let item = selectedItems.find(item => item.id == itemId);
        if(item.quantity + 0.25 <= item.maxQuantity)
        {
            selectedItems.find(item => item.id == itemId).quantity += 0.25;
            addRows(itemId);

            allItems.find(item => item.id == itemId).quantity -= 0.25;
            console.log(allItems.find(item => item.id == itemId).quantity);
        }
        else
            alert("Maximum quantity allowed: " + item.maxQuantity);
    }

    function subtract1Pao(itemId)
    {
        var quantity = selectedItems.find(item => item.id == itemId).quantity;
            
        if(quantity - 0.25 >= 0)
        {
            quantity -= 0.25;

            selectedItems.find(item => item.id == itemId).quantity = quantity;

            allItems.find(item => item.id == itemId).quantity += 0.25;
        }
        else
        {
            selectedItems.find(item => item.id == itemId).quantity = 0;

            allItems.find(item => item.id == itemId).quantity = selectedItems.find(item => item.id == itemId).maxQuantity;
        }

        addRows(itemId);
        console.log(allItems.find(item => item.id == itemId).quantity);
    }

    function add1Kg(itemId)
    {
        let item = selectedItems.find(item => item.id == itemId);

        if(item.quantity + 1 <= item.maxQuantity)
        {
            selectedItems.find(item => item.id == itemId).quantity++;
            addRows(itemId);

            allItems.find(item => item.id == itemId).quantity--;
            console.log(allItems.find(item => item.id == itemId).quantity);
        }
        else
            alert("Maximum quantity allowed: " + item.maxQuantity);
    }

    function subtract1Kg(itemId)
    {
        var quantity = selectedItems.find(item => item.id == itemId).quantity;
        if(quantity - 1 >= 0)
        {
            quantity--;

            selectedItems.find(item => item.id == itemId).quantity = quantity;

            addRows(itemId);

            allItems.find(item => item.id == itemId).quantity++;
            console.log(allItems.find(item => item.id == itemId).quantity);
        }
    }

    function add1Dozen(itemId)
    {
        let item = selectedItems.find(item => item.id == itemId);

        if(item.quantity + 12 <= item.maxQuantity)
        {
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

            allItems.find(item => item.id == itemId).quantity -= 12;
            console.log(allItems.find(item => item.id == itemId).quantity);
        }
        else
            alert("Maximum quantity allowed: " + item.maxQuantity);
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

            allItems.find(item => item.id == itemId).quantity += 12;
            console.log(allItems.find(item => item.id == itemId).quantity);
        }
    }

    function add1Piece(itemId, type)
    {
        let item = selectedItems.find(item => item.id == itemId);

        if(item.quantity + 1 <= item.maxQuantity)
        {
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

            allItems.find(item => item.id == itemId).quantity--;
            console.log(allItems.find(item => item.id == itemId).quantity);
        }
        else
            alert("Maximum quantity allowed: " + item.maxQuantity);
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

            allItems.find(item => item.id == itemId).quantity++;
            console.log(allItems.find(item => item.id == itemId).quantity);
        }
    }

    function refreshSale()
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

        document.getElementById("number-of-copies").value = 1;

        checkOutData = {
            discountType: 2,
            discount: 0,
            payment: 0,
            cash: 0,
            change: 0,
            total: 0,
            subTotal: 0
        }

        selectedItemsLength = 0;
        
        $("#discount").val(0);
        document.getElementById("discount-button").innerHTML = 0

        $("#payment").val(null);
        $("#balance").val(null);
        $("#cash").val(null);
        $("#change").val(null);
        $("#total").val(null);
        $("#subTotal").val(null);

        $('#customer').val('1');
        $('#customer').trigger('change');
        selectedCustomerId = 1;
        $('#receiving-date').val('');
        $("#third-row").remove();
        $("#order-discount-row").remove();
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

            selectedCustomerId = document.getElementById("customer").value;
            let selectedCustomerName = selectedCustomerId != 1? $('#customer').select2('data')[0].text.substring(0, $('#customer').select2('data')[0].text.indexOf('-')): null;

            $.ajax({
                type: 'POST',
                url: "{{ url('/sale') }}",
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                data: JSON.stringify({
                    sales: selectedItems,
                    cash: checkOutData.cash == 0? checkOutData.payment: checkOutData.cash,
                    change: checkOutData.change,
                    discount: checkOutData.discount,
                    discountType: checkOutData.discountType,
                    actualDiscountAmount: checkOutData.discount > 0? 
                        (parseFloat((checkOutData.discountType == 1? (checkOutData.total * (checkOutData.discount/100)): 
                                                                            checkOutData.discount).toFixed(2))): 0,
                    payment: checkOutData.payment,
                    balance: checkOutData.balance,
                    subTotal: checkOutData.subTotal,
                    total: checkOutData.total,
                    receiptNumber: checkOutData.receiptNumber,
                    customerId: selectedCustomerId,
                    customerName: selectedCustomerName,
                    status: status,
                    type: type,
                    receivingDate: receivingDate,
                    receivingTime: receivingTime,
                    togglePrint: document.getElementById("toggle-print").checked,
                    toggleDrawer: document.getElementById("toggle-drawer").checked,
                    numberOfCopies: document.getElementById("number-of-copies").value
                }),
                success: function(response){
                    response = JSON.parse(response);
                    if(response.message == 1)
                    {
                        selectedItems.forEach(currentItem => {
                            allItems.find(item => item.id == currentItem.id).quantity -= currentItem.quantity;
                        });

                        checkOutLoading = false;
                        $("#loading-text").addClass('d-none');

                        refreshSale();
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
        {
            checkout();
        }
        else if(e.key === "Escape")
        {
            closeAddCustomerDialogue();
            closeCustomerHistoryDialogue()
            closeDraftOrdersDialogue()
        }
    });

    function openAddCustomerDialogue() 
    {
        var modal = document.getElementById("add-customer-modal");

        modal.style.display = "block";
    }

    function closeAddCustomerDialogue()
    {
        var modal = document.getElementById("add-customer-modal");
        modal.style.display = "none";
    }

    function openDiscountDialogue() 
    {
        var modal = document.getElementById("discount-modal");

        modal.style.display = "block";

        document.getElementById("discount").value = checkOutData.discount;

        if(checkOutData.discountType == 2)
            document.getElementById("discount-amount").checked = true;
        else
            document.getElementById("discount-percentage").checked = true;
    }

    function closeDiscountDialogue(type = 2)
    {
        if(type == 1)
        {
            onDiscountInput();
        }

        var modal = document.getElementById("discount-modal");
        modal.style.display = "none";
    }

    function openItemDiscountDialogue(id) 
    {
        var modal = document.getElementById("item-discount-modal");

        modal.style.display = "block";

        let item = selectedItems.find(item => item.id == id);
        
        if(item.discount == '' || item.discount == null) {
            item.discount = 0;
        }
        document.getElementById("item-discount").value = item.discount;
        document.getElementById("item-discount-label").innerHTML = item.name;
        document.getElementById("save-item-discount-button").setAttribute('onclick', 'closeItemDiscountDialogue(' + id + ',1)');

        if(item.discountType == 2)
            document.getElementById("item-discount-amount").checked = true;
        else
            document.getElementById("item-discount-percentage").checked = true;
    }

    function closeItemDiscountDialogue(id = 0, type = 2)
    {
        if(type == 1)
        {
            let discount = document.getElementById("item-discount").value;
            let discountType = document.getElementById("item-discount-percentage").checked? 1: 2;

            if(discount == '' || discount == null) {
                discount = 0;
            }

            selectedItems.find(item => item.id == id).discount = discount;
            selectedItems.find(item => item.id == id).discountType = discountType;

            let originalPrice = selectedItems.find(item => item.id == id).originalPrice;
            selectedItems.find(item => item.id == id).price = parseFloat((discountType == 1? originalPrice - (originalPrice * (discount/100)): originalPrice - discount).toFixed(2));

            addRows(id);
        }

        var modal = document.getElementById("item-discount-modal");
        modal.style.display = "none";
    }

    function addCustomer(event)
    {
        event.preventDefault();

        if(document.getElementById("name").value != null)
        {
            $("#overlay").addClass("overlay");
            $("#spinner").addClass("spinner");

            $.ajax({
                type: 'POST',
                url: "{{ url('/customer') }}",
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                data: {name: document.getElementById("name").value, 
                        phone: document.getElementById("phone").value,
                        address: document.getElementById("address").value,
                        fromAjax: true},
                success: function(response){
                    $("#customer").append(new Option(document.getElementById("name").value + ' - ' + document.getElementById("phone").value, response, true, true)).trigger('change');
                    
                    $("#overlay").removeClass("overlay");
                    $("#spinner").removeClass("spinner");

                    closeAddCustomerDialogue();
                },
                error: function(response){
                    console.log(response);
                }
            });
        }
    }

    function openCustomerHistoryDialogue()
    {
        // $("#overlay").addClass("overlay");
        // $("#spinner").addClass("spinner");

        var modal = document.getElementById("customer-history-modal");

        modal.style.display = "block";
    }

    var searchedCustomers = [];
    var datatable = null;

    function searchCustomer()
    {
        var text = document.getElementById("name-or-number").value;

        if(text != null && text != '')
        {
            $.ajax({
                type: 'GET',
                url: "{{ url('/sale/get/customers') }}/" + text,
                success: function(response){
                    response = JSON.parse(response);
                    searchedCustomers = response;
                    console.clear();
                    for(let i = 0; i < searchedCustomers.length; i++) 
                    {
                        searchedCustomers[i] = Object.values(searchedCustomers[i]);
                    }
                    console.log(searchedCustomers);

                    datatable.clear().draw();
                    datatable.rows.add(searchedCustomers); // Add new data
                    datatable.columns.adjust().draw(); // Redraw the DataTable

                    /*let customers = response;

                    console.clear();
                    console.log(customers);

                    let rows = "";

                    for (let i = 0; i < customers.length; i++)
                    {
                        rows += `<tr>
                                    <td>` + customers[i].name + `</td>
                                    <td>` + customers[i].phone + `</td>
                                    <td>` + customers[i].receipt_number + `</td>
                                    <td>` + customers[i].sub_total + `</td>
                                    <td>` + customers[i].order_date + `</td>
                                </tr>`;                    
                    }

                    $("#searched-customer-rows").empty().append(rows);*/
                    // $("#searched-customer-table").DataTable();
                },
                error: function(response){
                    console.log(response);
                }
            });
        }
        else
        {
            $("#searched-customer-rows").empty();
        }
    }

    function closeCustomerHistoryDialogue()
    {
        datatable.clear().draw();
        datatable.columns.adjust().draw(); // Redraw the DataTable
        $("#name-or-number").val('');

        var modal = document.getElementById("customer-history-modal");

        modal.style.display = "none";
    }

    function openDraftOrdersDialogue()
    {
        var modal = document.getElementById("draft-orders-modal");

        modal.style.display = "block";

        searchDraftOrder();
    }

    var draftOrders = [];
    var draftOrderstable = null;

    function searchDraftOrder()
    {
        var text = document.getElementById("order-id").value;

        $.ajax({
            type: 'GET',
            url: "{{ url('/draft-order/get') }}" + ((text.trim() != null && text.trim() != '')? ('/' + text): ''),
            success: function(response){
                response = JSON.parse(response);
                draftOrders = response;
                console.clear();
                for(let i = 0; i < draftOrders.length; i++) 
                {
                    draftOrders[i].total = parseFloat(draftOrders[i].total);
                    draftOrders[i].discount_amount = parseFloat(draftOrders[i].discount_amount);
                    draftOrders[i].sub_total = parseFloat(draftOrders[i].sub_total);
                    draftOrders[i].payment = parseFloat(draftOrders[i].payment);
                    draftOrders[i].receivable = parseFloat(draftOrders[i].receivable);
                    draftOrders[i] = Object.values(draftOrders[i]);
                }
                console.log(draftOrders);

                draftOrderstable.clear().draw();
                draftOrderstable.rows.add(draftOrders); // Add new data
                draftOrderstable.columns.adjust().draw(); // Redraw the DataTable
            },
            error: function(response){
                console.log(response);
            }
        });
    }

    function markOrderomplete(orderId)
    {
        var markComplete = confirm('Complete order?');

        if(markComplete)
        {
            $("#overlay").addClass("overlay");
            $("#spinner").addClass("spinner");

            $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: "{{ url('/order/collect') }}/" + orderId,
                    type: 'PUT',
                    success: function(response){
                        if(response == 1)
                        {
                            alert('Status updated');
                            searchDraftOrder();

                            $("#overlay").removeClass("overlay");
                            $("#spinner").removeClass("spinner");
                        }
                        else
                            console.log(response);
                    },
                    error: function(response){
                        console.log(response);
                    }
            });
        }
    }

    function closeDraftOrdersDialogue()
    {
        draftOrders = [];
        draftOrderstable.clear().draw();
        draftOrderstable.columns.adjust().draw(); // Redraw the DataTable
        $("#order-id").val('');

        var modal = document.getElementById("draft-orders-modal");

        modal.style.display = "none";
    }

    $(document).ready(function(){
        $("#overlay").addClass("overlay");
        $("#spinner").addClass("spinner");

        document.getElementById("discount-button").innerHTML = checkOutData.discount;

        $.ajax({
            type: 'GET',
            url: "{{ url('/sale/create/update-pos-page') }}",
            success: function(response){
                $("#overlay").removeClass("overlay");
                $("#spinner").removeClass("spinner");

                response = JSON.parse(response);
                let customers = response.customers;
                let categories = response.categories;
                allItems = response.items;

                let customerOptions = `<option value="1">Walk-in customer</option>`;
                let itemsRows = ``;
                let categoryLinks = ``;
                let tabContents = ``;
                let itemGrids = ``;

                for(let i = 0; i < customers.length; i++)
                {
                    customerOptions += `<option value="` + customers[i].id + `">` + customers[i].name + ' - ' + customers[i].phone + `</option>`;
                }
                $("#customer").append(customerOptions);
                $("#customer").select2();

                for(let i = 0; i < allItems.length; i++)
                {
                    allItems[i].average_unit_cost = parseFloat(allItems[i].average_unit_cost);
                    allItems[i].category_id = parseInt(allItems[i].category_id);
                    allItems[i].discount = parseFloat(allItems[i].discount);
                    allItems[i].discount_type = parseInt(allItems[i].discount_type);
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
                            /*itemGrids += `<div class="col-2 sale-item-col" id="item-col-` + allItems[j].id + `" data-id="` + allItems[j].id + `" onclick="selectItem(` + allItems[j].id + `)">
                                            <a class="sale-item-link" data-id="` + allItems[j].id + `" data-price="` + allItems[j].price + `" data-unit="` + allItems[j].unit_name + `">
                                                <figure class="figure">
                                                    <img src="{{ url('/uploads/') . '/' .'` + (allItems[j].image ?? `noimage.png`) + `.'}}" width="120" class="figure-img img-fluid rounded" alt="` + allItems[j].name + `">
                                                    <figcaption class="figure-caption text-center" id="figcaption-` + allItems[j].id + `" data-category="` + categories[i].name + `" data-item="` + allItems[j].name + `" data-id="` + allItems[j].id + `">` + allItems[j].name + ` <span id="tick-mark` + allItems[j].id + `" class="selection-mark hidden-item">✔</span></figcaption>
                                                </figure>
                                            </a>
                                        </div>`;*/


                            itemGrids += `<div class="col-sm-2 sale-item-col" id="item-col-` + allItems[j].id + `" data-id="` + allItems[j].id + `" onclick="selectItem(` + allItems[j].id + `)">
                                                <a class="sale-item-link" data-id="` + allItems[j].id + `" data-price="` + allItems[j].price + `" data-unit="` + allItems[j].unit_name + `">
                                                    <img src="{{ url('/uploads/') . '/' .'` + (allItems[j].image ?? `noimage.png`) + `.'}}" width="120" class="figure-img img-fluid rounded" alt="` + allItems[j].name + `">
                                                    <figcaption class="figure-caption" id="figcaption-` + allItems[j].id + `" data-category="` + categories[i].name + `" data-item="` + allItems[j].name + `" data-id="` + allItems[j].id + `">` + allItems[j].name + `</figcaption>
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

            },
            error: function(response){
                console.log(response);
            }
        });

        var e = $.Event("keydown", { keyCode: 13}); 
        $("body").trigger(e);

        datatable = $('#searched-customer-table').DataTable({
            data: searchedCustomers,
            columns: [
                { title: "Name" },
                { title: "Phone" },
                { title: "Receipt No." },
                { title: "Total" },
                { title: "Order Date" },
            ]
        });
        

        draftOrderstable = $('#draft-orders-table').DataTable({
            data: draftOrders,
            columns: [
                { title: "Receipt No." },
                { title: "Sub Total" },
                { title: "Discount" },
                { title: "Total" },
                { title: "Received" },
                { title: "Receivable" },
                { title: "Date" },
                { title: "Complete", 
                    sortable: false,
                    "render": function ( data, type, full, meta ) {
                        return '<button onclick="markOrderomplete(' + full[0] + ')" class="btn btn-success" type="button">Complete</button>';
                    }
                }
            ]
        });
    });

    $(document).on('change', '#customer', function(){
        $("#overlay").addClass("overlay");
        $("#spinner").addClass("spinner");

        $.ajax({
            type: 'GET',
            url: "{{ url('/sale/create/get-csp') }}/" + $("#customer").val(),
            success: function(response){
                response = JSON.parse(response);
                var rates = response.sale_prices;

                if(rates.length > 0)
                {
                    for(let i = 0; i < rates.length; i++)
                    {
                        if(allItems.findIndex(item => item.id == rates[i].item_id) > -1)
                        {
                            allItems.find(item => item.id == rates[i].item_id).price = rates[i].sale_price;
                        }

                        if(selectedItems.findIndex(item => item.id == rates[i].item_id) > -1)
                        {
                            let thisItem = selectedItems.find(item => item.id == rates[i].item_id);
                            let discountedPrice = parseFloat((thisItem.discountType == 1? rates[i].sale_price - (rates[i].sale_price * (thisItem.discount/100)): rates[i].sale_price - thisItem.discount).toFixed(2));

                            selectedItems.find(item => item.id == rates[i].item_id).originalPrice = rates[i].sale_price;
                            selectedItems.find(item => item.id == rates[i].item_id).price = discountedPrice;
                            selectedItems.find(item => item.id == rates[i].item_id).actualDiscountAmount = parseFloat(((rates[i].sale_price - discountedPrice) * thisItem.quantity).toFixed(2));
                            selectedItems.find(item => item.id == rates[i].item_id).totalPrice = parseFloat((thisItem.quantity * discountedPrice).toFixed(2));

                            addRows(thisItem.id);
                        }
                    }
                }

                $("#overlay").removeClass("overlay");
                $("#spinner").removeClass("spinner");
            },
            error: function(response){
                console.log(response);
            }
        });

        console.log($("#customer").val());
    });
</script>
    
@endsection