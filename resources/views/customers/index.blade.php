@extends('master')

@section('content')
    <div class="container">
        @if(allowed(1, 'make'))
        <div class="row pb-3">
            <a href="{{ url('/customer/create') }}" class="btn btn-primary offset-10">Add a Customer</a>
        </div>
        @endif
        <div class="row mb-1">
            <div class="col-12">
                <input type="text" value="{{ $searchValue }}" class="form-control float-right col-3" placeholder="Name/Ph/Addr               Enter to search" name="search_value" onkeypress="searchCustomer(event)">
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <table class="table table-hover table-bordered" id="table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Phone</th>
                            <th>Address</th>
                            @if(allowed(1, 'edit'))
                            <th>Edit</th>
                            @endif
                            @if(allowed(1, 'remove'))
                            <th>Delete</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($customers as $customer)
                        <tr>
                            {{-- <td><a href="{{ url('/customer/') . '/' . $customer->id }}" class="orders">{{ $customer->name }}</a></td> --}}
                            <td>{{ $customer->name }}</td>
                            <td>{{ $customer->phone ?? '--' }}</td>
                            <td>{{ $customer->address ?? '--' }}</td>
                            @if(allowed(1, 'edit'))
                            <td><button type="button" class="btn btn-primary" onclick="openEditDialogue({{ $customer->id }}, '{{ $customer->name }}', '{{ $customer->phone }}', '{{ $customer->address }}')">Edit</button></td>
                            @endif
                            @if(allowed(1, 'remove'))
                            <td><button class="btn btn-danger" onclick="deleteCustomer({{ $customer->id }})">Delete</button></td>
                            @endif
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="row">
            <div class="col-12 d-flex justify-content-center">
                {{ $customers->links() }}
            </div>
        </div>
    </div>

    <!-- The Modal -->
    <div id="edit-customer-modal" class="edit-customer-modal">
        <!-- Modal content -->
        <div class="edit-customer-modal-content">
            <div class="row">
                <div class="col-6">
                    <h3>Edit Customer</h3>
                </div>
                <div class="col-6">
                    <span id="edit-customer-modal-close" class="edit-customer-modal-close" onclick="closeEditDialogue()">&times;</span>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="form-group col-12">
                    <div class="col-6 pl-5">
                        <form id="edit-customer-form" action="" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" id="redirect-url" name="redirect_url"/>
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
                                <input id="phone" type="text" class="form-control @error('phone') is-invalid @enderror" name="phone" value="{{ old('phone') }}" required autocomplete="phone">
                
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
                                <textarea rows="10" cols="12" id="address" class="form-control @error('address') is-invalid @enderror" name="address" value="{{ old('address') }}" required autocomplete="address"></textarea>
                
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

    <script type="application/javascript">

        let scroll = 0;

        function openEditDialogue(id, name, phone, address) 
        {
            var modal = document.getElementById("edit-customer-modal");
            modal.style.display = "block";

            document.getElementById("name").value = name;
            document.getElementById("phone").value = phone;
            document.getElementById("address").value = address;

            document.getElementById("edit-customer-form").action = "{{ url('/customer/') }}/" + id;

            let url = new URL(window.location.href);
            url.searchParams.set('scrollPosition', scroll);

            document.getElementById('redirect-url').value = url.toString();
        }

        function closeEditDialogue()
        {
            var modal = document.getElementById("edit-customer-modal");
            modal.style.display = "none";
        }       

        function deleteCustomer(id)
        {
            var _delete = confirm('Delete this customer?');

            if(_delete)
            {
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: "{{ url('/customer/') }}/" + id,
                    type: 'DELETE',
                    success: function(response){
                        if(response == 'deleted')
                            window.location.href = "{{ url('/customers') }}";
                        else
                            console.log(response);
                    },
                    failure: function(response){
                        console.log(response);
                    }
                });
            }
        }

        function searchCustomer(event)
        {
            if(event.which == 13)
            {
                window.location.href = "{{ url('/customers?searchValue=') }}" + event.target.value;
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