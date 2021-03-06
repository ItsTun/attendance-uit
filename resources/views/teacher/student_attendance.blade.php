@extends('layouts.teacher_layout')

@section('title')
    Add Attendance
@endsection

@section('styles')
@endsection

@section('content')
    @php $years = []; $yearKlasses = []; $classSubjects = [];@endphp
    @foreach($klasses as $klass)
        @php
            $years[$klass->year_name] = $klass->year_id;

            if(!array_key_exists($klass->year_id, $yearKlasses)) $yearKlasses[$klass->year_id] = [];
            $yearKlasses[$klass->year_id][$klass->class_id] = $klass->name;

            if(!array_key_exists($klass->class_id, $classSubjects)) $classSubjects[$klass->class_id] = [];
            $classSubjects[$klass->class_id][$klass->subject_id] = $klass->subject_name;
        @endphp
    @endforeach
    <div class="container-fluid">
        <form class="form-material" style="padding-top: 15px;">
            <div class="card" style="margin-bottom: 0px;">
                <div class="row" style="margin: 15px;">
                    <div class="col-md-3">
                        <select class="form-control form-control-line" id="years" onChange="onYearChange()">
                            @foreach($years as $year_name => $year_id)
                                <option value="{{ $year_id }}">{{ $year_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <select class="form-control form-control-line" id="klasses" onChange="onClassChange()">
                        </select>
                    </div>
                    <div class="col-md-4">
                        <select class="form-control form-control-line" id="subjects" onChange="onSubjectChange()">
                        </select>
                    </div>
                    <div class="col-md-1">
                        <a href="#" class="btn btn-info" id="btn_get">Get</a>
                    </div>
                </div>
            </div>
            <div class="row" style="margin-left: 1px;">
                @if(!is_null($attendances) && count($attendances) > 0)
                    <div class="card col-md-12" style="margin-top: 15px;">
                        <table class="table">
                            <tr>
                                <th>Name</th>
                                <th>Percent</th>
                            </tr>
                            @foreach($attendances as $key=>$value)
                                <tr>
                                    <td>{{ $key }}</td>
                                    <td>{{ $value['name'] }}</td>
                                    <td>{{ $value['percent'] }}</td>
                                </tr>
                            @endforeach
                        </table>
                    </div>
                @endif
            </div>
        </form>
    </div>
    <script>
        years = @php echo json_encode($years); @endphp;
        yearKlasses = @php echo json_encode($yearKlasses); @endphp;
        classSubjects = @php echo json_encode($classSubjects); @endphp;
        class_id = @php echo $class_id; @endphp;
        subject_id = @php echo $subject_id; @endphp;

        var btn_get, yearSelect, classSelect, subjectSelect;

        document.addEventListener("DOMContentLoaded", function () {
            btn_get = document.getElementById('btn_get');
            yearSelect = document.getElementById('years');
            classSelect = document.getElementById('klasses');
            subjectSelect = document.getElementById('subjects');
            onYearChange();

            year_id = -1;

            for (var key in yearKlasses) {
                for (var c_id in yearKlasses[key]) {
                    if (c_id == class_id) {
                        year_id = key;
                        break;
                    }
                }
                if (year_id != -1) break;
            }

            if (class_id != '0') {
                chooseSelected(year_id, class_id, subject_id);
            }
        });


        function chooseSelected(year_id, class_id, subject_id) {
            yearSelect.selectedIndex = yearSelect.querySelector("option[value='" + year_id + "']").index;
            classSelect.selectedIndex = classSelect.querySelector("option[value='" + class_id + "']").index;
            subjectSelect.selectedIndex = subjectSelect.querySelector("option[value='" + subject_id + "']").index;
        }

        function onYearChange() {
            var yearValue = yearSelect.options[yearSelect.selectedIndex].value;
            var klassesArr = yearKlasses[yearValue];

            while (classSelect.options.length > 0) {
                classSelect.remove(0);
            }

            for (var key in klassesArr) {
                var opt = document.createElement('option');
                opt.value = key;
                opt.innerHTML = klassesArr[key];
                classSelect.appendChild(opt);
            }

            onClassChange();
        }

        function onClassChange() {
            var classValue = classSelect.options[classSelect.selectedIndex].value;
            var subjectArr = classSubjects[classValue];

            while (subjectSelect.options.length > 0) {
                subjectSelect.remove(0);
            }

            for (var key in subjectArr) {
                var opt = document.createElement('option');
                opt.value = key;
                opt.innerHTML = subjectArr[key];
                subjectSelect.appendChild(opt);
            }

            onSubjectChange();
        }

        function onSubjectChange() {
            var classValue = classSelect.options[classSelect.selectedIndex].value;
            var subjectValue = subjectSelect.options[subjectSelect.selectedIndex].value;

            btn_get.href = 'students?class=' + classValue + '&subject=' + subjectValue;
        }
    </script>
@endsection