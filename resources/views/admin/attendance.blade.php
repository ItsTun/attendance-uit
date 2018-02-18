@extends('layouts.admin_layout')

@section('title')
    Attendance
@endsection

@section('styles')
    <link href="{{ asset('/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css') }}" rel="stylesheet"/>
    <link href="{{ asset('/css/font-awesome.min.css') }}" rel="stylesheet">
@endsection

@section('content')

    <div class="container">
        <div class="row" style="padding-top: 30px;">
            <div class="col-12" id="message" style="display:@if($msgCode!=0){{'block'}}@else{{'none'}}@endif">
                <div class="card" style="background-color:#81C784;">
                    <div class="card-block">
                        {{ \App\MessageUtils::getMessageFromCode($msgCode) }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12" style="margin-bottom: 10px; text-align: right;">
            <a href="{{ route('admin.student.percentage') }}">
                <button class="btn btn-info">View Percentage</button>
            </a>
        </div>
        <div class="card">
            <div class="card-block">
                <div class="row">
                    <div class="col-md-4 col-lg-4 col-sm-12 col-xs-12">
                        <label for="datepicker">Date</label>
                        <input type="text" class="form-control" value="{{ $selectedDate }}" id="datepicker"/>
                    </div>
                    <div class="col-md-4 col-lg-4 col-sm-12 col-xs-12">
                        <label for="class-select">Class</label>
                        <select class="form-control class-select" id="class-select" name="class_id">
                            <option disabled selected value="-1">Select a class</option>
                            @foreach($years as $year)
                                <optgroup label="{{ $year->name }}">
                                    @foreach($year->klasses as $klass)
                                        <option value="{{ $klass->class_id }}"
                                                @if($klass->class_id==$class_id) selected="selected" @endif>{{ $klass->name }}</option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-1 col-lg-1 col-sm-12 col-xs-12">
                        <label for="go-btn" style="color:white;">-</label>
                        <input type="button" href="#" class="btn btn-info form-control" onclick="dateChange()"
                               style="background-color:#1e88e5 !important" id="go-btn" value="Go"/>
                    </div>
                </div>
            </div>
        </div>
        @if (count($timetables) === 0)
            <div class="col-12">
                <div class="card" style="margin-bottom: 10px !important;">
                    <div class="card-block">
                        Please select class and press "Go".
                    </div>
                </div>
            </div>
        @else
            @php
                $numberOfPeriods = 1; $start_time = 0; $periods = "";
            @endphp
            @for ($i = 0; $i < count($timetables); $i++)
                @php
                    if($numberOfPeriods == 1) {
                        $start_time = $timetables[$i]->start_time;
                    }
                @endphp
                @if($i < count($timetables)-1 && $timetables[$i]->subject_code == $timetables[$i+1]->subject_code && $timetables[$i]->room == $timetables[$i+1]->room)
                    @php
                        if($timetables[$i]->subject_code != '000') {
                            $periods .= $timetables[$i]->period_id. ",";
                            $numberOfPeriods+=1;
                        }
                    @endphp
                @else
                    @php
                        if($timetables[$i]->subject_code != '000') {
                            $periods .= $timetables[$i]->period_id;
                        }
                    @endphp
                    @if($timetables[$i]->subject_code != '000')
                        <div class="col-12">
                            <a href="add/{{ $periods }}?date={{ $selectedDate }}&class_id={{ $class_id }}" style="color:#67757c;">
                                <div class="card" style="cursor: pointer; margin-bottom: 10px !important;">
                                    <div class="card-block">
                                        <div class="col-md-3 col-sm-12" style="float: left;">
                                            {{ date("g:i a", strtotime($start_time)) }}
                                            -
                                            {{ date("g:i a", strtotime($timetables[$i]->end_time)) }}
                                        </div>
                                        <div class="col-md-6" style="float: left;">
                                            <b>{{ $timetables[$i]->subject_code }} - {{ $timetables[$i]->subject_name }}</b>
                                            <br/>
                                            Room - {{ $timetables[$i]->room }}
                                        </div>
                                        <div class="col-md-2" style="float: left;">
                                            @if($numberOfPeriods > 1)<span
                                                    class="label label-info">{{ $numberOfPeriods." Periods" }}</span>@endif
                                        </div>
                                    </div>
                                </div>
                            </a>
                            @php
                                $numberOfPeriods = 1; $start_time = 0; $periods = "";
                            @endphp
                        </div>
                    @endif
                @endif
            @endfor
        @endif
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('/moment/js/moment.min.js') }}"></script>
    <script src="{{ asset('/bootstrap-datetimepicker/js/bootstrap-datetimepicker.js') }}"></script>
    <script>
        function dateChange() {
            var dateinput = document.getElementById('datepicker').value;
            console.log(dateinput);
            var date = moment(dateinput, 'YYYY-MM-DD h:m');
            if (date.isValid()) {
                classChange("attendance?date=" + date.format('YYYY-MM-DD'));
            }
        }

        function classChange(url) {
            var classSelect = $('.class-select');
            var classId = classSelect.val();
            console.log(classId);
            if (classId != undefined) {
                url += '&class_id=' + classId;
                window.location = url;
            }
        }

        (function () {
            $('#datepicker').datetimepicker({
                format: 'YYYY-MM-DD'
            });
            setTimeout(function () {
                document.getElementById('message').style.display = 'none';
            }, 3000);
        })();
    </script>
@endsection