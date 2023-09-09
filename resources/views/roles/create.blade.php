@extends('master')

@section('content')
<div id="spinner" class=""></div>
<div id="overlay" class=""></div>
<div class="container">
    <form enctype="multipart/form-data" method="POST" onsubmit="traditionalSubmit(event)">
    @csrf

        <div class="row">
            <div class="col-12">
                <div class="row">
                    <h2>Add Role</h2>
                </div>
                <hr>
                <div class="row">
                    <div class="col-8">
                        <div class="form-group row">
                            <label for="name" class="col-2 col-form-label text-md-right">Role name</label>
                            <div class="col-4">
                                <input type="text" id="name" class="form-control @error('name') is-invalid @enderror" name="name" required>
                                @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="float-right">
                            <input type="checkbox" id="select-all" onchange="toggleSelectAll(this)">
                            <label for="select-all">Select All</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <th>Permissions</th>
                                <th>View</th>
                                <th>Create</th>
                                <th>Edit</th>
                                <th>Delete</th>
                            </thead>
                            <tbody id="permission-rows">
                            @foreach ($permissions as $permission)
                                <tr>
                                    <td>{{ $permission->name }}</td>
                                    <td>
                                        <input type="checkbox" data-id="{{ $permission->id }}" id="{{ 'view_' . $permission->name }}" value="0" onclick="onPermissionCheck('{{ 'view_' . $permission->name }}')" class="">
                                    </td>
                                    <td>
                                        <input type="checkbox" data-id="{{ $permission->id }}" id="{{ 'make_' .$permission->name }}" value="0" onclick="onPermissionCheck('{{ 'make_' .$permission->name }}')" class="">
                                    </td>
                                    <td>
                                        <input type="checkbox" data-id="{{ $permission->id }}" id="{{ 'edit_' .$permission->name }}" value="0" onclick="onPermissionCheck('{{ 'edit_' .$permission->name }}')" class="">
                                    </td>
                                    <td>
                                        <input type="checkbox" data-id="{{ $permission->id }}" id="{{ 'remove_' .$permission->name }}" value="0" onclick="onPermissionCheck('{{ 'remove_' .$permission->name }}')" class="">
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-md-4"></div>
                    <div class="col-md-6">
                        <button class="btn btn-primary float-right" type="button" onclick="submitForm()">Submit</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script type="text/javaScript">
    function onPermissionCheck(id)
    {
        var checkbox = document.getElementById(id);

        if(checkbox.checked)
        {
            checkbox.value = 1;
        }
        else
        {
            checkbox.value = 0;
        }
    }

    function submitForm()
    {
        if(document.getElementById("name").value != '')
        {
            $("#overlay").addClass("overlay");
            $("#spinner").addClass("spinner");
            
            var permissionRoles = {
                name: document.getElementById("name").value,
                permissions: [
                    {
                        permission_id: document.getElementById("view_Customers").getAttribute("data-id"),
                        view: document.getElementById("view_Customers").value,
                        make: document.getElementById("make_Customers").value,
                        edit: document.getElementById("edit_Customers").value,
                        remove: document.getElementById("remove_Customers").value,
                    },
                    {
                        permission_id: document.getElementById("view_Earnings").getAttribute("data-id"),
                        view: document.getElementById("view_Earnings").value,
                        make: document.getElementById("make_Earnings").value,
                        edit: document.getElementById("edit_Earnings").value,
                        remove: document.getElementById("remove_Earnings").value,
                    },
                    {
                        permission_id: document.getElementById("view_Expenses").getAttribute("data-id"),
                        view: document.getElementById("view_Expenses").value,
                        make: document.getElementById("make_Expenses").value,
                        edit: document.getElementById("edit_Expenses").value,
                        remove: document.getElementById("remove_Expenses").value,
                    },
                    {
                        permission_id: document.getElementById("view_Categories").getAttribute("data-id"),
                        view: document.getElementById("view_Categories").value,
                        make: document.getElementById("make_Categories").value,
                        edit: document.getElementById("edit_Categories").value,
                        remove: document.getElementById("remove_Categories").value,
                    },
                    {
                        permission_id: document.getElementById("view_Items").getAttribute("data-id"),
                        view: document.getElementById("view_Items").value,
                        make: document.getElementById("make_Items").value,
                        edit: document.getElementById("edit_Items").value,
                        remove: document.getElementById("remove_Items").value,
                    },
                    {
                        permission_id: document.getElementById("view_Purchases").getAttribute("data-id"),
                        view: document.getElementById("view_Purchases").value,
                        make: document.getElementById("make_Purchases").value,
                        edit: document.getElementById("edit_Purchases").value,
                        remove: document.getElementById("remove_Purchases").value,
                    },
                    {
                        permission_id: document.getElementById("view_Inventory").getAttribute("data-id"),
                        view: document.getElementById("view_Inventory").value,
                        make: document.getElementById("make_Inventory").value,
                        edit: document.getElementById("edit_Inventory").value,
                        remove: document.getElementById("remove_Inventory").value,
                    },
                    {
                        permission_id: document.getElementById("view_Wastage").getAttribute("data-id"),
                        view: document.getElementById("view_Wastage").value,
                        make: document.getElementById("make_Wastage").value,
                        edit: document.getElementById("edit_Wastage").value,
                        remove: document.getElementById("remove_Wastage").value,
                    },
                    {
                        permission_id: document.getElementById("view_Orders").getAttribute("data-id"),
                        view: document.getElementById("view_Orders").value,
                        make: document.getElementById("make_Orders").value,
                        edit: document.getElementById("edit_Orders").value,
                        remove: document.getElementById("remove_Orders").value,
                    },
                    {
                        permission_id: document.getElementById("view_Sale").getAttribute("data-id"),
                        view: document.getElementById("view_Sale").value,
                        make: document.getElementById("make_Sale").value,
                        edit: document.getElementById("edit_Sale").value,
                        remove: document.getElementById("remove_Sale").value,
                    },
                    {
                        permission_id: document.getElementById("view_Rates").getAttribute("data-id"),
                        view: document.getElementById("view_Rates").value,
                        make: document.getElementById("make_Rates").value,
                        edit: document.getElementById("edit_Rates").value,
                        remove: document.getElementById("remove_Rates").value,
                    },
                    {
                        permission_id: document.getElementById("view_Reports").getAttribute("data-id"),
                        view: document.getElementById("view_Reports").value,
                        make: document.getElementById("make_Reports").value,
                        edit: document.getElementById("edit_Reports").value,
                        remove: document.getElementById("remove_Reports").value
                    },
                    {
                        permission_id: document.getElementById("view_Supplier").getAttribute("data-id"),
                        view: document.getElementById("view_Supplier").value,
                        make: document.getElementById("make_Supplier").value,
                        edit: document.getElementById("edit_Supplier").value,
                        remove: document.getElementById("remove_Supplier").value
                    },
                    {
                        permission_id: document.getElementById("view_PurchaseOrder").getAttribute("data-id"),
                        view: document.getElementById("view_PurchaseOrder").value,
                        make: document.getElementById("make_PurchaseOrder").value,
                        edit: document.getElementById("edit_PurchaseOrder").value,
                        remove: document.getElementById("remove_PurchaseOrder").value
                    },
                    {
                        permission_id: document.getElementById("view_CSP").getAttribute("data-id"),
                        view: document.getElementById("view_CSP").value,
                        make: document.getElementById("make_CSP").value,
                        edit: document.getElementById("edit_CSP").value,
                        remove: document.getElementById("remove_CSP").value
                    },
                    {
                        permission_id: document.getElementById("view_Dashboard").getAttribute("data-id"),
                        view: document.getElementById("view_Dashboard").value,
                        make: document.getElementById("make_Dashboard").value,
                        edit: document.getElementById("edit_Dashboard").value,
                        remove: document.getElementById("remove_Dashboard").value
                    },
                    {
                        permission_id: document.getElementById("view_Settings").getAttribute("data-id"),
                        view: document.getElementById("view_Settings").value,
                        make: document.getElementById("make_Settings").value,
                        edit: document.getElementById("edit_Settings").value,
                        remove: document.getElementById("remove_Settings").value
                    },
                    {
                        permission_id: document.getElementById("view_Users").getAttribute("data-id"),
                        view: document.getElementById("view_Users").value,
                        make: document.getElementById("make_Users").value,
                        edit: document.getElementById("edit_Users").value,
                        remove: document.getElementById("remove_Users").value
                    },
                    {
                        permission_id: document.getElementById("view_Roles").getAttribute("data-id"),
                        view: document.getElementById("view_Roles").value,
                        make: document.getElementById("make_Roles").value,
                        edit: document.getElementById("edit_Roles").value,
                        remove: document.getElementById("remove_Roles").value
                    },
                    {
                        permission_id: document.getElementById("view_Barcodes").getAttribute("data-id"),
                        view: document.getElementById("view_Barcodes").value,
                        make: document.getElementById("make_Barcodes").value,
                        edit: document.getElementById("edit_Barcodes").value,
                        remove: document.getElementById("remove_Barcodes").value
                    }
                ]
            }

            $.ajax({
                type: 'POST',
                url: "{{ url('/role') }}",
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                data: JSON.stringify(permissionRoles),
                success: function(response){
                    if(response == 1)
                    {

                        $("#overlay").removeClass("overlay");
                        $("#spinner").removeClass("spinner");

                        alert("Role added.");

                        window.location.href = "{{ url('/roles') }}";
                    }
                    else
                    {
                        window.location.href = "{{ url('/') }}";
                    }
                },
                error: function(response){
                    console.log(response);
                }
            });
        }
    }

    function traditionalSubmit(event)
    {
        event.preventDefault();
    }

    function toggleSelectAll(checkbox) 
    {
        if(checkbox.checked == true)
        {
            $('#permission-rows tr').each(function (i, row) {
                $cells = $(this).children();
                $cells.each(function(j, cell) {
                    $eachCheckbox = $(cell).find("input");
                    $eachCheckbox.prop('checked', true);

                    $eachCheckbox.val(1);
                });
            });
        }
        else
        {
            $('#permission-rows tr').each(function (i, row) {
                $cells = $(this).children();
                $cells.each(function(j, cell) {
                    $eachCheckbox = $(cell).find("input");
                    $eachCheckbox.prop('checked', false);

                    $eachCheckbox.val(0);
                });
            });
        }
    }

    $(document).ready(function() {
        $('#permission-rows tr').on('click', 'td', function (event) {
            if (event.target.type !== 'checkbox') {
                let checkbox = $(this).find("input");

                $(':checkbox', this).trigger('click');
            }
        });
    });
</script>
@endsection