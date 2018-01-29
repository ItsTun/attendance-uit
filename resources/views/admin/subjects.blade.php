@extends('layouts.admin_layout')

@section('title')
	Subjects
@endsection

@section('styles')
  <link rel="stylesheet" href="{{ asset('/css/subjects.css') }}" />
  <link rel="stylesheet" href="{{ asset('/select2/css/select2.min.css') }}" />
@endsection

@section('content')
<div class="card" style="margin: 7px;">
    <div class="card-block">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                  <form action="#" method="get">
                    <div class="form-group form-material">
                        <label class="col-md-12">Search By Name</label>
                        <div class="container">
                            <div class="row">
                                <div class="col-sm-9">
                                    <input class="form-control form-control-line" type="text" name="q" value="{{ $query }}" /></div>
                                <div class="col-sm-2" style="padding-left: 0px;">
                                    <input type="submit" class="btn btn-success" value="Search" /></div>
                            </div>
                        </div>
                    </div>
                  </form>
                </div>
                
                <div class="col-md-6" style="text-align: right;">
                    <div class="form-material">
                        <div style="padding: 20px 0px 0px 150px;">
                            <button class="btn btn-md btn-success" id="add-btn" data-toggle="modal" data-target="#addOrEditSubject">Add New Subject</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="card" style="margin: 7px;">
    <div class="row" style="padding: 30px;">
      @if(sizeof($subjects) > 0)
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Subject Code</th>
                    <th>Name</th>
                    <th>Classes</th>
                    <th>Options</th>
                </tr>
            </thead>
            <tbody>
                @foreach($subjects as $subject)
                  <tr>
                    <td>{{ $subject->subject_code }}</td>
                    <td>{{ $subject->name }}</td>
                    <td>
                      @foreach($subject->subject_class as $subject_class)
                        <span class="label label-primary" title="{{ $subject_class->klass->name }}">{{ $subject_class->klass->short_form }}</span>
                      @endforeach
                    </td>
                    <td><button type="button" id="edit-btn" class="btn btn-success" data-toggle="modal" data-subject-id="{{ $subject->subject_id }}" data-subject-name="{{ $subject->name }}" data-subject-code="{{ $subject->subject_code }}" data-subject-classes="{{ $subject->subject_class }}" data-target="#addOrEditSubject">Edit</button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
      @else
        <div class="col-md-12"><center> No subjects @if(!is_null($query))for "{{ $query }}"@endif</center></div>
      @endif
    </div>
</div>


<!--Add Subject Modal-->
<div id="addOrEditSubject" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Add Subject</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <form class="form-horizontal form-material" method="post" action="addOrUpdateSubject">
      {{ csrf_field() }}
      <input type="hidden" name="subject_id" class="input-subject-id" />
      <div class="modal-body">
          <div class="container">
                      <div class="form-group">
                        <label class="col-md-12">Subject Code</label>
                          <div class="col-md-12">
                            <input class="form-control form-control-line input-subject-code" type="text" name="subject_code">
                          </div>
                      </div>
                      <div class="form-group">
                        <label class="col-md-12">Name</label>
                          <div class="col-md-12">
                            <input class="form-control form-control-line input-subject-name" type="text" name="name">
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
        <input type="submit" class="btn btn-success" id="savedata" value="Save" />
      </div>
      </form>
    </div>
  </div>
</div>
<!-- Add Subject Modal -->
@endsection

@section('scripts')
  <script src="{{ asset('/select2/js/select2.min.js') }}"></script>
  <script>
    $(document).ready(function() {
      $('.class-select').select2({
        placeholder: "Select classes that this subject will be taught",
      });
    });

    $(document).on("click", "#edit-btn", function () {
      var subjectId = $(this).data('subject-id');
      var subjectName = $(this).data('subject-name');
      var subjectCode = $(this).data('subject-code');
      var subjectClasses = $(this).data('subject-classes');

      var classes = [];

      subjectClasses.forEach(function(entry) {
        classes[classes.length] = entry['class_id'];
      });

      $('.input-subject-id').val(subjectId);
      $('.input-subject-name').val(subjectName);
      $('.input-subject-code').val(subjectCode);
      $('.class-select').val(classes);
      $('.class-select').trigger('change');
    });

    $(document).on("click", "#add-btn", function () {
      $('.input-subject-id').val('');
      $('.input-subject-name').val('');
      $('.input-subject-code').val('');
      $('.class-select').val('');
      $('.class-select').trigger('change');
    });
  </script>
@endsection