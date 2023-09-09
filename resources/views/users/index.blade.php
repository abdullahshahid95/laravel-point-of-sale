@extends('master')

@section('content')
<div id="spinner" class=""></div>
<div id="overlay" class=""></div>
    <div class="container">
        @if(Session::get('message'))
            <div class="alert alert-success">
                {{ Session::get('message') }}
            </div>
        @endif
        <div class="row pb-3">
            <a href="{{ url('/register') }}" class="btn btn-primary offset-10">Register user</a>
        </div>
        <div class="row">
            <div class="col-12">
                <table class="table" id="table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Username</th>
                            <th>Role</th>
                            @if(allowed(18, 'edit'))
                            <th>Edit</th>
                            @endif
                            @if(allowed(18, 'remove'))
                            <th>Delete</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $user)
                        <tr>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->username }}</td>
                            <td>{{ $user->role->name }}</td>
                            @if(allowed(18, 'edit'))
                            <td><a href="{{ url('/user/edit/') . '/' . $user->id }}" class="btn btn-primary">Edit</a></td>
                            @endif
                            @if(allowed(18, 'remove'))
                            <td><button class="btn btn-danger" onclick="deleteUser({{ $user->id }})">Delete</button></td>
                            @endif
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script type="application/javascript">
        function deleteUser(id)
        {
            var _delete = confirm('Delete this user?');

            if(_delete)
            {
                $("#overlay").addClass("overlay");
                $("#spinner").addClass("spinner");

                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: "{{ url('/user/') }}/" + id,
                    type: 'DELETE',
                    success: function(response){
                        console.log(response);
                        if(response == 1)
                        {
                            $("#overlay").removeClass("overlay");
                            $("#spinner").removeClass("spinner");

                            window.location.href = "{{ url('/users') }}";
                        }
                        else
                        {
                            $("#overlay").removeClass("overlay");
                            $("#spinner").removeClass("spinner");
                            
                            window.location.href = '/';
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