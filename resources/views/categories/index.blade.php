@extends('master')

@section('content')
<div id="spinner" class=""></div>
<div id="overlay" class=""></div>
    <div class="container">
        @if(allowed(4, 'make'))
        <div class="row pb-3">
            <a href="{{ url('/category/create') }}" class="btn btn-primary offset-10">Add category</a>
        </div>
        @endif
        <div class="row">
            <div class="col-12">
                <input type="text" value="{{ $searchValue }}" class="form-control float-right col-3" placeholder="Search Category            Enter to search" name="search_value" onkeypress="searchCategory(event)">
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <table class="table table-bordered table-hover" id="table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Picture</th>
                            @if(allowed(4, 'edit'))
                            <th>Edit</th>
                            @endif
                            @if(allowed(4, 'remove'))
                            <th>Delete</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($categories as $category)
                        <tr>
                            <td>{{ $category->name }}</td>
                            <td><img src="{{ url('/uploads/') . '/' . ($category->image ?? 'noimage.png') }}" class="img-thumbnail" width="120" /></td>
                            @if(allowed(4, 'edit'))
                            <td><a href="{{ url('/category/edit/') . '/' . $category->id }}" class="btn btn-primary">Edit</a></td>
                            @endif
                            @if(allowed(4, 'remove'))
                            <td><button class="btn btn-danger" onclick="deleteCategory({{ $category->id }})">Delete</button></td>
                            @endif
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-12">
            @if(request()->input('searchValue'))
            {{ $categories->appends(['searchValue' => request()->input('searchValue')])->links() }}
            @else
            {{ $categories->links() }}
            @endif
        </div>
    </div>

    <script type="application/javascript">
        function deleteCategory(id)
        {
            var _delete = confirm('Delete this category?');

            if(_delete)
            {
                $("#overlay").addClass("overlay");
                $("#spinner").addClass("spinner");

                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: "{{ url('/category/') }}" + '/' + id,
                    type: 'DELETE',
                    success: function(response){
                        console.log(response);
                        if(response == 'deleted')
                        {
                            $("#overlay").removeClass("overlay");
                            $("#spinner").removeClass("spinner");
                            window.location.href = "{{ url('/categories') }}";
                        }
                    },
                    error: function (jqXHR, status, err) {
                        console.log(jqXHR);
                    }
                });
            }
        }

        function searchCategory(event)
        {
            if(event.which == 13)
            {
                console.log(event.target.value);
                window.location.href = "{{ url('/categories?searchValue=') }}" + event.target.value;
            }
        }

        $(document).ready(function() {
            // $('#table').DataTable();
        });
    </script>
@endsection