@extends('layouts.admin_layout')

@section('title')
    Departments
@endsection

@section('styles')
	<link rel="stylesheet" href="{{ asset('/css/subjects.css') }}" />
  	<link rel="stylesheet" href="{{ asset('/select2/css/select2.min.css') }}" />
@endsection
@section('content')

<div class="container" >
    <div class="row">
        <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8"></div>
        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4" style="padding: 10px;">
          <button type="button" class="btn btn-md btn-success pull-right" data-toggle="modal" id="add-btn" data-target="#addOrEditDepartment">
            Add New Department
          </button>
        </div>
    </div>
</div>

<div class="card" style="margin:10px;">
    <div class="row" style="padding: 30px;">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Department No</th>
                    <th>Department name</th>
                    <th>Classes</th>
                    <th>Options</th>
                </tr>
            </thead>
            <tbody>
            @foreach($faculties as $faculty)
              <tr>
                <td>{{ $faculty->faculty_id}}</td>
                <td>{{ $faculty->name}}</td>
                <td>
                	@foreach($faculty->faculty_class as $faculty_class)
                        <span class="label label-primary" title="{{ $faculty_class->klass->name }}">{{ $faculty_class->klass->short_form }}</span>
                    @endforeach
                </td>
                <td>
                  <button type="button" class="btn btn-primary edit-btn" data-faculty-id="{{ $faculty->faculty_id }}" data-faculty-name="{{ $faculty->name }}" data-faculty-class="{{ $faculty->faculty_class }}" id="edit-btn" data-toggle="modal" data-target="#addOrEditDepartment">Edit</button>
                </td>
              </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    {{ $faculties->links('pagination.circle-pagination') }}
</div>


<!--Add Department Modal-->
<div id="addOrEditDepartment" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Add New Department</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <form class="form-horizontal form-material" action="addOrUpdateFaculty" method="post">
        <div class="modal-body">
            <div class="container">
              {{ csrf_field() }}
              <input type="hidden" type="text" name="faculty_id" class="input-faculty-id" />
              <div class="form-group">
                <label class="col-md-12">Name</label>
                  <div class="col-md-12">
                    <input class="form-control form-control-line input-faculty-name" type="text" name="name" required>
                  </div>
              </div>
              <div class="form-group">
              	<label class="col-md-12" style="margin-bottom: 10px;">Classes</label>
              	<div class="col-md-12">
              		<select class="class-select col-md-12" style="width: 100%;" name="classes[]" multiple="multiple">
              		@foreach($years as $year)
                      <optgroup label="{{ $year->name }}" data-year-id="{{ $year->year_id }}">
                   	    @foreach($year->klasses as $klass)
                          <option value="{{ $klass->class_id }}">{{ $klass->name }}</option>
                        @endforeach
                      </optgroup>
                    @endforeach
                    </select>
              	</div>
              </div>
            </div>
          </div>
        
        <div class="modal-footer">
          <button type="cancel" class="btn btn-default" data-dismiss="modal">Close</button>
          <input type="submit" class="btn btn-success" value="Save">
        </div>
      </form>
    </div>
  </div>
</div>


@endsection

@section('scripts')
<script src="{{ asset('/select2/js/select2.min.js') }}"></script>
<script>
	$(document).ready(function() {
      $('.class-select').select2({
        placeholder: "Select classes within this department",
      });
    });
      $(document).on("click", "#edit-btn", function () {
        console.log("clicked");
        var facultyId = $(this).data('faculty-id');
        var facultyName = $(this).data('faculty-name');
        var facultyClass = $(this).data('faculty-class');

        var classes = [];

        facultyClass.forEach(function (entry) {
        	classes[classes.length] = entry['class_id'];
        });

        $('.input-faculty-id').val(facultyId);
        $('.input-faculty-name').val(facultyName);
        $('.class-select').val(classes);
        $('.class-select').trigger('change');
      });

      $(document).on("click", "#add-btn", function () {
        $('.input-faculty-id').val('');
        $('.input-faculty-name').val('');
        $('.class-select').val('');
        $('.class-select').trigger('change');
      });
  </script>

@endsection