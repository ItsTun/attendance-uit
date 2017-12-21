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
		<div class="col-3">
			<form class="form-material">
				<select class="form-control form-control-line" id="date" onChange="dateChange(this.selectedIndex)">
					@foreach ($dates as $date)
			            <option @if($selectedDate == $date) {{ "selected" }} @endif>{{ $date }}</option>
					@endforeach
				</select>
			</form>
		</div>
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
				<div class="col-12" style="margin-top: 30px;">
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
						</div>
					</a>
				</div>
			@endforeach
		@endif
		</div>
	</div>

	<script>
		function dateChange(selectedIndex) {
			var dateSelectBox = document.getElementById('date');
			window.location = "timetable?date="+dateSelectBox.options[selectedIndex].value;
		}
		(function() {
			setTimeout(function () {
		        document.getElementById('message').style.display='none';
		    }, 3000);
		})();
	</script>
@endsection