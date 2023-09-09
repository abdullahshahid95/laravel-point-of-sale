@extends('master')

@section('content')
    <div class="container">
        @if(allowed(15, 'make'))
        <div class="row pb-3">
            <a href="{{ url('/specific-price/create') }}" class="btn btn-primary offset-10">Add CSP(s)</a>
        </div>
        @endif`
        <div class="row mb-1">
            <div class="col-12">
                <input type="text" value="{{ $searchValue }}" class="form-control float-right col-3" placeholder="Item/Customer              Enter to search" name="search_value" onkeypress="searchItemOrCustomer(event)">
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
                            <th>Item</th>
                            <th>Customer</th>
                            <th>Normal Price</th>
                            <th>Customer Price</th>
                            @if(allowed(15, 'edit'))
                            <th>Edit</th>
                            @endif
                            @if(allowed(15, 'remove'))
                            <th>Delete</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($specificPrices as $csp)
                        <tr id="item-row{{ $csp->csp_id }}">
                            <td>{{ $csp->item_name }}</td>
                            <td>{{ $csp->customer_name }}</td>
                            <td>
                                @if($csp->unit_id == 2)
                                {{ round($csp->normal_price * 12) . ' /Dozen' }}  
                                @elseif($csp->unit_id == 1)
                                    {{ $csp->normal_price . ' /kg' }}
                                @else
                                    {{ $csp->normal_price . ' /piece' }}
                                @endif
                            </td>
                            <td>
                                @if($csp->unit_id == 2)
                                {{ round($csp->customer_price * 12) . ' /Dozen' }}  
                                @elseif($csp->unit_id == 1)
                                    {{ $csp->customer_price . ' /kg' }}
                                @else
                                    {{ $csp->customer_price . ' /piece' }}
                                @endif
                            </td>
                            @if(allowed(15, 'edit'))
                            <td><button type="button" class="btn btn-primary" onclick="openEditDialogue({{ $csp->csp_id }}, {{ $csp->item_id }}, '{{ $csp->item_name }}', {{ $csp->customer_id }}, '{{ $csp->customer_name }}', {{ $csp->normal_price }}, {{ $csp->customer_price }})">Edit</button></td>
                            @endif
                            @if(allowed(15, 'remove'))
                            <td><button type="button" class="btn btn-danger" onclick="deleteCSP(event, {{ $csp->csp_id }})">Delete</button></td>
                            @endif
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-12">
            @if(request()->input('searchValue'))
            {{ $specificPrices->appends(['searchValue' => request()->input('searchValue')])->links() }}
            @else
            {{ $specificPrices->links() }}
            @endif
        </div>
    </div>
    
    <!-- The Modal -->
    <div id="edit-csp-modal" class="edit-item-modal">
        <!-- Modal content -->
        <div class="edit-item-modal-content">
            <div class="row">
                <div class="col-6">
                    <h3>Edit Item</h3>
                </div>
                <div class="col-6">
                    <span id="edit-csp-modal-close" class="edit-item-modal-close" onclick="closeEditDialogue()">&times;</span>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="form-group col-12">
                    <div class="col-6 pl-5">
                        <form id="edit-csp-form" action="" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <input type="hidden" id="redirect-url" name="redirect_url"/>
                        <div class="form-group row">
                            <label for="customer_id" class="col-md-4 col-form-label text-md-right">Customer</label>
                            <div class="col-md-6">
                                <select id="customer_id" class="form-control @error('customer_id') is-invalid @enderror" name="customer_id" required>
                                    <option value="">Please select a Customer</option>
                                    @foreach ($customers as $customer)
                                    <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                                    @endforeach
                                </select>
                                @error('customer_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="item_id" class="col-md-4 col-form-label text-md-right">Item</label>
                            <div class="col-md-6">
                                <select id="item_id" class="form-control @error('item_id') is-invalid @enderror" name="item_id" required>
                                    <option value="">Please select an Item</option>
                                    @foreach ($items as $item)
                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                    @endforeach
                                </select>
                                @error('item_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="sale_price" class="col-md-4 col-form-label text-md-right">Customer Price</label>
                            <div class="col-md-6">
                                <input id="sale_price" type="number" min="0" class="form-control @error('sale_price') is-invalid @enderror" name="sale_price" value="" autocomplete="sale_price" step="0.01">
                
                                @error('sale_price')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-md-4"></div>
                            <div class="col-md-6">
                                <button class="btn btn-primary float-right" type="submit">Save</button>
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

        function openEditDialogue(cspId, itemId, itemName, customerId, customerName, normalPrice, customerPrice) 
        {
            var modal = document.getElementById("edit-csp-modal");
            modal.style.display = "block";

            document.getElementById("customer_id").value = customerId;
            document.getElementById("item_id").value = itemId;
            document.getElementById("sale_price").value = customerPrice;

            document.getElementById("edit-csp-form").action = "{{ url('/specific-price/') }}/" + cspId;

            let url = new URL(window.location.href);
            url.searchParams.set('scrollPosition', scroll);

            document.getElementById('redirect-url').value = url.toString();

            $("#customer_id").select2();
            $("#item_id").select2();
        }

        function closeEditDialogue()
        {
            var modal = document.getElementById("edit-csp-modal");
            modal.style.display = "none";
        }

        function deleteCSP(event, id)
        {
            var _delete = confirm('Delete this CSP?');

            let url = new URL(window.location.href);
            url.searchParams.set('scrollPosition', scroll);

            document.getElementById('redirect-url').value = url.toString();

            if(_delete)
            {
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: "{{ url('/specific-price/') }}/" + id,
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

        function searchItemOrCustomer(event)
        {
            if(event.which == 13)
            {
                window.location.href = "{{ url('/specific-prices?searchValue=') }}" + event.target.value;
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