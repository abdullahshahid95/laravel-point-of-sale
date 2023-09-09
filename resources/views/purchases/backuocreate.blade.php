@extends('master')

@section('content')
<div id="spinner" class=""></div>
<div id="overlay" class=""></div>

<div id="my-jumbotron" class="jumbotron">
    <div class="row">
        <div class="col-1">
            <a class="btn btn-success" onclick="refreshPurchase()">Refresh</a>
        </div>
        <div class="col-3">
            <div class="row">
                <div class="col-12">
                    <select id="supplier" class="form-control" style="width: 90%;">
                        <option value="1">No supplier</option>
                        @foreach ($suppliers as $supplier)
                            <option value="{{ $supplier->id }}">{{ $supplier->name . ' - ' . $supplier->phone}}</option>
                        @endforeach
                    </select>
                    <button type="button" class="btn btn-primary pt-0 pb-1 pl-1 pr-1" onclick="openAddSupplierDialogue()">+</button>        
                </div>
            </div>
            <div class="row d-none" id="not-received-row">
                <div class="col-6" style="padding-right: 0;">
                    <input id="receiving-date" type="date" class="form-control" style="width: 95%; font-size: smaller;">
                </div>
                <div class="col-6" style="padding-left: 0;">
                    <input id="receiving-time" type="time" class="form-control" style="width: 80%; font-size: smaller;">
                </div>
            </div>
        </div>
        <div class="col-3">
            <input type="radio" id="received" name="status" value="2" checked>
            <label for="received">Received</label>
            |
            <input type="radio" id="not-received" name="status" value="1">
            <label for="not-received">Not Received</label>
        </div>
    </div>
    <br>
    <div id="no-print">
        <div class="row">
            <div class="col-6">
                <div class="row mb-2">
                    <div class="col-12">
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
                            <tbody>
                                @foreach ($items as $item)
                                    <tr onclick="selectItem({{ $item->id }}, '{{ $item->name }}', {{ $item->price }}, {{ $item->unit_id }}, {{ $item->quantity }})">
                                        <td>{{ $item->name }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="row" id="items-grid-view">
                    <div class="col-12">
                        <div class="tab" id="category-tabs">
                            @foreach ($categories as $category)
                                <button class="tablinks" id="{{ 'category-' . $category->name }}" onclick="openTab(event, '{{ $category->name }}')">{{ $category->name }}</button>
                            @endforeach
                        </div>
                        @foreach ($categories as $category)
                            <div id="{{ $category->name }}" class="tabcontent" style="height: 500px; overflow: scroll;">
                                <div class="row sale-item-row">
                                    @foreach ($items as $item)
                                        @if($item->category_id == $category->id)
                                        <div class="col-2 sale-item-col" id="item-col-{{ $item->id }}" data-id="{{ $item->id }}" onclick="selectItem({{ $item->id }}, '{{ $item->name }}', {{ $item->price }}, {{ $item->unit_id }}, {{ $item->quantity }})">
                                            <a class="sale-item-link" data-id="{{ $item->id }}" data-price="{{ $item->price }}" data-unit="{{ $item->unit_name }}">
                                                <figure class="figure">
                                                    <img src="{{ url('/uploads/') . '/' . ($item->image ?? 'noimage.png') }}" width="120" class="figure-img img-fluid rounded" alt="{{ $item->name }}">
                                                    <figcaption class="figure-caption text-center" id="figcaption-{{ $item->id }}" data-category="{{ $category->name }}" data-item="{{ $item->name }}" data-id="{{ $item->id }}">{{ $item->name }} <span id="tick-mark{{$item->id}}" class="selection-mark hidden-item">✔</span></figcaption>
                                                </figure>
                                            </a>
                                        </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="col-6 sale-items-list">
                <div class="row mb-3">
                    <div class="col-3 payment-fields-cols">
                        <label for="total">Total</label>
                        <input type="text" id="total" name="total" class="form-control" readonly>
                    </div>
                    <div class="col-3 payment-fields-cols">
                        <label for="payment">Payment</label>
                        <input type="number" min="1" id="payment" class="form-control"  name="payment" oninput="onPaymentInput()" required/>
                    </div>
                    <div class="col-6 pt-0 pb-0 border-top border-bottom">
                        <button type="button" id="print-button" class="submitBtn no-print btn btn-primary float-right" onclick="checkout()">
                            <svg width="3em" height="3em" viewBox="0 0 16 16" class="bi bi-printer-fill" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                <path d="M5 1a2 2 0 0 0-2 2v1h10V3a2 2 0 0 0-2-2H5z"/>
                                <path fill-rule="evenodd" d="M11 9H5a1 1 0 0 0-1 1v3a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1v-3a1 1 0 0 0-1-1z"/>
                                <path fill-rule="evenodd" d="M0 7a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2h-1v-2a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v2H2a2 2 0 0 1-2-2V7zm2.5 1a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1z"/>
                                </svg>
                        </button>
                        <small class="float-right d-none" id="loading-text">Please wait...</small>
                    </div>
                </div>
                <div class="row">
                    <div class="col-3 payment-fields-cols">
                        <label for="balance">Balance</label>
                        <input type="number" id="balance" class="form-control" readonly>
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
                                    <th>P. price</th>
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

    <div class="customer-invoice invisible d-none" id="section-to-print">
        <div class="row">
            <div class="col-12">
                <p class="text-center h2 font-weight-bold border-bottom">
                    Purchase Order
                </p>                
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <p class="text-center h3 font-weight-bold border-bottom">
                    {{ posConfigurations()->title }} <br>
                    @if(posConfigurations()->subtitle)
                    <span class="h4">
                        {{ posConfigurations()->subtitle }}
                    </span> 
                    @endif
                </p>
                @if(posConfigurations()->contact)
                <p class="text-center h6">
                    Contact# <span class="font-weight-bold">{{ posConfigurations()->contact }}</span>
                </p>
                @endif
                @if(posConfigurations()->address)
                <p class="text-center h6 border-bottom">
                    {{ posConfigurations()->address }}
                </p>
                @endif
            </div>
        </div>
        <div class="row" id="second-row">
            <div class="col-6">
                <p class="text-left h6" id="order-date"></p>
            </div>
            <div class="col-6">
                <p class="text-right h6" id="invoice-number"></p>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <table class="table invoice-table">
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th>Qty.</th>
                            <th>Price</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody id="order-purchases">

                    </tbody>
                    <tfoot>
                        <tr id="order-total-row">
                            <td></td>
                            <td></td>
                            <td>Total</td>
                            <td id="order-total"></td>
                        </tr>
                        <tr>
                            <td style="border: 0;"></td>
                            <td style="border: 0;"></td>
                            <td style="border: 0;">Payment</td>
                            <td style="border: 0;" id="order-payment"></td>
                        </tr>
                        <tr>
                            <td style="border: 0;"></td>
                            <td style="border: 0;"></td>
                            <td style="border: 0;">Balance</td>
                            <td style="border: 0;" id="order-balance"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <p class="h6">
                    User: {{ auth()->user()->name }}
                </p>
            </div>
        </div>
        <hr>
        <p class="text-center" style="font-size: 10px; font-weight: normal;">
            Powered by: Bantach Applications <br>
            03353115731
            <br><br>
        </p>
    </div>
</div>

<!-- The Modal -->
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
    document.getElementsByClassName("tabcontent")[0].style.display = "block";
    document.getElementsByClassName("tablinks")[0].classList.add("active");

    function openTab(evt, tabName) {
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

    var status = 2;
    
    var selectedItems = [];
    var checkOutData = {
        discount: 0,
        total: 0,
        payment: 0,
        balance: 0,
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
                document.getElementById('not-received-row').classList.remove('d-none');
            }
            else
            {
                document.getElementById('not-received-row').classList.add('d-none');
            }
            status = this.value;
        });
    }

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

    function addRows()
    {
        checkOutData.total = 0;
        purchaseRows = ``;
        quantityHtml = ``;
        selectedItems.forEach(item => {
        item.totalPrice = item.quantity * item.price;
        
        checkOutData.total += (item.price * item.quantity);

        if(item.totalPrice == 0)
        {
            item.totalPrice = '';
        }
        else
        {
            if(item.totalPrice % 1 === 0)
            {
                item.totalPrice = item.totalPrice;
            }
            else if(item.totalPrice.toFixed(0) - parseInt(item.totalPrice.toFixed(1)) > 0.4)
            {
                item.totalPrice = item.totalPrice.toFixed(0) - 1;
            }
            else
            {
                item.totalPrice = item.totalPrice.toFixed(0);
            }
        }

            if(item.unitId == 1)
            {
                quantityHtml = `    <input type="number" id="quantity` + item.id + `" min="1" max="` + item.maxQuantity + `" value="` + (item.quantity % 1 === 0? item.quantity: item.quantity.toFixed(2)) + `" class="form-control" oninput="onQuantityInput(event, ` + item.id + `)" required>
                                    <strong class="ml-1">kg</strong>
                                </td>
                                <td width="20%">
                                    <input type="number" id="price` + item.id + `" min="1" max="` + (item.price*item.maxQuantity) + `" value="` + item.totalPrice + `" class="form-control" oninput="onPriceInput(event, ` + item.id + `)" required>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-primary change-quantity-btn" onclick="add1Kg(` + item.id + `)"><span class="quantity-arrow">↑</span> Kg</button><br>
                                    <button type="button" class="btn btn-primary change-quantity-btn" onclick="subtract1Kg(` + item.id + `)"><span class="quantity-arrow">↓</span> Kg</button>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-primary change-quantity-btn" onclick="add1Pao(` + item.id + `)"><span class="quantity-arrow">↑</span> Pao</button>
                                    <button type="button" class="btn btn-primary change-quantity-btn" onclick="subtract1Pao(` + item.id + `)"><span class="quantity-arrow">↓</span> Pao</button>
                                </td>
                                <td>
                                ` + item.totalPrice + `
                                </td>
                                <td>
                                    <input type="number" id="purchase-price` + item.id + `" min="0" value="` + item.price + `" class="form-control" oninput="onPurchasePriceInput(event, ` + item.id + `)" required>
                                </td>`;
            }
            else if(item.unitId == 2)
            {
                quantityHtml = `    <input type="number" id="quantity` + item.id + `" min="1" max="` + item.maxQuantity + `" step="1" value="` + (item.quantity % 1 === 0? item.quantity: item.quantity.toFixed(2)) + `"
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
                                    <button type="button" class="btn btn-primary change-quantity-btn" onclick="subtract1Piece(` + item.id + `, ` + 1 + `)"><span class="quantity-arrow">↓</span></button>
                                </td>
                                <td>
                                ` + item.totalPrice + `
                                </td>
                                <td>
                                    <input type="number" id="purchase-price` + item.id + `" min="0" value="` + item.price + `" class="form-control" oninput="onPurchasePriceInput(event, ` + item.id + `)" required>
                                </td>`;
            }
            else
            {
                quantityHtml = `    <input type="number" id="quantity` + item.id + `" min="1" max="` + item.maxQuantity + `" step="1" value="` + item.quantity + `"
                                                onkeypress="return event.charCode >= 48 && event.charCode <= 57" 
                                                class="form-control" oninput="onQuantityInput(event, ` + item.id + `)" required>
                                </td>
                                <td></td>
                                <td>
                                    <button type="button" class="btn btn-primary change-quantity-btn" onclick="add1Piece(` + item.id + `, ` + 0 + `)"><span class="quantity-arrow">↑</span></button><br>
                                    <button type="button" class="btn btn-primary change-quantity-btn" onclick="subtract1Piece(` + item.id + `, ` + 0 + `)"><span class="quantity-arrow">↓</span></button>
                                </td><td>
                                </td>
                                <td>
                                ` + item.totalPrice + `
                                </td>
                                <td>
                                    <input type="number" id="purchase-price` + item.id + `" min="0" value="` + item.price + `" class="form-control" oninput="onPurchasePriceInput(event, ` + item.id + `)" required>
                                </td>`;
            }

            purchaseRows+= `<tr>
                            <td>
                            ` + item.name + `
                            </td>
                            <td width="20%">
                            ` + quantityHtml + `
                            <td class="no-print">
                                <button type="button" class="btn btn-danger delete" onclick="removeItem(` + item.id + `)"><strong>X</strong></button>
                            </td>
                        </tr>`;
        });

        $("#sales").empty().append(purchaseRows);

        $("#total").val(checkOutData.total.toFixed(0));
        $("#balance").val(checkOutData.total.toFixed(0) - checkOutData.payment);
    }

    function selectItem(id, itemName, price, unitId, maxQuantity)
    {
        if(selectedItems.findIndex(item => item.id == id) < 0)
        {
            currentItemId = id;

            selectedItems.push({id: id, name: itemName, price: price, quantity: 1, totalPrice: price, unitId: unitId, maxQuantity: maxQuantity});

            document.getElementById("tick-mark" + id).classList.remove("hidden-item");

            addRows();
        }
        else
        {
            let item = selectedItems.find(item => item.id == id);
            selectedItems.find(item => item.id == id).quantity++
            addRows();
            
            // var quantity = selectedItems.find(item => item.id == id).quantity; 
            // var price = selectedItems.find(item => item.id == id).price;
            // selectedItems.find(item => item.id == id).totalPrice = quantity * price; 
        }
    }

    function removeItem(itemId)
    {        
        selectedItems.splice(selectedItems.findIndex(item => item.id == itemId), 1);
        
        document.getElementById("tick-mark" + itemId).classList.add("hidden-item");

        addRows();

        onPaymentInput();
    }

    function onPurchasePriceInput(event, itemId) 
    {
        selectedItems.find(item => item.id == itemId).price = event.target.value;
        addRows();

        $("#purchase-price" + itemId).focus();
        var temp = $("#purchase-price" + itemId).val();
        $("#purchase-price" + itemId).val('');
        $("#purchase-price" + itemId).val(temp);
    }

    function onQuantityInput(event, itemId)
    {
        if(event.target.value != selectedItems.find(item => item.id == itemId).quantity)
        {
            if(event.target.value != '')
            {
                selectedItems.find(item => item.id == itemId).quantity = parseFloat(event.target.value);
            }
            else
                selectedItems.find(item => item.id == itemId).quantity = '';
            
            addRows();

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
            selectedItems.find(item => item.id == itemId).quantity = parseFloat(event.target.value)/item.price;
            // selectedItems.find(item => item.id == itemId).quantity = parseFloat((parseFloat(event.target.value)/item.price).toString().match(/^-?\d+(?:\.\d{0,2})?/)[0]);
        }
        else
        {
            selectedItems.find(item => item.id == itemId).quantity = '';
        }

        addRows();

        $("#price" + itemId).focus();
        var temp = $("#price" + itemId).val();
        $("#price" + itemId).val('');
        $("#price" + itemId).val(temp);
    }

    function onPaymentInput()
    {
        if(parseFloat(document.getElementById("payment").value) > 0)
        {
            checkOutData.payment = document.getElementById("payment").value;
        }
        else
        {
            checkOutData.payment = 0;
        }

        checkOutData.balance = checkOutData.total - checkOutData.payment;
        $("#balance").val(checkOutData.balance);
    }

    function add1Pao(itemId)
    {
        selectedItems.find(item => item.id == itemId).quantity += 0.25;
        addRows();
    }

    function subtract1Pao(itemId)
    {
        selectedItems.find(item => item.id == itemId).quantity -= 0.25;
        addRows();
    }

    function add1Kg(itemId)
    {
        selectedItems.find(item => item.id == itemId).quantity++;
        addRows();
    }

    function subtract1Kg(itemId)
    {
        selectedItems.find(item => item.id == itemId).quantity--;
        addRows();
    }

    function add1Dozen(itemId)
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

        addRows();
    }

    function subtract1Dozen(itemId)
    {
        selectedItems.find(item => item.id == itemId).quantity -= 12;
        var quantity = selectedItems.find(item => item.id == itemId).quantity;

        var dozenQuantity = 0;
        $("#dozen-quantity" + itemId).html('');
        if(quantity >= 12)
        {
            dozenQuantity = parseInt(quantity / 12) + ' Dozen' + (quantity % 12 > 0? ' ' + quantity % 12: '');
            $("#dozen-quantity" + itemId).html(dozenQuantity);
        }

        addRows();
    }

    function add1Piece(itemId, type)
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

        addRows();
    }

    function subtract1Piece(itemId, type)
    {
        selectedItems.find(item => item.id == itemId).quantity--;
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

        addRows();
    }

    function refreshPurchase()
    {
        itemIds = selectedItems.map(item => item.id);

        ItemIdsLength = itemIds.length;
        for(let i = 0; i < ItemIdsLength; i++)
        {
            removeItem(itemIds[i]);
        }

        checkOutData = {
            payment: 0,
            balance: 0,
            total: 0,
        }

        selectedItemsLength = 0;

        $("#total").val(0);
        $("#payment").val(0);
        $("#balance").val(0);

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
        itemIds = selectedItems.filter(item => item.quantity <= 0).map(item => item.id);

        ItemIdsLength = itemIds.length;
        for(let i = 0; i < ItemIdsLength; i++)
        {
            removeItem(itemIds[i]);
        }

        selectedItemsLength = selectedItems.length;

        let receivingDate = status == 1? document.getElementById("receiving-date").value: null;
        let receivingTime = status == 1? document.getElementById("receiving-time").value: null;

        if(selectedItemsLength > 0 && !checkOutLoading)
        {
            $("#overlay").addClass("overlay");
            $("#spinner").addClass("spinner");

            $("#section-to-print").removeClass('d-none');

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

            $.ajax({
                type: 'POST',
                url: "{{ url('/purchase') }}",
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                data: JSON.stringify({
                    purchases: selectedItems,
                    total: checkOutData.total,
                    payment: checkOutData.payment,
                    balance: checkOutData.balance,
                    receiptNumber: checkOutData.receiptNumber,
                    supplierId: selectedSupplierId,
                    status: status,
                    receivingDate: (receivingDate != null && receivingDate != '')? receivingDate: null,
                    receivingTime: (receivingTime != null && receivingTime != '')? receivingTime: null
                }),
                success: function(response){
                    response = JSON.parse(response);
                    if(response.message == 1)
                    {
                        checkOutLoading = false;
                        $("#loading-text").addClass('d-none');
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

            let orderPurchases = ``;
            let itemQuantity = ``;

            selectedItems.forEach(item => {
                item.quantity = parseFloat(item.quantity.toFixed(2));
                if(item.totalPrice == 0)
                {
                    item.totalPrice = '';
                }
                else
                {
                    if(item.totalPrice % 1 === 0)
                    {
                        item.totalPrice = item.totalPrice;
                    }
                    else if(item.totalPrice.toFixed(0) - parseInt(item.totalPrice.toFixed(1)) > 0.4)
                    {
                        item.totalPrice = item.totalPrice.toFixed(0) - 1;
                    }
                    else
                    {
                        item.totalPrice = item.totalPrice.toFixed(0);
                    }
                }
            }); // for removing extra decimals
            
            selectedItems.forEach(item => {

                itemQuantity = item.unitId == 1? (item.quantity + ` kg`):
                (item.unitId == 3? (item.quantity):
                (item.quantity + `(` + parseInt(item.quantity / 12) + ` Dozen` + (item.quantity % 12 > 0? (` ` + item.quantity % 12): ``) + `)`));

                orderPurchases += `<tr>
                                    <td>` + item.name + `</td>
                                    <td>
                                        ` +
                                        itemQuantity
                                        + `
                                    </td>
                                    <td>` + item.price + `</td>
                                    <td>` + item.totalPrice + `</td>
                                </tr>`;
            }); //dynamically adding rows for printing

            $("#order-purchases").empty().append(orderPurchases);
            $("#order-date").empty().text(checkOutData.date);
            $("#invoice-number").empty().text(checkOutData.receiptNumber);

            if(selectedSupplierId != 1)
            {
                $("#second-row").after(`<div class="row" id="third-row">
                                            <div class="col-12">
                                                <p class="text-left">Supplier: <span class="h6">` + $("#supplier option:selected").text() + `</span></p>
                                            </div>
                                        </div>`);
            }

            if(checkOutData.total % 1 === 0)
            {
                checkOutData.total = checkOutData.total;
            }
            else if(checkOutData.total.toFixed(0) - parseInt(checkOutData.total.toFixed(1)) > 0.4)
            {
                checkOutData.total = checkOutData.total.toFixed(0) - 1;
            }
            else
            {
                checkOutData.total = checkOutData.total.toFixed(0);
            }

            $("#order-total").empty().text(checkOutData.total);
            $("#order-payment").empty().text(checkOutData.payment);
            $("#order-balance").empty().text(checkOutData.balance);

            $("#overlay").removeClass("overlay");
            $("#spinner").removeClass("spinner");

            window.print();

            refreshPurchase();

            $("#section-to-print").addClass('d-none');
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

    $(document).ready(function(){
        $("#items-table").DataTable();
        $("#supplier").select2();

        var e = $.Event("keydown", { keyCode: 13}); 
        $("body").trigger(e);
    });
</script>
    
@endsection