@extends('master')

@section('content')
    <div class="container">
        @if(allowed(5, 'make') || allowed(20, 'make'))
        <div class="row pb-3">
            @if(allowed(5, 'make'))
            <div class="col-12">
                <a href="{{ url('/item/create') }}" class="btn btn-primary float-right">Add Item(s)</a>
            </div>
            @endif
        </div>
        @endif
        <div class="row mb-1">
            <div class="col-12">
                <input type="text" value="{{ $searchValue }}" class="form-control float-right col-3" placeholder="Search Item               Enter to search" name="search_value" onkeypress="searchItem(event)">
            </div>
        </div>
        @if($errors->any())
        <div class="row mb-3">
            <div class="col-12">
                <span class="alert alert-danger" role="alert">
                    <strong>{{ $errors->first() }}</strong>
                </span>
            </div>
        </div>
        @endif
        <div class="row">
            <div class="col-12">
                <table class="table table-bordered table-hover" id="table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Label No.</th>
                            <th>Sale Price</th>
                            <th>Purchase Price</th>
                            <th>SKU</th>
                            <th>Category</th>
                            <th>Reorder Level</th>
                            <th>Picture</th>
                            @if(allowed(5, 'edit'))
                            <th>Edit</th>
                            @endif
                            @if(allowed(5, 'edit'))
                            <th>Status</th>
                            @endif
                            @if(allowed(5, 'remove'))
                            <th>Delete</th>
                            @endif
			    <th>Modification Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($items as $item)
                        <tr id="item-row{{ $item->id }}">
                            <td>{{ $item->name }}</td>
                            <td>{{ $item->label }}</td>
                            <td>
                                @if($item->unit_id == 2)
                                {{ round($item->sale_price * 12) . ' /Dozen' }}  
                                @elseif($item->unit_id == 1)
                                    {{ $item->sale_price . ' /kg' }}
                                @else
                                    {{ $item->sale_price . ' /piece' }}
                                @endif
                            </td>
                            <td>
                                @if($item->unit_id == 2)
                                {{ round($item->purchase_price * 12) . ' /Dozen' }}  
                                @elseif($item->unit_id == 1)
                                    {{ $item->purchase_price . ' /kg' }}
                                @else
                                    {{ $item->purchase_price . ' /piece' }}
                                @endif
                            </td>
                            <td>{{ $item->sku }}</td>
                            <td>{{ $item->category_name }}</td>
                            <td>{{ $item->reorder_level }}</td>
                            <td><img src="{{ url('/uploads/') . '/' . ($item->image ?? 'noimage.png') }}" class="img-thumbnail" width="120" /></td>
                            @if(allowed(5, 'edit'))
                            <td><button type="button" class="btn btn-primary" onclick="openEditDialogue({{ $item->id }}, '{{ $item->name }}', '{{ $item->label }}', {{ $item->discount_type }}, {{ $item->discount }}, {{ $item->tax_type }}, {{ $item->tax }}, {{ $item->category_id }}, {{ $item->unit_id }}, {{ $item->reorder_level }}, {{ $item->sale_price }}, {{ $item->purchase_price }})">Edit</button></td>
                            @endif
                            @if(allowed(5, 'edit'))
                            <td style="text-align: center;">
                                @if($item->status == 1)
                                    <span class="badge badge-success pt-2 pb-4 mb-1" style="width: 100%; height: 20px; font-size: 100%;">Active</span>
                                    <button type="button" class="btn btn-warning p-0" onclick="deactivateItem(event, {{ $item->id }})" style="width: 80%; height: 20%; font-size: 90%;">Deactivate</button>
                                @else
                                    <span class="badge badge-danger pt-2 pb-4 mb-1" style="width: 100%; height: 20px; font-size: 100%;">Inactive</span>
                                    <button type="button" class="btn btn-success p-0" onclick="activateItem(event, {{ $item->id }})" style="width: 80%; height: 20%; font-size: 90%;">Activate</button>
                                @endif
                            </td>
                            @endif
                            @if(allowed(5, 'remove'))
                            <td><button type="button" class="btn btn-danger" onclick="deleteItem(event, {{ $item->id }})">Delete</button></td>
                            @endif
                            <td>
                                @php
                                    $new_datetime = DateTime::createFromFormat ( "Y-m-d H:i:s", $item->updated_at);
                                    echo $new_datetime->format('d-m-y l, h:i A');
                                @endphp
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-12">
            @if(request()->input('searchValue'))
            {{ $items->appends(['searchValue' => request()->input('searchValue')])->links() }}
            @else
            {{ $items->links() }}
            @endif
        </div>
    </div>
    
    <!-- The Modal -->
    <div id="edit-item-modal" class="edit-item-modal">
        <!-- Modal content -->
        <div class="edit-item-modal-content">
            <div class="row">
                <div class="col-6">
                    <h3>Edit Item</h3>
                </div>
                <div class="col-6">
                    <span id="edit-item-modal-close" class="edit-item-modal-close" onclick="closeEditDialogue()">&times;</span>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="form-group col-12">
                    <div class="col-6 pl-5">
                        <form id="edit-item-form" action="" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <input type="hidden" id="redirect-url" name="redirect_url"/>
                        <div class="form-group row">
                            <label for="category_id" class="col-md-4 col-form-label text-md-right">Category</label>
                            <div class="col-md-6">
                                <select id="category_id" class="form-control @error('category_id') is-invalid @enderror" name="category_id" required>
                                    <option value="">Please select a Category</option>
                                    @foreach ($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="name" class="col-md-4 col-form-label text-md-right">Name</label>
                            <div class="col-md-6">
                                <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" required autocomplete="name">
                
                                @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="label" class="col-md-4 col-form-label text-md-right">Label</label>
                            <div class="col-md-6">
                                <input id="label" type="text" class="form-control @error('label') is-invalid @enderror" name="label" required autocomplete="label">
                
                                @error('label')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="tax" class="col-md-4 col-form-label text-md-right">Tax</label>
                            <div class="col-md-6">
                                <input id="tax" type="number" min="0" class="form-control @error('tax') is-invalid @enderror" name="tax" autocomplete="tax" step="0.01">
                                <input type="radio" id="tax-percentage" name="tax_type" value="1">
                                <label for="tax-percentage">%</label>
                                |
                                <input type="radio" id="tax-amount" name="tax_type" value="2">
                                <label for="tax-amount">Amnt.</label>
                
                                @error('tax')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="discount" class="col-md-4 col-form-label text-md-right">Discount</label>
                            <div class="col-md-6">
                                <input id="discount" type="number" min="0" class="form-control @error('discount') is-invalid @enderror" name="discount" autocomplete="label" step="0.01">
                                <input type="radio" id="discount-percentage" name="discount_type" value="1">
                                <label for="discount-percentage">%</label>
                                |
                                <input type="radio" id="discount-amount" name="discount_type" value="2">
                                <label for="discount-amount">Amnt.</label>

                                @error('discount')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="reorder_level" class="col-md-4 col-form-label text-md-right">Reorder Level</label>
                            <div class="col-md-6">
                                <input id="reorder_level" type="number" min="1" class="form-control @error('reorder_level') is-invalid @enderror" name="reorder_level" required autocomplete="reorder_level">
                
                                @error('reorder_level')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="image" class="col-md-4 col-form-label text-md-right">Image</label>
                            <div class="col-md-6">
                                <input id="image" type="file" class="form-control @error('image') is-invalid @enderror" name="image">
                
                                @error('image')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="unit_id" class="col-md-4 col-form-label text-md-right">Unit</label>
                            <div class="col-md-6">
                                <select id="unit_id" class="form-control @error('unit_id') is-invalid @enderror" name="unit_id" required>
                                    <option value="">Please select a unit</option>
                                    <option value="1">Kilogram</option>
                                    <option value="2">Dozen</option>
                                    <option value="3">Piece</option>
                                    {{-- <option value="3">Pound</option> --}}
                                </select>
                                @error('unit_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="sale_price" class="col-md-4 col-form-label text-md-right">Sale Price</label>
                            <div class="col-md-6">
                                <input id="sale_price" type="number" min="0" class="form-control @error('sale_price') is-invalid @enderror" name="sale_price" value="{{ $item->rate->sale_price ?? old('sale_price') }}" autocomplete="sale_price" step="0.01">
                
                                @error('sale_price')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="purchase_price" class="col-md-4 col-form-label text-md-right">Purchase Price</label>
                            <div class="col-md-6">
                                <input id="purchase_price" type="number" min="0" class="form-control @error('purchase_price') is-invalid @enderror" name="purchase_price" value="{{ $item->rate->purchase_price ?? old('purchase_price') }}" autocomplete="purchase_price" step="0.01">
                
                                @error('purchase_price')
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

    <script type="application/javascript">
        let scroll = 0;

        function openEditDialogue(id, name, label, discountType, discount, taxType, tax, categoryId, unitId, reorderLevel, salePrice, purchasePrice) 
        {
            var modal = document.getElementById("edit-item-modal");
            modal.style.display = "block";

            document.getElementById("name").value = name;
            document.getElementById("label").value = label;
            if(taxType == 1)
            {
                document.getElementById("tax-percentage").checked = true;
            }
            else
            {
                document.getElementById("tax-amount").checked = true;
            }
            document.getElementById("tax").value = tax;
            if(discountType == 1)
            {
                document.getElementById("discount-percentage").checked = true;
            }
            else
            {
                document.getElementById("discount-amount").checked = true;
            }
            document.getElementById("discount").value = discount;
            document.getElementById("category_id").value = categoryId;
            document.getElementById("unit_id").value = unitId;
            document.getElementById("reorder_level").value = reorderLevel;
            document.getElementById("sale_price").value = salePrice;
            document.getElementById("purchase_price").value = purchasePrice;

            document.getElementById("edit-item-form").action = "{{ url('/item/') }}/" + id;

            let url = new URL(window.location.href);
            url.searchParams.set('scrollPosition', scroll);

            document.getElementById('redirect-url').value = url.toString();
        }

        function closeEditDialogue()
        {
            var modal = document.getElementById("edit-item-modal");
            modal.style.display = "none";
        }

        function deleteItem(event, id)
        {
            var _delete = confirm('Delete this item?');

            let url = new URL(window.location.href);
            url.searchParams.set('scrollPosition', scroll);

            document.getElementById('redirect-url').value = url.toString();

            if(_delete)
            {
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: "{{ url('/item/') }}/" + id,
                    type: 'DELETE',
                    success: function(response){
                        console.log(response);
                        if(response == 'deleted')
                        {
                            // window.location.href = "{{ url('/items') }}";
                            // window.location.href = url.toString();
                            document.getElementById("item-row" + id).remove();
                        }
                    },
                    error: function (jqXHR, status, err) {
                        console.log(jqXHR);
                    }
                });
            }
        }

        function activateItem(event, id)
        {
            var _delete = confirm('Activate this item?');

            let url = new URL(window.location.href);
            url.searchParams.set('scrollPosition', scroll);

            document.getElementById('redirect-url').value = url.toString();

            if(_delete)
            {
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: "{{ url('/item/activate') }}/" + id,
                    type: 'PUT',
                    success: function(response){
                        console.log(response);
                        if(response == 'activated')
                        {
                            // window.location.href = "{{ url('/items') }}";
                            // window.location.href = url.toString();
                            // document.getElementById("item-row" + id).remove();
                            console.log($(event.target).parent().empty().append(`<span class="badge badge-success pt-2 pb-4 mb-1" style="width: 100%; height: 20px; font-size: 100%;">Active</span>
                                                                                <button type="button" class="btn btn-warning p-0" onclick="deactivateItem(event, ` + id + `)" style="width: 80%; height: 20%; font-size: 90%;">Deactivate</button>`));
                        }
                    },
                    error: function (jqXHR, status, err) {
                        console.log(jqXHR);
                    }
                });
            }
        }
        
        function deactivateItem(event, id)
        {
            var _delete = confirm('Deactivate this item?');

            let url = new URL(window.location.href);
            url.searchParams.set('scrollPosition', scroll);

            document.getElementById('redirect-url').value = url.toString();

            if(_delete)
            {
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: "{{ url('/item/deactivate') }}/" + id,
                    type: 'PUT',
                    success: function(response){
                        console.log(response);
                        if(response == 'deactivated')
                        {
                            // window.location.href = "{{ url('/items') }}";
                            // window.location.href = url.toString();
                            // document.getElementById("item-row" + id).remove();
                            console.log($(event.target)
                                        .parent()
                                        .empty()
                                        .append(`<span class="badge badge-danger pt-2 pb-4 mb-1" style="width: 100%; height: 20px; font-size: 100%;">Inactive</span>
                                                <button type="button" class="btn btn-success p-0" onclick="activateItem(event, ` + id + `)" style="width: 80%; height: 20%; font-size: 90%;">Activate</button>`));
                        }
                    },
                    error: function (jqXHR, status, err) {
                        console.log(jqXHR);
                    }
                });
            }
        }

        function searchItem(event)
        {
            if(event.which == 13)
            {
                window.location.href = "{{ url('/items?searchValue=') }}" + event.target.value;
            }
        }

        $(window).scroll(function (event) {
            scroll = $(window).scrollTop();
        });

        $(document).ready(function() {
            let url = new URL(window.location.href);
            scroll = url.searchParams.get('scrollPosition') != null? url.searchParams.get('scrollPosition'): 0;
            
            window.scrollTo(0, scroll);
        });
    </script>
@endsection