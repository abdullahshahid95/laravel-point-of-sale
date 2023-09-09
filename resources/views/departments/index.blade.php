@extends('master')

@section('content')
    <div class="container">
        <div class="row pb-3">
            <a href="{{ url('/department/create') }}" class="btn btn-primary offset-10">Add Department</a>
        </div>
        <div class="row">
            <div class="col-12">
                <table class="table">
                    <tr>
                        <th>Name</th>
                        <th>Delete</th>
                    </tr>
                @foreach ($departments as $department)
                    <tr>
                        <td><a href="{{ url('/department/') . '/' . $department->id }}">{{ $department->name }}</a></td>
                        <td><button class="btn btn-danger" onclick="deleteDepartment({{ $department->id }})">Delete</button></td>
                    </tr>
                @endforeach
                </table>
            </div>
        </div>
        <div class="row">
            <div class="col-12 d-flex justify-content-center">
                {{ $departments->links() }}
            </div>
        </div>
    </div>

    <script type="application/javascript">
        function deleteDepartment(id)
        {
            var _delete = confirm('Delete this department?');

            if(_delete)
            {
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: "{{ url('/department/') }}/" + id,
                    type: 'DELETE',
                    success: function(response){
                        console.log(response);
                        if(response = 'deleted')
                            window.location.href = "{{ url('/departments') }}";
                    },
                    error: function (jqXHR, status, err) {
                        console.log(jqXHR);
                    }
                });
            }
        }
    </script>
@endsection