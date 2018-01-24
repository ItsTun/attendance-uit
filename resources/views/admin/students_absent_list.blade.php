@extends ('layouts.admin_layout')

@section ('title')
	Students Absent List
@endsection

@section ('styles')
	
@endsection

@section ('content')
	<div class="card" style="margin: 7px;">
		<div class="card-block">
			<form>
				<div class="container">
					<div class="row">
						<div class="col-md-3">
							<div class="form-group form-material">
								<label>Class</label> 
								<br>
								<select id="classesSelect" class="form-control">
									@foreach ($classes_ary as $key => $value) 
										<optgroup label="{{ $key }}">
											@foreach ($value as $v)
												<option id="{{ $v['class_id'] }}" 
														value="{{ $v['short_form'] }}" 
														name="{{ $v['name'] }}">
													{{ $v['name'] }}
												</option>
											@endforeach
										</optgroup>
									@endforeach
								</select>
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
							<button id="btnGet" class="btn btn-primary" type="button" style="margin-top: 35px;">Get</button>
						</div>
					</div>
				</div>
			</form>
		</div>
		<div id="results" class="card" style="margin: 7px;">
			<div class="card-block" style="padding: 30px;">
				<table class="table"></table>
			</div>
		</div>
	</div>
@endsection

@section ('scripts')
	<script type="text/javascript" src="/js/script.js"></script>
	<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
 	<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">  
	<script type="text/javascript">
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

		    $('#results').hide();

		    $('#btnGet').click(function() {
		    	$('#results').hide();
		    	$('table tr').remove();

		    	var class_id = $('#classesSelect').find(':selected').attr('id');
		    	var fromDate = $('#from_datepicker').val();
		    	var toDate = $('#to_datepicker').val();

		    	var isFromDateCorrect = validateDate(fromDate);
		    	var isToDateCorrect = validateDate(toDate);

		    	if (!isFromDateCorrect || !isToDateCorrect) {
		    		alert("Please enter date(s)!");
		    		return;
		    	}

		    	getData(class_id, fromDate, toDate);
		    });
		});

		function getData(class_id, from, to) {
			$.get('/admin/getStudentsAbsentList', {
				class_id: class_id,
				from: from,
				to: to
			}, function(data, status) {
				my_json = JSON.parse(data);
				showTable(my_json);
			});
		}

		function showTable(data) {
			showHeader();
			data.forEach(function(element) {
				var date = element['date'];
				var absent_students = element['absent_students'];

				var row = $('<tr></tr>').appendTo($('table'));
				$('<td></td>').text(date).appendTo(row);
				if (absent_students == null) {
					$('<td></td>').text('No absent students').appendTo(row);
					return;
				}
				var students = absent_students.split(',');
				var col = $('<td></td>').appendTo(row);
				students.forEach(function(student) {
					$('<span></span>').text(student)
									.addClass('label label-danger')
									.css('margin-right', '10px')
									.appendTo(col);
				});
			});
			$('#results').show();
		}

		function showHeader() {
			var row = $('<tr></tr>').appendTo($('table'));
			$('<th></th>').text('Date').appendTo(row);
			$('<th></th>').text('Absent Students').appendTo(row);
		}
	</script>
@endsection