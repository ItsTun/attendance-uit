@extends('layouts.admin_layout')

@section('title')
	Students
@endsection

@section('styles')
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link rel="stylesheet" href="{{ asset('/css/students.css') }}" />
  <link rel="stylesheet" href="{{ asset('/select2/css/select2.min.css') }}" />
@endsection

@section('content')
<div class="container">
  <div class="row">
    <div class="col-md-4">
      <div style="padding: 20px 0px 0px 60px;">
          <button class="btn btn-success" data-toggle="modal" id="add-btn" data-target="#addOrEditStudent">Add New Student</button>
          <button class="btn btn-success" onclick="window.location='{{ route("students.csv") }}'">Import CSV</button>
      </div>
    </div>
  </div>
</div>
<form action="#" method="get">
  <div class="card" style="margin: 7px;">
      <div class="card-block">
          <div class="container">
              <div class="row">
                <div class="col-md-3">
                  <div class="form-group form-material">
                      <label>Roll No</label>
                      <input class="form-control form-control-line" name="r_q" type="text" value="{{ $roll_no_query }}" />
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-group form-material">
                      <label>Name</label>
                      <input class="form-control form-control-line" name="q" type="text" value="{{ $name_query }}"/>
                  </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group form-material">
                        <label>Class</label>
                        <select class="form-control form-control-line" name="c_id">
                            <option value="-1">All</option>
                            @foreach($years as $year)
                              <optgroup label="{{ $year->name }}">
                                @foreach($year->klasses as $klass)
                                  <option value="{{ $klass->class_id }}" @if($klass->class_id==$class_id) selected="selected" @endif>{{ $klass->name }}</option>
                                @endforeach
                              </optgroup>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-3 af-wrappter">
                  <input type="submit" class="btn btn-success af" value="Apply Filters" />
                </div>
              </div>
      </div>
  </div>
</form>

<div class="card" style="margin: 7px;">
    <div class="row" style="padding: 30px;">
      @if(sizeof($students) > 0)
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Roll No</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Options</th>
                </tr>
            </thead>
            <tbody>
                @foreach($students as $student)
                  <tr>
                    <td>{{ $student->roll_no }}</td>
                    <td>{{ $student->name }}</td>
                    <td>{{ $student->email }}</td>
                    <td>
                      <button type="button" id="edit-btn" class="btn btn-primary" data-toggle="modal" data-target="#addOrEditStudent" data-roll-no="{{ $student->roll_no }}" data-name="{{ $student->name }}" data-email="{{ $student->email }}" data-class-id="{{ $student->class_id }}">Edit</button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
          <div class="col-md-12">
            <center>No students found.</center>
          </div>
        @endif
    </div>
    {{ $students->links() }}
</div>


<!--Add Student Modal-->
<div id="addOrEditStudent" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!--Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Add Student</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <form onsubmit="return onSubmitBtnClick()" class="form-horizontal form-material">
      <div class="modal-body">
          <div class="container">            
                <div class="form-group">
                  <label class="col-md-12">Roll No</label>
                    <div class="col-md-12" style="margin-top: 5px;">
                      <input type="hidden" disabled="disabled" class="prefix" name="prefix" />
                      <table>
                        <tr>
                          <td><label class="label-prefix">3CS-</label></td>
                          <td width="100%;"><input id="roll_no" name="roll_no" class="form-control form-control-line input-roll-no" type="text" onkeypress='return event.charCode >= 48 && event.charCode <= 57' required/></td>
                        </tr>
                      </table>
                    </div>
                </div>
                <div class="form-group">
                  <label class="col-md-12">Name</label>
                    <div class="col-md-12">
                      <input class="form-control form-control-line input-name" name="name" type="text" required/>
                    </div>
                </div>
                <div class="form-group">
                  <label class="col-md-12">Email</label>
                    <div class="col-md-12">
                      <input class="form-control form-control-line input-email" name="email" type="email" required/>
                    </div>
                </div>
                <div class="form-group">
                  <label class="col-md-12">Class</label>
                    <div class="col-md-12" style="margin-top: 10px;">
                      <select class="class-select form-control form-control-line" name="class_id" onchange="onClassChange()" style="width: 100%;" name="c_id">
                        @foreach($years as $year)
                          <optgroup label="{{ $year->name }}">
                            @foreach($year->klasses as $klass)
                              <option value="{{ $klass->class_id }}" data-short-form="{{ $klass->short_form }}" @if($klass->class_id==$class_id) selected="selected" @endif>{{ $klass->name }}</option>
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
        <input type="submit" class="btn btn-success" id="savedata" value="Save" />
      </div>
      </form>
    </div>
  </div>
