@extends('master')

@section('content')
    <div class="jumbotron" style="padding-left: 3%; padding-right: 3%;">
        <form action="{{ url('/item') }}" enctype="multipart/form-data" method="POST">
            @csrf
            <input type="hidden" id="rowsCount" name="rows_count" value="1">
            <div class="row">
                <div class="col-6">
                    <h2>Add Item</h2>
                </div>
                <div class="col-6">
                    <button type="button" class="btn btn-primary float-right" onclick="importExcel()">Import from Excel</button>
                    <input type="file" accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" style="display: none;" id="excel-file" onchange="readExcelFile(event)">
                </div>
            </div>
            <div class="row">
                <div class="col-12" style="padding-left: 0; padding-right: 0;">
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
                                    <th>Category</th>
                                    <th>Name</th>
                                    <th>Label</th>
                                    <th>Tax</th>
                                    <th>Discount</th>
                                    <th>Reorder Level</th>
                                    <th>Image</th>
                                    <th>Unit</th>
                                    <th>Sale Price</th>
                                    <th>Pur. Price</th>
                                    <th>Remove</th>
                                </thead>
                                <tbody id="item-table-body">
                                    <tr id="row-1">
                                        <td>
                                            <select class="form-control category-id" name="category_id[]" required>
                                                <option value="">Category</option>
                                                @foreach ($categories as $category)
                                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control" name="name[]" required>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control" name="label[]">
                                        </td>
                                        <td>
                                            <input type="number" class="form-control" name="tax[]" step="0.01">
                                            <input type="radio" id="tax-percentage-1" name="tax_type_1" value="1" checked>
                                            <label for="tax-percentage-1">%</label>
                                            |
                                            <input type="radio" id="tax-amount-1" name="tax_type_1" value="2">
                                            <label for="tax-amount-1">Amnt.</label>
                                        </td>
                                        <td>
                                            <input type="number" class="form-control" name="discount[]" step="0.01">
                                            <input type="radio" id="discount-percentage-1" name="discount_type_1" value="1" checked>
                                            <label for="discount-percentage-1">%</label>
                                            |
                                            <input type="radio" id="discount-amount-1" name="discount_type_1" value="2">
                                            <label for="discount-amount-1">Amnt.</label>
                                        </td>
                                        <td width="6%">
                                            <input type="number" min="1" class="form-control" name="reorder_level[]" required>
                                        </td>
                                        <td>
                                            <input type="file" class="form-control" name="image[]">
                                        </td>
                                        <td>
                                            <select class="form-control unit-id" name="unit_id[]" required>
                                                <option value="">Unit</option>
                                                <option value="1">Kilogram</option>
                                                <option value="2">Dozen</option>
                                                <option value="3">Piece</option>
                                                {{-- <option value="4">Pound</option> --}}
                                            </select>
                                        </td>
                                        <td width="6%">
                                            <input type="number" min="0" class="form-control" name="sale_price[]" step="0.01">
                                        </td>
                                        <td width="6%">
                                            <input type="number" min="0" class="form-control" name="purchase_price[]" step="0.01">
                                        </td>
                                        <td>
                                            <button class="btn btn-danger" type="button" onclick="removeRow(event)">X</button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script type="text/javaScript">
        $(".category-id").select2();
        var rowCount = 2;

        function addRow()
        {
            let row = `<tr id='row-` + rowCount + `'>
                    <td>
                        <select class="form-control category-id" name="category_id[]" required>
                            <option value="">Category</option>
                            @foreach ($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td>
                        <input type="text" class="form-control" name="name[]" required>
                    </td>
                    <td>
                        <input type="text" class="form-control" name="label[]">
                    </td>
                    <td>
                        <input type="number" class="form-control" name="tax[]" step="0.01">
                        <input type="radio" id="tax-percentage-` + rowCount + `" name="tax_type_` + rowCount + `" value="1" checked>
                        <label for="tax-percentage-` + rowCount + `">%</label>
                        |
                        <input type="radio" id="tax-amount-` + rowCount + `" name="tax_type_` + rowCount + `" value="2">
                        <label for="tax-amount-` + rowCount + `">Amnt.</label>
                    </td>
                    <td>
                        <input type="number" class="form-control" name="discount[]" step="0.01">
                        <input type="radio" id="discount-percentage-` + rowCount + `" name="discount_type_` + rowCount + `" value="1" checked>
                        <label for="discount-percentage-` + rowCount + `">%</label>
                        |
                        <input type="radio" id="discount-amount-` + rowCount + `" name="discount_type_` + rowCount + `" value="2">
                        <label for="discount-amount-` + rowCount + `">Amnt.</label>
                    </td>
                    <td width="6%">
                        <input type="number" min="1" class="form-control" name="reorder_level[]" required>
                    </td>
                    <td>
                        <input type="file" class="form-control" name="image[]">
                    </td>
                    <td>
                        <select class="form-control unit-id" name="unit_id[]" required>
                            <option value="">Unit</option>
                            <option value="1">Kilogram</option>
                            <option value="2">Dozen</option>
                            <option value="3">Piece</option>
                        </select>
                    </td>
                    <td width="6%">
                        <input type="number" min="0" class="form-control" name="sale_price[]" step="0.01">
                    </td>
                    <td width="6%">
                        <input type="number" min="0" class="form-control" name="purchase_price[]" step="0.01">
                    </td>
                    <td>
                        <button class="btn btn-danger" type="button" onclick="removeRow(event)">X</button>
                    </td>
                </tr>`;

            $("#item-table-body").append(row);

            $(".category-id").select2();

            document.getElementById("rowsCount").value = rowCount;

            rowCount++;
        }

        function removeRow(event)
        {
            if($("#item-table-body tr").length > 1)
            {
                $(event.target.parentElement.parentElement).remove();                
            }
        }

        $(document).ready(function(){

        });

        var collectiveCategoryId = 0;
        var collectiveUnitId = 0;

        function importExcel()
        {
            let row = $("#item-table-body tr").first();

            let category = $(row).find(".category-id");
            let unit = $(row).find(".unit-id");

            if($(category).val() != null && $(category).val() != '' && $(category).val() != 0)
            {
                collectiveCategoryId = $(category).val();
                console.log(collectiveCategoryId);
            }
            else
            {
                alert("Select category");
                return;
            }

            if($(unit).val() != null && $(unit).val() != '' && $(unit).val() != 0)
            {
                collectiveUnitId = $(unit).val();
                console.log(collectiveUnitId);
            }
            else
            {
                alert("Select Unit");
                return;
            }

            document.getElementById("excel-file").click();
        }

        function readExcelFile(event)
        {
            var file = event.target.files[0];

            var config = {
                            delimiter: "",	// auto-detect
                            newline: "",	// auto-detect
                            quoteChar: '"',
                            escapeChar: '"',
                            header: false,
                            transformHeader: undefined,
                            dynamicTyping: false,
                            preview: 0,
                            encoding: "",
                            worker: false,
                            comments: false,
                            step: undefined,
                            complete: function(c) {
                                let data = c.data;
                                let rows = ``;
                                rowCount = 1;

                                for (let i = 0; i < data.length; i++)
                                {
                                    let filteredData = data[i].filter(f => f.trim() != null && f.trim() != '')
                                    if(filteredData.length > 0)
                                    {
                                        rows += `<tr id='row-` + rowCount + `'>
                                                    <td>
                                                        <select class="form-control category-id" name="category_id[]" required>
                                                            <option value="">Category</option>
                                                            @foreach ($categories as $category)
                                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control" name="name[]" value="` + filteredData[0] + `" required>
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control" name="label[]">
                                                    </td>
                                                    <td>
                                                        <input type="number" class="form-control" name="tax[]" step="0.01">
                                                        <input type="radio" id="tax-percentage-` + rowCount + `" name="tax_type_` + rowCount + `" value="1" checked>
                                                        <label for="tax-percentage-` + rowCount + `">%</label>
                                                        |
                                                        <input type="radio" id="tax-amount-` + rowCount + `" name="tax_type_` + rowCount + `" value="2">
                                                        <label for="tax-amount-` + rowCount + `">Amnt.</label>
                                                    </td>
                                                    <td>
                                                        <input type="number" class="form-control" name="discount[]" step="0.01">
                                                        <input type="radio" id="discount-percentage-` + rowCount + `" name="discount_type_` + rowCount + `" value="1" checked>
                                                        <label for="discount-percentage-` + rowCount + `">%</label>
                                                        |
                                                        <input type="radio" id="discount-amount-` + rowCount + `" name="discount_type_` + rowCount + `" value="2">
                                                        <label for="discount-amount-` + rowCount + `">Amnt.</label>
                                                    </td>
                                                    <td width="6%">
                                                        <input type="number" min="1" class="form-control" name="reorder_level[]" value="5" required>
                                                    </td>
                                                    <td>
                                                        <input type="file" class="form-control" name="image[]">
                                                    </td>
                                                    <td>
                                                        <select class="form-control unit-id" name="unit_id[]" required>
                                                            <option value="">Unit</option>
                                                            <option value="1">Kilogram</option>
                                                            <option value="2">Dozen</option>
                                                            <option value="3">Piece</option>
                                                        </select>
                                                    </td>
                                                    <td width="6%">
                                                        <input type="number" min="0" class="form-control" name="sale_price[]" value="` + filteredData[1] + `" step="0.01">
                                                    </td>
                                                    <td width="6%">
                                                        <input type="number" min="0" class="form-control" name="purchase_price[]" value="` + filteredData[2] + `" step="0.01">
                                                    </td>
                                                    <td>
                                                        <button class="btn btn-danger" type="button" onclick="removeRow(event)">X</button>
                                                    </td>
                                                </tr>`;

                                        rowCount++;
                                        console.log(filteredData);
                                    }
                                }

                                $("#item-table-body").empty().append(rows);
                                $(".category-id").select2();
                                $('.category-id').val(collectiveCategoryId);
                                $('.category-id').trigger('change');
                                $(".unit-id").val(collectiveUnitId);
                                // $('.unit-id').trigger('change');

                                document.getElementById("rowsCount").value = rowCount;
                            },
                            error: function(e) {
                                console.log(e);
                            },
                            download: false,
                            downloadRequestHeaders: undefined,
                            downloadRequestBody: undefined,
                            skipEmptyLines: false,
                            chunk: undefined,
                            chunkSize: undefined,
                            fastMode: undefined,
                            beforeFirstChunk: undefined,
                            withCredentials: undefined,
                            transform: undefined,
                            delimitersToGuess: [',', '\t', '|', ';', Papa.RECORD_SEP, Papa.UNIT_SEP]
                        }

            Papa.parse(file, config);
        }
    </script>
@endsection