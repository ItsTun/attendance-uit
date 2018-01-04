@extends('layouts.admin_layout')

@section('title')
	Years
@endsection

@section('styles')
  <link rel="stylesheet" href="{{ asset('/css/years.css') }}" />
@endsection

@section('content')
<div class="container" >
    <div class="row">
        <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8"></div>
        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4" style="padding: 10px;">
          <button type="button" class="btn btn-md btn-success pull-right" data-toggle="modal" id="add-btn" data-target="#addOrEditYear">
            Add A Year
          </button>
        </div>
    </div>
</div>

<div class="card" style="margin:10px;">
    <div class="row" style="padding: 30px;">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Year Code</th>
                    <th>Name</th>
                    <th>Options</th>
                </tr>
            </thead>
            <tbody>
              @foreach($years as $year)
              <tr>
                <td>{{ $year->year_id }}</td>
                <td>{{ $year->name }}</td>
                <td>
                  <button type="button" class="btn btn-primary edit-btn" id="edit-btn" data-toggle="modal" data-id="{{ $year->year_id }}" data-name="{{ $year->name }}" data-target="#addOrEditYear">Edit</button>
                </td>
              </tr>
              @endforeach
            </tbody>
        </table>
    </div>
    
    {{ $years->links('pagination.circle-pagination') }}
    
</div>


<!--Add Year Modal-->
<div id="addOrEditYear" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Add A Year</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <form class="form-horizontal form-material" action="addOrUpdateYear" method="post">
        <div class="modal-body">
            <div class="container">
              {{ csrf_field() }}
              <input type="hidden" type="text" name="year_id" class="input-year-id" />
              <div class="form-group">
                <label class="col-md-12">Name</label>
                  <div class="col-md-12">
                    <input class="form-control form-control-line input-year-name" type="text" name="name" required>
                  </div>
              </div>
            </div>
          </div>
        
        <div class="modal-footer">
          <button type="cancel" class="btn btn-default" data-dismiss="modal">Close</button>
          <input type="submit" class="btn btn-success" id="savedata" value="Save" />
        </div>
      </form>
    </div>
  </div>
</div>

@endsection

@section('scripts')
  <script>
      $(document).on("click", "#edit-btn", function () {
        console.log("clicked");
        var yearId = $(this).data('id');
        var name = $(this).data('name');

        $('.input-year-id').val(yearId);
        $('.input-year-name').val(name);
      });

      $(document).on("click", "#add-btn", function () {
        $('.input-year-id').val('');
        $('.input-year-name').val('');
      });
  </script>
@endsection