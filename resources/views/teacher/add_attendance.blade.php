@extends('layouts.teacher_layout')

@section('title')
Add Attendance
@endsection

@section('styles')
	<link href="{{ asset('css/add_attendance.css') }}" rel="stylesheet">
@endsection

@section('content')
<form action="#" method="post">
	{{ csrf_field() }}
	<input type="hidden" name="date" value="{{ $date }}" />
	<input type="hidden" name="period" value="{{ $period }}" />
	<div class="container-fluid">
		<div class="row" style="padding-top: 30px;">
			@foreach($students as $student)
				<div class="col-lg-4 col-md-6 col-sm-12 col-xs-12">
				<div class="card">
					<div class="card-block">
						<div class="custom-checkbox">
							<input type="checkbox" name="student" id="{{ $student->roll_no }}" value="{{ $student->roll_no }}"/>
							<label for="{{ $student->roll_no }}">{{ $student->name }}</label>
						</div>
					</div>
				</div>
			</div>
			@endforeach
		</div>
		<input type="submit" class="btn waves-effect waves-light btn-success hidden-md-down" value="Save" />
	</div>
</form>

@endsection