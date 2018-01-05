@extends('layouts.admin_layout')

@section('title')
	Classes
@endsection

@section('styles')
  <link rel="stylesheet" href="{{ asset('/css/classes.css') }}" />
@endsection

@section('content')

<div class="container">
    <div class="row">
        <div class="col-md-8 col-lg-8 col-sm-8 col-xs-8"></div>
        <div class="col-md-4 col-lg-4 col-sm-4 col-xs-4" style="padding: 10px;">
            <button type="button" class="btn btn-md btn-success pull-right" data-toggle="modal" id="add-btn" data-target="#addOrEditClass"> Add A Class
            </button>
        </div>
    </div>
</div>

<div class="card" style="margin:10px;">
    <div class="row" style="padding: 30px;">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Year</th>
                    <th>Short Form</th>
                    <th>Name</th>
                    <th>Options</th>
                </tr>
            </thead>
            <tbody>
                @foreach($klasses as $klass)
                <tr>
                    <td>{{ $klass->year->name }}</td>
                    <td>{{ $klass->short_form }}</td>
                    <td>{{ $klass->name }}</td>
                    <td><button type="button" class="btn btn-primary" data-cid="{{ $klass->class_id }}" data-yid="{{ $klass->year->year_id }}" data-name="{{ $klass->name }}" data-sform="{{ $klass->short_form }}" data-toggle="modal" id="edit-btn" data-target="#addOrEditClass">Edit</button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    {{ $klasses->links('pagination.circle-pagination') }}
</div>


<!--Add Class Modal-->
<div id="addOrEditClass" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Add A Class</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <form class="form-horizontal form-material" method="post" action="addOrUpdateClass">
        {{ csrf_field() }}
        <div class="modal-body">
            <div class="container">
              <input type="hidden" name="class_id" class="input-class-id" />
              <div class="form-group">
                <label class="col-md-12">Year</label>
                  <div class="col-md-12">
                    <select class="form-control input-year-id" name="year_id" style="margin-top: 7px;" required>
                      @foreach($years as $year)
                        <option value="{{ $year->year_id }}">{{ $year->name }}</option>
                      @endforeach
                    </select>
                  </div>
              </div>
              <div class="form-group">
                <label class="col-md-12">Name</label>
                  <div class="col-md-12">
                    <input class="form-control form-control-line input-class-name" name="name" type="text" required>
                  </div>
              </div>
              <div class="form-group">
                <label class="col-md-12">Short Form</label>
                  <div class="col-md-12">
                    <input class="form-control form-control-line input-class-short-form" name="short_form" type="text" required>
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
        var yearId = $(this).data('yid');
        var name = $(this).data('name');
        var classId = $(this).data('cid');
        var shortForm = $(this).data('sform');

        $('.input-year-id').val(yearId);
        $('.input-class-id').val(classId);
        $('.input-class-short-form').val(shortForm);
        $('.input-class-name').val(name);
      });

      $(document).on("click", "#add-btn", function () {
        $('.input-year-id').val('');
        $('.input-class-id').val('');
        $('.input-class-short-form').val('');
        $('.input-class-name').val('');
      });
  </script>
@endsection