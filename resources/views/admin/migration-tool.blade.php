@extends('layouts.admin_layout')

@section('title')
    Migration tool
@endsection

@section('styles')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('/css/migration-tool.css') }}"/>
    <link rel="stylesheet" href="{{ asset('/select2/css/select2.min.css') }}"/>
@endsection

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-5">
                <label for="class-select-from">From</label>
                <select class="class-select-from col-md-12" style="width: 100%;" id="class-select-from" name="classes[]"
                        onchange="loadStudents()">
                    @foreach($years as $year)
                        <optgroup label="{{ $year->name }}" data-year-id="{{ $year->year_id }}">
                            @foreach($year->klasses as $klass)
                                <option value="{{ $klass->class_id }}" data-short-form="{{ $klass->short_form  }}">{{ $klass->name }}</option>
                            @endforeach
                        </optgroup>
                    @endforeach
                </select>
                <div class="table-container">
                    <table class="students-from">
                    </table>
                </div>
            </div>
            <div class="col-md-2"></div>
            <div class="col-md-5">
                <label for="class-select-to">To</label>
                <select class="class-select-to col-md-12" style="width: 100%;" id="class-select-to" name="classes[]"
                        onchange="clearList()">
                    @foreach($years as $year)
                        <optgroup label="{{ $year->name }}" data-year-id="{{ $year->year_id }}">
                            @foreach($year->klasses as $klass)
                                <option value="{{ $klass->class_id }}" data-short-form="{{ $klass->short_form  }}">{{ $klass->name }}</option>
                            @endforeach
                        </optgroup>
                    @endforeach
                </select>
                <form onsubmit="return false;">
                    <div class="table-container">
                        <table class="students-to">
                        </table>
                    </div>
                    <input type="submit" class="btn btn-info btn-save" value="Save" onclick="save()" />
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('/select2/js/select2.min.js') }}"></script>
    <script>
        var oldSelectedIndex = -99;
        var currentStudents = [];
        var selectedPrefix = "";

        $(document).ready(function () {
            $('.class-select-from').select2();
            $('.class-select-to').select2();
            $('.class-select-from').trigger('change');
            $('.class-select-to').trigger('change');
        });

        function loadStudents() {
            selectedPrefix = $('.class-select-to').find(':selected').attr('data-short-form');
            $.ajax({
                type: "GET",
                data: {
                    "class_id": $('.class-select-from').val(),
                },
                url: "getStudentsFromClass",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (result) {
                    console.log(result);
                    currentStudents = result;
                    $('.students-from').empty();
                    result.forEach(function (student) {
                        $('.students-from').append('<tr>'
                            + '<td>' + student.roll_no + '</td>'
                            + '<td>' + student.name + '</td>'
                            + '<td><button class="btn btn-info" data-roll-no="' + student.roll_no + '" data-student-id="' + student.student_id + '" data-student-name="' + student.name + '" data-student-email="' + student.email + '" onclick="migrate(this)"> → </button></td>'
                            + '</tr>');
                    });
                },
                error: function (error) {
                    console.log(error);
                }
            });
        }

        function save() {
            var students = [];
            var student_data = $('.student-data');

            for(var i = 0; i < student_data.length; i++) {
                var student = {};
                student['student_id'] = student_data[i].dataset.studentId;
                student['new_roll_no'] = $('#stu_'+student['student_id']).val();
                students.push(student);
            }

            $.ajax({
                type: "POST",
                data: {
                    "to_class_id": $('.class-select-to').val(),
                    "students": students
                },
                url: "migrateStudents",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (result) {
                    clearList();
                    alert("Migrated successfully!")
                },
                error: function (error) {
                    console.log(error);
                }
            });
            return false;
        }

        function getStudentDataFromButton(button) {
            var student = {};
            var dataset = button.dataset;
            student['roll_no'] = dataset.rollNo;
            student['student_id'] = dataset.studentId;
            student['name'] = dataset.studentName;
            student['email'] = dataset.studentEmail;
            return student;
        }

        function getPositionOfCell(button) {
            var tr = $(button).parent().parent();
            return tr.index();
        }

        function migrate(button) {
            var student = getStudentDataFromButton(button);

            $('.students-to').append('<tr>'
                + '<td><button class="btn btn-info student-data" data-cell-position="'+ getPositionOfCell(button) +'" data-roll-no="' + student.roll_no + '" data-student-id="' + student.student_id + '" data-student-name="' + student.name + '" data-student-email="' + student.email + '" onclick="undoMigrate(this)"> ← </button></td>'
                + '<td><input type="text" class="form-control input-roll-no" id="prefix" name="prefix" disabled="disabled" value="'+ selectedPrefix +'"/> - <input type="text" class="form-control input-roll-no" id="stu_' + student.student_id + '" required/></td>'
                + '<td>' + student.name + '</td>'
                + '</tr>');

            button.parentNode.parentNode.remove();
        }

        function undoMigrate(button) {
            var student = getStudentDataFromButton(button);
            var position = button.dataset.cellPosition;

            var numberOfRows = $('.students-from > tr').length;

            var data = '<tr>'
                + '<td>' + student.roll_no + '</td>'
                + '<td>' + student.name + '</td>'
                + '<td><button class="btn btn-info" data-roll-no="' + student.roll_no + '" data-student-id="' + student.student_id + '" data-student-name="' + student.name + '" data-student-email="' + student.email + '" onclick="migrate(this)"> → </button></td>'
                + '</tr>';

            if(position == numberOfRows) {
                $('.students-from > tr').eq(position - 1).after(data);
            } else {
                $('.students-from > tr').eq(position).before(data);
            }

            button.parentNode.parentNode.remove();
        }

        function clearList() {
            var currentSelectedIndex = $('.class-select-to').val();
            if (oldSelectedIndex != currentSelectedIndex) {
                oldSelectedIndex = currentSelectedIndex;
                $('.students-to').empty();
            }
            loadStudents(); //Reloading
        }
    </script>
@endsection