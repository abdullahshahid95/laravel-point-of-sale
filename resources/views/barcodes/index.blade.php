@extends('master')

@section('content')
    <div id="spinner" class=""></div>
    <div id="overlay" class=""></div>
    <div class="container" id="generated-barcodes" style="display: none;">
        <!-- jsbarcode-format="CODE128" -->
        <!-- jsbarcode-fontSize= "15"
        jsbarcode-height= "40"
        jsbarcode-fontoptions="bold" -->
        <div class="row">
            <div class="col-12">
                <button type="button" class="btn btn-primary float-left" id="print-button" onclick="window.print()">Print</button>
                <button type="button" class="btn btn-primary float-right" id="back-button" onclick="moveBack()">Back</button>
            </div>
        </div>
        <div class="row d-flex justify-content-center" id="barcode-print-section" style="">
			<div class="col-12">
				<div class="row" style="margin-top: 2px; width: 70%;" id="generated-barcodes-row">
					{{--@foreach ($items as $item)
					<div class="col-6" style="margin-bottom: 12%; height: 120px; width: 500px;">
                        <div class="row" style="text-align: left;">
                            <div class="col-12" style="white-space: nowrap;">
                                <strong class="ml-2 font-weight-bold float-left" style="font-size: 85%;">{{ $item->name }}</strong><br>
                                <strong class="ml-2 font-weight-bold"> Rs{{ $item->sale_price + 0 }}</strong>
                            </div>
                        </div>
                        <div class="row" style="margin-top: -1%;">
                            <div class="col-12">
                                <svg class="barcodei-{{ $item->id }}"
                                    jsbarcode-value="{{ $item->label }}"
                                    jsbarcode-textmargin="0"
                                    jsbarcode-height="30"
                                    jsbarcode-fontSize="12">
                                </svg>
                            </div>
                        </div>
                        <div class="row" style="text-align: left; margin-top: -2%;">
                            <div class="col-12">
                                <strong class="ml-2 font-weight-bold float-left">Abc Mart & General Store</strong>
                            </div>
                        </div>
					</div>
					@endforeach--}}
				</div>
			</div>
        </div>
    </div>
    <div class="container" style="padding-left: 3%; padding-right: 3%;" id="before-barcode-generation">
        <div class="row">
            <div class="col-6">
                <h2>Select Items for Barcode</h2>
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
                                <th>Items</th>
                                <th>Quantity</th>
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
        </div>
    </div>
    <script type="application/javascript">
        let itemSelect = `<select class="form-control item-id" required>
                            <option value="">Item</option>`;
        let itemOptions = ``;

        var rowCount = 2;
        var items = [];

        function addRow()
        {
            let row = `<tr id='row-` + rowCount + `'>
                            <td width="40%" class="pl-2 pr-2">
                                `+ itemSelect +`
                            </td>
                            <td width="40%" class="pl-2 pr-2">
                                <input type="number" class="form-control quantity" id="quantity` + rowCount + `" step="1" min="1" max="20">
                            </td>
                            <td width="20%" class="pl-2 pr-2">
                                <button class="btn btn-danger float-right" type="button" onclick="removeRow(event)" style="width: 50%;">X</button>
                            </td>
                        </tr>`;

            $("#item-table-body").append(row);

            $(".item-id").select2();

            rowCount++;
        }

        function removeRow(event)
        {
            if($("#item-table-body tr").length > 1)
            {
                $(event.target.parentElement.parentElement).remove();                
            }
        }

        function moveBack()
        {
            console.log("here");
            document.getElementById("generated-barcodes").style.display = "none";
            document.getElementById("before-barcode-generation").style.display = "block";
        }

        function moveToNextStep()
        {
            var itemsInput = [];

            $('#item-table-body tr').each(function (i, row) {
                // reference all the stuff you need first
                var row = $(row);
                let itemId = row.find('.item-id').val();
                let quantity = row.find('.quantity').val();
                if(itemId != null && itemId != '' && quantity != null && quantity != '' && quantity != 0 && quantity != '0') {
                    if(parseInt(quantity) > 20 || parseInt(quantity) < 1) {
                        alert("Quantity limit: 1 to 20.");

                        itemsInput = [];
                        return false;
                    }
                    if(itemsInput.findIndex(it => it.id == itemId) > -1) {
                        alert("Duplicate entry not allowed.");

                        itemsInput = [];
                        return false;
                    }

                    itemsInput.push({
                        id: itemId,
                        name: items.find(it => it.id == itemId).name,
                        label: items.find(it => it.id == itemId).label,
                        price: parseFloat(items.find(it => it.id == itemId).sale_price),
                        quantity: parseInt(quantity)
                    });

                    let totalQuantity = itemsInput.reduce(function(prev, cur) {
                            return prev + cur.quantity;
                        }, 0);

                    if(totalQuantity > 20) {
                        alert("Total Quantity limit Per Print: 1 to 20.");

                        itemsInput = [];
                        return false;
                    }
                }
            });

            console.log(itemsInput);
            let rows = ``;

            if(itemsInput.length > 0 && itemsInput.length < 21)
            {   
                for (let i = 0; i < itemsInput.length; i++)
                {
                    for(let j = 0; j < itemsInput[i].quantity; j++)
                    {
                        rows += `<div class="col-6 barcode-details-column" style="margin-bottom: 15%; height: 120px; width: 600px;">
                                <div class="row" style="text-align: left;">
                                    <div class="col-12" style="white-space: nowrap;">
                                        <strong class="ml-2 font-weight-bold float-left barcode-item-name" style="font-size: 85%;">` + itemsInput[i].name + `</strong><br>
                                        <strong class="ml-2 font-weight-bold barcode-item-price"> Rs` + itemsInput[i].price + `</strong>
                                    </div>
                                </div>
                                <div class="row" style="margin-top: -1%; display: block;">
                                    <div class="col-12">
                                        <svg class="barcodei-` + itemsInput[i].id + `"
                                            jsbarcode-value="` + itemsInput[i].label + `"
                                            jsbarcode-width= "1"
                                            jsbarcode-textmargin="0"
                                            jsbarcode-height="30"
                                            jsbarcode-fontSize="12">
                                        </svg>
                                    </div>
                                </div>
                                <div class="row" style="text-align: left; margin-top: -2%;">
                                    <div class="col-12" style="font-size: 85%; white-space: nowrap;">
                                        <strong class="ml-2 font-weight-bold float-left">Abc Mart & General Store</strong>
                                    </div>
                                </div>
                            </div>`;
                    }
                }

                document.getElementById("before-barcode-generation").style.display = "none";
                document.getElementById("generated-barcodes").style.display = "block";

                $("#generated-barcodes-row").empty().append(rows);

                $("#overlay").addClass("overlay");
                $("#spinner").addClass("spinner");

                $('*[class^="barcodei"]').each(function(key, value) {
                    let elementClass = $(value).attr("class");
                    console.log(elementClass);
                    // JsBarcode("." + elementClass, "123456789012", {
                    //         format: "upc",
                    //         lineColor: "#0aa",
                    //         width: 4,
                    //         height: 40,
                    //         displayValue: false
                    //     });
                    JsBarcode("." + elementClass).init();
                });

                $("#overlay").removeClass("overlay");
                $("#spinner").removeClass("spinner");
            }
        }

        $(document).ready(function() {
            $(".item-id").select2();

            $("#overlay").addClass("overlay");
            $("#spinner").addClass("spinner");

            $.ajax({
                type: 'GET',
                url: "{{ url('/barcode/create/update-barcode-page') }}",
                success: function(response){
                    $("#overlay").removeClass("overlay");
                    $("#spinner").removeClass("spinner");

                    response = JSON.parse(response);
                    items = response.items;

                    let item
                    for(let i = 0; i < items.length; i++)
                    {
                        itemOptions += `<option value="` + items[i].id + `">` + items[i].name + `</option>`;
                    }

                    itemSelect = itemSelect + (itemOptions + `</select>`);

                    let row = `<tr id='row-1'>
                                    <td width="40%" class="pl-2 pr-2">
                                        `+ itemSelect +`
                                    </td>
                                    <td width="40%" class="pl-2 pr-2">
                                        <input type="number" class="form-control quantity" id="quantity1" step="1" min="1" max="20">
                                    </td>
                                    <td width="20%" class="pl-2 pr-2">
                                        <button class="btn btn-danger float-right" type="button" onclick="removeRow(event)" style="width: 50%;">X</button>
                                    </td>
                                </tr>`;
                    $("#item-table-body").empty().append(row);

                    $(".item-id").select2();
                },
                error: function(response){
                    console.log(response);
                }
            });
        });
    </script>
@endsection