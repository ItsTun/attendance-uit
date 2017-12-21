@extends('layouts.teacher_layout')

@section('title')
Timetable
@endsection

@section('content')

<div class="container-fluid">
	<div class="row" style="padding-top: 30px;">
		@if (count($timetables) === 0)
		<div class="col-12" style="margin-top: 30px;">
			<div class="card">
				<div class="card-block">
					No timetable for today.
				</div>
			</div>
		</div>
		@else
		@foreach ($timetables as $timetable)
		<div class="col-12">
			<a href="add/{{ $timetable->period_id }}" style="color:#67757c;">
				<div class="card" style="cursor: pointer;">
					<div class="card-block">
						<div class="col-md-3 col-sm-12" style="float: left;">
							{{ $timetable->duration }}
						</div>
						<div class="col-md-8" style="float: left;">
							<b>{{ $timetable->name }}</b>
							<br/>
							Room - {{ $timetable->room }}
						</div>
					</div>
				</div></a>
			</div>
			@endforeach
			@endif
		</div>
	</div>


@endsection