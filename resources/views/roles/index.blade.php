@extends('master')

@section('content')
<div id="spinner" class=""></div>
<div id="overlay" class=""></div>
    <div class="container">
        <div class="row pb-3">
            <a href="{{ url('/role/create') }}" class="btn btn-primary offset-10">Add Role</a>
        </div>
        <div class="row">
            <div class="col-12">
                <table class="table" id="table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            @if(allowed(19, 'edit'))
                            <th>Edit</th>
                            @endif
                            @if(allowed(19, 'remove'))
                            <th>Delete</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($roles as $role)
                        <tr>
                            <td>{{ $role->name }}</td>
                            @if(allowed(19, 'edit'))
                            <td><a href="{{ url('/role/edit/') . '/' . $role->id }}" class="btn btn-primary">Edit</a></td>
                            @endif
                            @if(allowed(19, 'remove'))
                            <td><button class="btn btn-danger" onclick="deleteRole({{ $role->id }})">Delete</button></td>
                            @endif
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script type="application/javascript">
        function deleteRole(id)
        {
            var _delete = confirm('Delete this role?');

            if(_delete)
            {
                $("#overlay").addClass("overlay");
                $("#spinner").addClass("spinner");

                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: "{{ url('/role/') }}/" + id,
                    type: 'DELETE',
                    success: function(response){
                        console.log(response);
                        if(response == 1)
                        {
                            $("#overlay").removeClass("overlay");
                            $("#spinner").removeClass("spinner");

                            window.location.href = "{{ url('/roles') }}";
                        }
                        else
                        {
                            $("#overlay").removeClass("overlay");
                            $("#spinner").removeClass("spinner");
                            
                            window.location.href = "{{ url('/') }}";
                        }
                    },
                    error: function (jqXHR, status, err) {
                        console.log(jqXHR);
                    }
                });
            }
        }

        $(document).ready(function() {
            $('#table').DataTable();
        });
    </script>
@endsection