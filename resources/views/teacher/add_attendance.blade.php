@extends('layouts.teacher_layout')

@section('title')
Add Attendance
@endsection

@section('styles')
	<link href="{{ asset('css/add_attendance.css') }}" rel="stylesheet">
@endsection

@section('content')

<div class="container-fluid">
	<div class="row" style="padding-top: 30px;">
		@foreach($students as $student)
			<div class="col-lg-4 col-md-6 col-sm-12 col-xs-12">
			<div class="card">
				<div class="card-block">
					<div class="custom-checkbox">
						<input type="checkbox" id="{{ $student->roll_no }}"/>
						<label for="{{ $student->roll_no }}">{{ $student->name }}</label>
					</div>
				</div>
			</div>
		</div>
		@endforeach
	</div>
</div>


@endsection