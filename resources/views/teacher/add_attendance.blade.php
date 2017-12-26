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
	<input type="hidden" name="period" value="@php echo implode(",",$periods); @endphp" />
	<div class="container-fluid">
		<div class="row" style="padding-top: 30px;">
			<div class="col-lg-4 col-md-6 col-sm-12 col-xs-12" style="background-color: white; padding: 0px !important; margin-left: 15px; margin-bottom: 15px;">
				<table class="table">
					<tr>
						<th>Student</th>
						@if( $numberOfPeriods > 1)
							@for ($i = 1; $i <=$numberOfPeriods; $i++)
								<th style="text-align: center">{{ $i }}</th>
							@endfor
						@endif
					</tr>
					@foreach($students as $student)

					<tr>
						<td>
							<b>{{ $student->roll_no }}</b><br />
							{{ $student->name }}
						</td>
							@for ($i = 0; $i <count($periods); $i++)
								@if($periodObjects[$i]->subject_class->class_id == $student->class_id)
									<td align="center">
										<div class="custom-checkbox">
											<input type="checkbox" name="{{ $periodObjects[$i]->period_id }}_student[]" id="{{ $periodObjects[$i]->period_id }}_{{ $student->roll_no }}" value="{{ $student->roll_no }}"/>
											<label for="{{ $periodObjects[$i]->period_id }}_{{ $student->roll_no }}"></label>
										</div>
									</td>
								@endif
							@endfor
					</tr>
					@endforeach
				</table>
			</div>
			<div class="col-md-12" style="padding-left: 0px !important">
				<div class="col-md-1">
					<input type="submit" class="btn waves-effect waves-light btn-success" value="Save" />
				</div>
			</div>
		</div>
	</div>
</form>

@endsection

{{-- <div class="col-lg-4 col-md-6 col-sm-12 col-xs-12">
					<div class="card">
						<div class="card-block">
							<div class="custom-checkbox">
								<input type="checkbox" name="student" id="{{ $student->roll_no }}" value="{{ $student->roll_no }}"/>
								<label for="{{ $student->roll_no }}">{{ $student->name }}</label>
							</div>
						</div>
					</div>
				</div> --}}