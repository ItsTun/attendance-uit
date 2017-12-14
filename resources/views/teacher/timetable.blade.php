@extends('layouts.teacher_layout')

@section('title')
	Timetable
@endsection

@section('content')

<div class="container-fluid">
	<div class="row">
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
				<div class="card">
		        	<div class="card-block">
				<div class="col-2.5" style="float: left;">
					{{ $timetable->duration }}
				</div>
				<div class="col-8" style="float: left;">
					<b>{{ $timetable->name }}</b>
					<br/>
					Room - {{ $timetable->room }}
				</div>
			</div></div>
			</div>
			@endforeach
		@endif
    </div>
</div>

			
@endsection