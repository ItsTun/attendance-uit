@extends ('layouts.admin_layout')

@section ('title')
	Student Attendance Details
@endsection

@section ('styles')
	<link rel="stylesheet" href="{{ asset('/jquery-ui/jquery.ui.css') }}"> 
@endsection

@section ('content')
	<div class="card" style="margin: 7px;">
		<div class="card-block">
			<form>
				<div class="container">
					<div class="row">
						<div class="col-md-2">
							<div class="form-group form-material">
								<label>Class</label> 
								<br>
								<select id="classesSelect" class="form-control">
									@foreach ($years as $year) 
										<optgroup label="{{ $year->name }}">
											@foreach ($year->klasses as $v)
												<option id="{{ $v->class_id }}" 
														value="{{ $v->short_form }}" 
														name="{{ $v->name }}">
													{{ $v->name }}
												</option>
											@endforeach
										</optgroup>
									@endforeach
								</select>
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group form-material">
								<label>Roll No</label>
								<div>
									<span id="classValue"></span>
									<input class="form-control form-control-line" type="text" id="rollNo" style="width: 80%;">
								</div>
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group">
								<label>From</label>
								<br>
								<input class="form-control form-control-line" type="text" id="from_datepicker" readonly="readonly" style="background:white; color: #000;">
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group">
								<label>To</label>
								<br>
								<input class="form-control form-control-line" type="text" id="to_datepicker" readonly="readonly" style="background:white; color: #000;">
							</div>
						</div>
						<div class="col-md-1">
							<div class="form-group form-material">
								<button id="btnGet" type="button" class="btn btn-primary" style="margin-top: 35px;">Get</button>
							</div>
						</div>
					</div>
				</div>
			</form>
		</div>
		<div id="results" class="card" style="margin: 7px;">
			<div class="card-block" style="padding: 30px;">
				<div class="row">
					<div class="col-md-12">
						<table class="table table-bordered"></table>
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection

@section ('scripts')
	<script src="{{ asset('/js/utils.js') }}"></script>
	<script src="{{ asset('/jquery-ui/jquery.ui.js') }}"></script>

	<script>
		$(function() {
			$.datepicker.setDefaults({
				dateFormat: 'yy-mm-dd',
				constraintInput: false,
		    	maxDate: new Date()
			});

		    $("#from_datepicker").datepicker({
				onSelect: function(selectedDate) {
				    $('#to_datepicker').datepicker('option', 'minDate', selectedDate);
				}
		    });

		    $("#to_datepicker").datepicker({
		    	onSelect: function(selectedDate) {
			        $('#from_datepicker').datepicker('option', 'maxDate', selectedDate);
			    }
		    });

		    $('#classValue').html($('#classesSelect').val() + '-');

		    $('#classesSelect').change(function() {
		    	$('#classValue').html($('#classesSelect').val() + '-');
		    });

		    $('#results').hide();

		    $('#btnGet').click(function() {
		    	$('#results').hide();
		    	$('table tr').remove();

		    	var cl = $('#classesSelect').find(':selected').val();
		    	var rollNo = $('#rollNo').val();
		    	var fromDate = $('#from_datepicker').val();
		    	var toDate = $('#to_datepicker').val();

				var isRollNoCorrect = validateRollNo(rollNo);
		    	var isFromDateCorrect = validateDate(fromDate);
		    	var isToDateCorrect = validateDate(toDate);

		    	if (!isFromDateCorrect || !isToDateCorrect) {
		    		alert("Please enter date(s)!");
		    		return;
		    	}

		    	if (!isRollNoCorrect) {
		    		alert("Please enter valid roll no!");
		    		return;
		    	}

		    	getData(cl, rollNo, fromDate, toDate);
		    });
		});

		function getData(cl, roll_no, from_date, to_date) {
			$.get('{{ route('admin.getStudentAttendanceDetails') }}', {
				roll_no: cl + '-' + roll_no,
				from: from_date,
				to: to_date
			}, function(data, status) {
				if (status === 'nocontent') {
					alert('No data!');
					return;
				}
				my_json = JSON.parse(data);
				showTable(my_json);
			});
		}

		function showTable(data) {
			data.forEach(function(element) {
				var date = element['date'];
				var attendances = element['attendances'];

				var row = $('<tr></tr>').appendTo($('table'));
				$('<td></td>').text(date).appendTo(row);
				for (var key in attendances) {
					if (attendances.hasOwnProperty(key)) {
						if (attendances[key]['present'] == 0) {
							$('<td></td>').attr({ class: 'table-danger fixed-cell' }).text(attendances[key]['subject_code']).appendTo(row);
						} else if (attendances[key]['present'] == 1) {
							$('<td></td>').attr({ class: 'table-success fixed-cell' }).text(attendances[key]['subject_code']).appendTo(row);
						} else {
							$('<td></td>').attr({ class: 'fixed-cell' }).text(attendances[key]['subject_code']).appendTo(row);
						}
					}
				}
			});

			$('#results').show();
		}
	</script>
@endsection