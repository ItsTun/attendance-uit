@extends('layouts.admin_layout')

@section('title')
    Medical Leave
@endsection

@section('styles')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('/css/medical_leave.css') }}"/>
    <link rel="stylesheet" href="{{ asset('/select2/css/select2.min.css') }}"/>
    <link href="{{ asset('/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css') }}" rel="stylesheet"/>
    <link href="{{ asset('/css/font-awesome.min.css') }}" rel="stylesheet">
@endsection

@section('content')
    <div class="container" style="padding: 15px !important;">
        <div class="card col-md-12">
            <div class="card-block">
                <div class="row">
                    <div class="col-md-4">
                        <label for="class-select">Class</label>
                        <br/>
                        <select id="class-select" onchange="onClassChange()">
                            @foreach($years as $year)
                                <optgroup label="{{ $year->name }}" data-year-id="{{ $year->year_id }}">
                                    @foreach($year->klasses as $klass)
                                        <option value="{{ $klass->short_form }}">{{ $klass->name }}</option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="class-short-form">Roll No</label>
                        <br/>
                        <span id="class-short-form">5SE-</span>
                        <input type="text" class="form-control form-control-line" placeholder="4" id="roll-no"
                               onblur="doneTyping()"/>
                    </div>
                    <div class="col-md-4">
                        <label for="student-name">Name</label>
                        <br/>
                        <input type="text" class="form-control form-control-line" id="student-name"
                               disabled="disabled"/>
                    </div>
                </div>
                <div class="row" style="margin-top: 10px;">
                    <div class="col-md-4">
                        <label for="leave-from">From</label>
                        <br/>
                        <input type="text" class="form-control form-control-line" id="leave-from"/>
                    </div>
                    <div class="col-md-4">
                        <label for="leave-to">To</label>
                        <br/>
                        <input type="text" class="form-control form-control-line" id="leave-to"/>
                    </div>
                    <div class="col-md-4">
                        <label for="submit-btn">-</label>
                        <br/>
                        <input type="button" class="btn btn-info" id="submit-btn" value="Save" onclick="onSaveClick()"/>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('/select2/js/select2.min.js') }}"></script>
    <script src="{{ asset('/moment/js/moment.min.js') }}"></script>
    <script src="{{ asset('/bootstrap-datetimepicker/js/bootstrap-datetimepicker.js') }}"></script>
    <script>
        $(document).ready(function () {
            $('#class-select').select2();
            onClassChange();
            $('#leave-from').datetimepicker({
                format: 'YYYY-MM-DD'
            });
            $('#leave-to').datetimepicker({
                format: 'YYYY-MM-DD',
                useCurrent: false
            });
            $("#leave-from").on("dp.change", function (e) {
                $('#leave-to').data("DateTimePicker").minDate(e.date);
            });
            $("#leave-to").on("dp.change", function (e) {
                $('#leave-from').data("DateTimePicker").maxDate(e.date);
            });
        });

        var typingTimer;                //timer identifier
        var doneTypingInterval = 1000;  //time in ms, 5 second for example
        var $input = $('#roll-no');
        var short_form = '';
        var is_student_found = false;
        var student = undefined;

        //user is "finished typing," do something
        function doneTyping() {
            $.ajax({
                type: "GET",
                data: {
                    "roll_no": short_form + $input.val()
                },
                url: "{{ route('admin.getStudentFromRollNo') }}",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (result) {
                    is_student_found = (result != '');
                    $('#student-name').val((result != '') ? result['name'] : "Student not found");
                    if (is_student_found) {
                        student = result;
                    } else {
                        student = undefined;
                    }
                },
                error: function (error) {
                    console.log(error);
                    is_student_found = false;
                }
            });
        }

        function onClassChange() {
            var selectedOption = $('#class-select option:selected');
            short_form = selectedOption.val() + '-';
            $('#class-short-form').html(short_form);
        }

        function onSaveClick() {
            if (is_student_found) {
                var student_id = student['student_id'];
                var leave_from = $('#leave-from').val();
                var leave_to = $('#leave-to').val();

                $.ajax({
                    type: "POST",
                    data: {
                        'student_id': student_id,
                        'leave_from': leave_from,
                        'leave_to': leave_to
                    },
                    url: "{{ route('admin.saveMedicalLeave') }}",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (result) {
                        console.log(result);
                        if (result == "Saved") {
                            alert("Saved successfully!");
                            location.reload();
                        } else {
                            alert("Saving failed");
                        }
                    },
                    error: function (error) {
                        alert("Saving failed");
                        console.log(error);
                    }
                });
            } else {
                alert("Student with roll no, " + short_form + $('#roll-no').val() + ", is not found");
            }
        }
    </script>
@endsection