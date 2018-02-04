@extends('layouts.teacher_layout')

@section('title')
    Timetable
@endsection

@section('content')

    <div class="container-fluid">
        <div class="row" style="padding-top: 30px;">
            <div class="col-12" id="message" style="display:@if($msgCode!=0){{'block'}}@else{{'none'}}@endif">
                <div class="card" style="background-color:#81C784;">
                    <div class="card-block">
                        {{ \App\MessageUtils::getMessageFromCode($msgCode) }}
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-12 col-xs-12" style="margin-bottom: 30px;">
                <form class="form-material">
                    <select class="form-control form-control-line" id="date" onChange="dateChange(this.selectedIndex)">
                        @foreach ($dates as $date)
                            <option @if($selectedDate == $date) {{ "selected" }} @endif>{{ $date }}</option>
                        @endforeach
                    </select>
                </form>
            </div>
            @if (count($timetables) === 0)
                <div class="col-12">
                    <div class="card">
                        <div class="card-block">
                            No timetable for today.
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
                                <a href="add/{{ $periods }}?date={{ $selectedDate }}" style="color:#67757c;">
                                    <div class="card" style="cursor: pointer; margin-bottom: 10px !important;">
                                        <div class="card-block">
                                            <div class="col-md-3 col-sm-12" style="float: left;">
                                                {{ date("g:i a", strtotime($start_time)) }}
                                                -
                                                {{ date("g:i a", strtotime($timetables[$i]->end_time)) }}
                                            </div>
                                            <div class="col-md-6" style="float: left;">
                                                <b>{{ $timetables[$i]->subject_name }}</b>
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
    </div>

    <script>
        function dateChange(selectedIndex) {
            var dateSelectBox = document.getElementById('date');
            window.location = "timetable?date=" + dateSelectBox.options[selectedIndex].value;
        }

        (function () {
            setTimeout(function () {
                document.getElementById('message').style.display = 'none';
            }, 3000);
        })();
    </script>
@endsection