</div>
<!-- Add Student Modal -->
@endsection

@section('scripts')
  <script src="{{ asset('/select2/js/select2.min.js') }}"></script>
  <script>
    var isUpdate = false;
    var oldRollNo = '';
    var oldEmail = '';
    $(document).ready(function() {
      $('.class-select').select2({
        placeholder: "Select classes that this subject will be taught",
      });

      onClassChange();
    });

    function postRecord() {
      $.ajax({
        type: "POST",
        data: {
          "old_r": oldRollNo,
          "prefix": $('.prefix').val(),
          "roll_no": $('.input-roll-no').val(),
          "name": $('.input-name').val(),
          "email": $('.input-email').val(),
          "class_id": $('.class-select').val(),
        },
        url: "addOrUpdateStudent", 
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(result){
          alert("Saved successfully.");
          window.location = "students";
        },
        error: function(error) {
          console.log(error);
          alert("Failed to save");
        }
      });
      console.log('Post Record');
    }

    function checkEmail() {
      var email = $('.input-email').val();
      if((isUpdate && oldEmail != email) || !isUpdate) {
        $.ajax({
          method: "GET",
          data: {
            "email": email
          },
          url: "getStudentWithEmail",
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          success: function(result) {
            if(result!='null') alert('Another student with email '+ email +' already existed');
            else postRecord();
          },
          error: function(error) {
            alert('Oops! Something went wrong');
          }
        });
      } else {
        postRecord();
      }
    }

    function onSubmitBtnClick() {
      var roll_no = $('.prefix').val() + $('.input-roll-no').val();
      if((isUpdate && oldRollNo != roll_no) || !isUpdate) {
        $.ajax({
          method: "GET",
          data: {
            "roll_no": roll_no
          },
          url: "getStudent",
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          success: function(result) {
            if(result!='null') alert('Another student with roll number '+ roll_no +' already existed');
            else checkEmail();
          },
          error: function(error) {
            alert('Oops! Something went wrong');
          }
        });
      } else {
        checkEmail();
      }
      
      return false;
    }

    function onClassChange() {
      var selectedOption = $(".class-select option:selected");
      var short_form = selectedOption.data('short-form') + '-';
      $('.prefix').val(short_form);
      $('.label-prefix').html(short_form);
    }

    $(document).on("click", "#edit-btn", function () {
      isUpdate = true;
      var roll_no = $(this).data('roll-no');
      var name = $(this).data('name');
      var email = $(this).data('email');
      var class_id = $(this).data('class-id');

      oldRollNo = roll_no;
      oldEmail = email;

      var splitted = roll_no.split('-');

      $('.prefix').val(splitted[0].trim());
      $('.input-roll-no').val(splitted[1].trim());
      $('.input-name').val(name);
      $('.input-email').val(email);
      $('.class-select').val(class_id);
      $('.class-select').trigger('change');
    });

    $(document).on("click", "#add-btn", function () {
      isUpdate = false;
      oldRollNo = '';
      oldEmail = '';

      $('.prefix').val('');
      $('.input-roll-no').val('');
      $('.input-name').val('');
      $('.input-email').val('');
      $('.class-select').prop('selectedIndex', 0);
      $('.class-select').trigger('change');
      onClassChange();
    });
  </script>
@endsection