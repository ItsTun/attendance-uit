@extends('layouts.admin_layout')

@section('title')
    Teachers
@endsection

@section('styles')
    <link rel="stylesheet" href="{{ asset('/css/teachers.css') }}"/>
    <link rel="stylesheet" href="{{ asset('/select2/css/select2.min.css') }}"/>
@endsection

@section('content')

    <div class="col-md-6">
        <div class="form-material">
            <div style="padding: 20px 0px 0px 150px;">
                <button class="btn btn-success" data-toggle="modal" data-target="#addOrEditTeacher" id="add-btn">Add New
                    Teacher
                </button>
            </div>
        </div>
    </div>
    <form action="#" method="get">
        <div class="card" style="margin: 7px;">
            <div class="card-block">
                <div class="container">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group form-material">
                                <label class="col-md-12">Name</label>
                                <div class="col-md-12">
                                    <input type="text" class="form-control form-control-line" name="q"
                                           value="{{ $query }}"/>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group form-material">
                                <label class="col-md-12">Faculty</label>
                                <div class="col-md-12">
                                    <select class="form-control form-control-line filter-faculty-select" name="f_id">
                                        <option value="-1">All</option>
                                        @foreach($faculties as $faculty)
                                            <option value="{{ $faculty->faculty_id }}">{{ $faculty->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <input type="submit" class="btn btn-success af" value="Apply filter"/>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>


    <div class="card" style="margin: 7px;">
        <div class="row" style="padding: 30px;">
            <table class="table table-hover">
                <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Subjects</th>
                    <th>Faculty</th>
                    <th>Options</th>
                </tr>
                </thead>
                <tbody>
                @foreach($teachers as $teacher)
                    <tr>
                        <td>{{ $teacher->name }}</td>
                        <td>{{ $teacher->email }}</td>
                        <td>
                            @foreach($teacher->subject_teachers as $subject_teacher)
                                <span class="label label-primary"
                                      title="{{ $subject_teacher->subject_class->subject->name }}">
                          {{ $subject_teacher->subject_class->klass->short_form }} -
                                    {{ $subject_teacher->subject_class->subject->subject_code }}
                        </span>
                            @endforeach
                        </td>
                        <td>{{ $teacher->faculty->name }}</td>
                        <td>
                            <button type="button" id="edit-btn" class="btn btn-success"
                                    data-teacher-id="{{ $teacher->teacher_id }}"
                                    data-teacher-name="{{ $teacher->name }}" data-teacher-email="{{ $teacher->email }}"
                                    data-subject-classes="{{ $teacher->subject_teachers }}"
                                    data-teacher-faculty="{{ $teacher->faculty_id }}"
                                    data-class-teacher-of="{{ $teacher->class_teacher_of }}"
                                    data-year-head-of="{{ $teacher->year_head_of }}"
                                    data-is-principle="{{ $teacher->is_principle }}" data-toggle="modal"
                                    data-target="#addOrEditTeacher">Edit
                            </button>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        {{ $teachers->links() }}
        <div id="addOrEditTeacher" class="modal fade" role="dialog">
            <div class="modal-dialog">

                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Teacher</h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <form class="form-horizontal form-material" id="teacher-form" method="post"
                          onsubmit="return onFormSubmit()"
                          action="addOrUpdateTeacher">
                        {{ csrf_field() }}
                        <input type="hidden" name="teacher_id" class="input-teacher-id"/>
                        <div class="modal-body">
                            <div class="container">
                                <div class="form-group">
                                    <label class="col-md-12">Name</label>
                                    <div class="col-md-12">
                                        <input class="form-control form-control-line input-teacher-name" type="text"
                                               name="name" required/>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-12">Email</label>
                                    <div class="col-md-12">
                                        <input class="form-control form-control-line input-teacher-email" type="email"
                                               name="email" required/>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-12" style="margin-bottom: 10px;">Subjects</label>
                                    <div class="col-md-12">
                                        <select class="col-md-12 form-control form-control-line faculty-select"
                                                style="width: 100%;" required name="faculty_id">
                                            @foreach($faculties as $faculty)
                                                <option value="{{$faculty->faculty_id}}">{{ $faculty->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-12" style="margin-bottom: 10px;">Subjects</label>
                                    <div class="col-md-12">
                                        <select class="subject-select col-md-12" style="width: 100%;" required
                                                name="subject_classes[]" multiple="multiple">
                                            @foreach($years as $year)
                                                @foreach($year->klasses as $klass)
                                                    <optgroup label="{{ $year->name }} - {{ $klass->name }}">
                                                        @foreach($klass->subject_class as $subject_class)
                                                            <option value="{{ $subject_class['subject_class_id'] }}">{{ $subject_class->subject['name'] }}</option>
                                                        @endforeach
                                                    </optgroup>
                                                @endforeach
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <input type="checkbox" id="class-teacher-of" onchange="onCheckChanged(this)"/>
                                <label for="class-teacher-of">Class Teacher</label>
                                <div class="col-md-12" style="margin-top: 10px; margin-bottom: 15px;">
                                    <select class="class-teacher-of" name="class_teacher_of"
                                            style="width: 100%;display: none;">
                                        @foreach($years as $year)
                                            <option style="display:none"></option>
                                            <optgroup label="{{ $year->name }}">
                                                @foreach($year->klasses as $klass)
                                                    <option value="{{ $klass->class_id }}">{{ $klass->name }}</option>
                                                @endforeach
                                            </optgroup>
                                        @endforeach
                                    </select>
                                </div>
                                <input type="checkbox" id="year-head-of" onchange="onCheckChanged(this)"/>
                                <label for="year-head-of">Year Head</label>
                                <div class="col-md-12" style="margin-top: 10px; margin-bottom: 15px;">
                                    <select class="year-head-of" name="year_head_of" style="width: 100%;display: none;">
                                        <option style="display:none"></option>
                                        @foreach($years as $year)
                                            <option value="{{ $year->year_id }}">{{ $year->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <input type="checkbox" id="is-principle" name="is_principle" value="is-principle"/>
                                <label for="is-principle">Headmistress / Headmaster</label>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="cancel" class="btn btn-default" data-dismiss="modal">Close</button>
                            <input type="submit" class="btn btn-success" id="savedata" value="Save"/>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        @endsection

        @section('scripts')
        @section('scripts')
            <script src="{{ asset('/select2/js/select2.min.js') }}"></script>
            <script>
                $(document).ready(function () {
                    $('.subject-select').select2({
                        placeholder: "Select subjects that this teacher will teach",
                    });
                    $('.class-teacher-of').select2({
                        placeholder: "Select class",
                    });
                    $('.class-teacher-of').next(".select2-container").hide();
                    $('.year-head-of').select2({
                        placeholder: "Select year",
                    });
                    $('.year-head-of').next(".select2-container").hide();
                    @if(!is_null($faculty_id)) $('.filter-faculty-select').val('{{ $faculty_id }}'); @endif
                });

                var isUpdate = false;
                var oldEmail = '';

                function onFormSubmit() {
                    var email = $('.input-teacher-email').val();
                    if(!$('#class-teacher-of').prop('checked')) {
                        $('.class-teacher-of option:eq(0)').prop('selected', true).trigger('change');
                    }
                    if(!$('#year-head-of').prop('checked')) {
                        $('.year-head-of option:eq(0)').prop('selected', true).trigger('change');
                    }
                    if ((isUpdate && oldEmail != email) || !isUpdate) {
                        var responseText = $.ajax({
                            type: "GET",
                            data: {
                                "email": email,
                            },
                            async: false,
                            url: "getTeacherWithEmail",
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            }
                        }).responseText;
                        if (responseText != null) {
                            alert("Another teacher with email address," + email + ", already existed");
                        }
                        return (responseText == 'null');
                    }
                }

                $(document).on("click", "#edit-btn", function () {
                    isUpdate = true;

                    var teacherId = $(this).data('teacher-id');
                    var teacherName = $(this).data('teacher-name');
                    var teacherEmail = $(this).data('teacher-email');
                    var teacherFaculty = $(this).data('teacher-faculty');
                    var teacherSubjectClasses = $(this).data('subject-classes');
                    var classTeacherOf = $(this).data('class-teacher-of');
                    var yearHeadOf = $(this).data('year-head-of');
                    var isPrinciple = $(this).data('is-principle');

                    console.log(classTeacherOf+','+yearHeadOf+','+isPrinciple);

                    oldEmail = teacherEmail;

                    var subjectClasses = [];

                    teacherSubjectClasses.forEach(function (entry) {
                        subjectClasses[subjectClasses.length] = entry['subject_class_id'];
                    });

                    $('.input-teacher-id').val(teacherId);
                    $('.input-teacher-name').val(teacherName);
                    $('.input-teacher-email').val(teacherEmail);
                    $('.faculty-select').val(teacherFaculty);
                    $('.subject-select').val(subjectClasses);
                    if (classTeacherOf) {
                        $('#class-teacher-of').prop('checked', true);
                        $('.class-teacher-of').val(classTeacherOf).trigger('change');
                        onCheckChanged($('#class-teacher-of'));
                    }
                    if (yearHeadOf) {
                        $('#year-head-of').prop('checked', true);
                        $('.year-head-of').val(yearHeadOf).trigger('change');
                        onCheckChanged($('#year-head-of'));
                    }
                    if(isPrinciple) $('#is-principle').prop('checked', true);
                    $('.subject-select').trigger('change');
                });

                $(document).on("click", "#add-btn", function () {
                    isUpdate = false;
                    oldEmail = '';
                    $('.input-teacher-id').val('');
                    $('.input-teacher-name').val('');
                    $('.input-teacher-email').val('');
                    $('.subject-select').val('');
                    $('.subject-select').trigger('change');
                });

                function onCheckChanged(checkbox) {
                    var id = $(checkbox).attr('id');
                    var isChecked = $(checkbox).prop('checked');
                    if (isChecked) $('.' + id).next(".select2-container").show();
                    else $('.' + id).next(".select2-container").hide();
                }
            </script>
@endsection