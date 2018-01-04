@extends('layouts.admin_layout')

@section('title')
	Timetables
@endsection

@section('styles')
<link href="{{ asset('/css/admin-timetables.css') }}" rel="stylesheet" />
<link href="{{ asset('/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css') }}" rel="stylesheet" />
<link href="{{ asset('/css/font-awesome.min.css') }}" rel="stylesheet">

<meta name="csrf-token" content="{{ csrf_token() }}">

@if(!is_null($class_id) && !is_null($year_id))
	<script src="{{ asset('/redips/js/redips-drag-min.js') }}"></script>
	<script src="{{ asset('/js/admin-timetables.js') }}"></script>
@endif

@endsection

@php

	$classes;

@endphp

@section('content')
	<div class="container-fluid" style="padding-top: 16px;padding-bottom: 0px;">
		<div class="card" style="padding: 8px; margin-bottom: 0px;">
			<div class="row">
				<div class="col-md-4">
					<select class="form-control" id="year-select" onchange="onYearChange()">
						@foreach($years as $year)
							<option value="{{ $year['year_id'] }}" 
							@if($year_id==$year['year_id'] || (is_null($year_id) && $year['year_id']==$years[0]['year_id'])) 
								{{ "selected" }} 
								@php $classes = $year->klasses; @endphp 
							@endif>{{ $year['name'] }}</option>
						@endforeach
					</select>
				</div>
				<div class="col-md-4">
					<select class="form-control" id="class-select" onchange="onClassChange()">
						@foreach($classes as $klass)
							<option value="{{ $klass['class_id'] }}" @if($class_id && $class_id==$klass['class_id']) {{ "selected" }} @endif>{{ $klass['name'] }}</option>
						@endforeach
					</select>
				</div>
				<a href="#" id="change-class" class="btn btn-info">
					Choose class
				</a>
			</div>
		</div>
	</div>
	@if(!is_null($class_id) && !is_null($year_id))
	<div id="redips-drag">
				<!-- left container (table with subjects) -->
				<center><div id="left" style="padding-top:15px; margin-bottom: 15px;">
					<table id="table1" style="background: transparent;">
						<tbody>
							<tr>
								@foreach($subject_classes as $key => $subject_class)
									@if(!is_null($subject_class->subject_id))
										<td class="dark" align="center"><div data-subject-class-id="{{ $subject_class->subject_class_id }}" class="redips-drag redips-mark redips-clone ar">{{ $subject_class->subject['subject_code'] }}<br><input type="text" placeholder="Room" class="room" required /></div></td>
										@if($key != 0 && $key % 4 == 0)
											</tr><tr>
										@endif
									@endif
								@endforeach
								<td class="redips-trash" title="Trash" align="center">Trash</td>
							</tr>
						</tbody>
					</table>
				</div><!-- left container --></center>
				
				<!-- right container -->
				<center>
					<form action="#" onsubmit="return save()">
					{{ csrf_field() }}
					<div id="right" style="overflow:auto; position: relative">
					<table id="table2">
						<tbody>
							<tr class="lunch-break-row">
								<td class="redips-mark small-cell lunch-time dark">Lunch Time</td>
								<td class="redips-mark small-cell lunch-time"><input type="radio" name="is_lunch_time" id="lt1" value="1" onclick="onRadioSelect(this)" /><label for="lt1"></label></td>
								<td class="redips-mark small-cell lunch-time"><input type="radio" name="is_lunch_time" id="lt2" value="2" onclick="onRadioSelect(this)"/><label for="lt2"></label></td>
								<td class="redips-mark small-cell lunch-time"><input type="radio" name="is_lunch_time" id="lt3" value="3" onclick="onRadioSelect(this)"/><label for="lt3"></label></td>
								<td class="redips-mark small-cell lunch-time"><input type="radio" checked="checked" name="is_lunch_time" id="lt4" value="4" onclick="onRadioSelect(this)"/><label for="lt4"></label></td>
								<td class="redips-mark small-cell lunch-time"><input type="radio" name="is_lunch_time" id="lt5" value="5" onclick="onRadioSelect(this)"/><label for="lt5"></label></td>
								<td class="redips-mark small-cell lunch-time"><input type="radio" name="is_lunch_time" id="lt6" value="6" onclick="onRadioSelect(this)"/><label for="lt6"></label></td>
								<td class="redips-mark small-cell lunch-time"><input type="radio" name="is_lunch_time" id="lt7" value="7" onclick="onRadioSelect(this)"/><label for="lt7"></label></td>
								<td class="redips-mark">
									<div class="btn btn-warning" onclick="removeOneColumn()">Remove Period</div><br>
								</td>
							</tr>
							<tr class="time-chooser-row">
								<!-- if checkbox is checked, clone school subjects to the whole table row  -->
								<td class="medium-cell redips-mark blank">
									
								</td>
								<td class="medium-cell tc1 redips-mark dark">
									<input type="text" class="time-chooser t1" onchange="ontimechange(this)" required/> <br>:<br> <input type="text" class="time-chooser t2" onchange="ontimechange(this)" required/>
								</td>
								<td class="medium-cell tc2 redips-mark dark">
									<input type="text" class="time-chooser t3" onchange="ontimechange(this)" required/> <br>:<br> <input type="text" class="time-chooser t4" onchange="ontimechange(this)" required/>
								</td>
								<td class="medium-cell tc3 redips-mark dark">
									<input type="text" class="time-chooser t5" onchange="ontimechange(this)" required/> <br>:<br> <input type="text" class="time-chooser t6" onchange="ontimechange(this)" required/>
								</td>
								<td class="medium-cell tc4 redips-mark dark">
									<input type="text" class="time-chooser t7" onchange="ontimechange(this)" required/> <br>:<br> <input type="text" class="time-chooser t8" onchange="ontimechange(this)" required/>
								</td>
								<td class="medium-cell tc5 redips-mark dark">
									<input type="text" class="time-chooser t9" onchange="ontimechange(this)" required/> <br>:<br> <input type="text" class="time-chooser t10" onchange="ontimechange(this)" required/>
								</td>
								<td class="medium-cell tc6 redips-mark dark">
									<input type="text" class="time-chooser t11" onchange="ontimechange(this)" required/> <br>:<br> <input type="text" class="time-chooser t12" onchange="ontimechange(this)" required/>
								</td>
								<td class="medium-cell tc7 redips-mark dark">
									<input type="text" class="time-chooser t13" onchange="ontimechange(this)" required/> <br>:<br> <input type="text" class="time-chooser t14" onchange="ontimechange(this)" required/>
								</td>
								<td class="redips-mark">
									<div class="btn btn-info" onclick="addOneColumn()" style="margin-top: 5px;">Add Period</div><br>
									<input type="submit" class="btn btn-success" style="margin-top: 5px;background: #26c6da !important" value="Save"/>
								</td>
							</tr>
							<tr class="row1">
								<td class="slot redips-mark dark day">Monday</td>
								<td class="slot r1 c1"></td>
								<td class="slot r1 c2"></td>
								<td class="slot r1 c3"></td>
								<td class="slot r1 c4"></td>
								<td class="slot r1 c5"></td>
								<td class="slot r1 c6"></td>
								<td class="slot r1 c7"></td>
							</tr>
							<tr class="row2">
								<td class="slot redips-mark dark day">Tuesday</td>
								<td class="slot r2 c1"></td>
								<td class="slot r2 c2"></td>
								<td class="slot r2 c3"></td>
								<td class="slot r2 c4"></td>
								<td class="slot r2 c5"></td>
								<td class="slot r2 c6"></td>
								<td class="slot r2 c7"></td>
							</tr>
							<tr class="row3">
								<td class="slot redips-mark dark day">Wednesday</td>
								<td class="slot r3 c1"></td>
								<td class="slot r3 c2"></td>
								<td class="slot r3 c3"></td>
								<td class="slot r3 c4"></td>
								<td class="slot r3 c5"></td>
								<td class="slot r3 c6"></td>
								<td class="slot r3 c7"></td>
							</tr>
							<tr class="row4">
								<td class="slot redips-mark dark day">Thursday</td>
								<td class="slot r4 c1"></td>
								<td class="slot r4 c2"></td>
								<td class="slot r4 c3"></td>
								<td class="slot r4 c4"></td>
								<td class="slot r4 c5"></td>
								<td class="slot r4 c6"></td>
								<td class="slot r4 c7"></td>
							</tr>
							<tr class="row5">
								<td class="slot redips-mark dark day">Friday</td>
								<td class="slot r5 c1"></td>
								<td class="slot r5 c2"></td>
								<td class="slot r5 c3"></td>
								<td class="slot r5 c4"></td>
								<td class="slot r5 c5"></td>
								<td class="slot r5 c6"></td>
								<td class="slot r5 c7"></td>
							</tr>
						</tbody>
					</table>
				</div><!-- right container -->
			</form>
			</center>
			</div><!-- drag container -->
			@endif
@endsection

@section('scripts')
	<script>
		var classes = [];
		var yearSelect;
		var classSelect;
		var changeClassBtn;
		var periods;
		var lunchBreak;
		var subject_classes = @php echo $subject_classes; @endphp

		@if(!is_null($periods) && sizeof($periods) > 0)
			periods = @php echo $periods; @endphp;
			lunchBreak = @php echo (!is_null($lunch_break_subject_class_id))?$lunch_break_subject_class_id:-99; @endphp;
		@endif

		@foreach($years as $year)
			classes[{{ $year['year_id'] }}] = @php echo $year->klasses; @endphp;
		@endforeach
	</script>

	<script src="{{ asset('/moment/js/moment.min.js') }}"></script>
	<script src="{{ asset('/bootstrap-datetimepicker/js/bootstrap-datetimepicker.js') }}"></script>
	<script>
			var currentValue = 4; 
			var times = [];
			
			document.addEventListener("DOMContentLoaded", function() {

				yearSelect = document.getElementById('year-select');
				classSelect = document.getElementById('class-select');
				changeClassBtn = document.getElementById('change-class');

				if(periods != undefined && periods.length > 0)	{
					addOrRemoveColumnsIfNeeded();
					placePeriods();
				} else {
					changeColumnsColor(currentValue, '#FFD54F', '#FFD54F', true);
				}

				@if(!is_null($class_id) && !is_null($year_id))
					initializeTimeChooser();
				@endif
			});

			function initializeTimeChooser() {
				$('.time-chooser').datetimepicker({
						useCurrent: false,
						widgetPositioning : { vertical: 'bottom' },
	                    format: 'LT'
	                }).on('dp.change',function(e){
	                	console.log(document.activeElement.className);
	                	if (document.activeElement.className == e.target.className) {
		                	var patt = new RegExp("\\d{1,2}");
		                	var classNames = e.currentTarget.className;
		                	var res = patt.exec(classNames);
						    validateTimeOfPeriod(res[0], e);
						}
					});
			}

			function placePeriods() {
				for(var i = 0; i < periods.length; i++) {
					var period = periods[i];

					if(period.subject_class_id != lunchBreak.subject_class_id) {
						console.log('> ' + period.subject_class_id);
						$('.r'+period.day+'.c'+period.period_num).html('<div data-subject-class-id="'+ period.subject_class_id +'" class="redips-drag ar" style="border-style: solid; cursor: move;" id="c'+ i +'">'+ getSubjectCode(period.subject_class_id) +'<br><input type="text" placeholder="Room" class="room" value="'+ period.room +'"required></div>');
					} else {
						currentValue = period.period_num;
						$('#lt'+currentValue).prop('checked', true);
						changeColumnsColor(currentValue, '#FFD54F', '#FFD54F', true);
					}
					var s = ((period.period_num * 2) - 1);
					var e = period.period_num * 2;
					times['t' + s] = moment(period.start_time, 'h:mm A');
					times['t' + e] = moment(period.end_time, 'h:mm A');
					$('.time-chooser.t' + s).val(period.start_time);
					$('.time-chooser.t' + e).val(period.end_time);
				}
			}

			function getSubjectCode(subject_class_id) {
				for(var i = 0; i < subject_classes.length; i++) {
					var subject_class = subject_classes[i];
					if(subject_class.subject_class_id == subject_class_id) {
						return subject_class.subject.subject_code;
					}
				}
			}

			function addOrRemoveColumnsIfNeeded() {
				var numberOfPeriods = getNumberOfPeriods();
				console.log(numberOfPeriods);
				if(numberOfPeriods > 7) {
					var columnsToBeAdded = numberOfPeriods - 7;
					for(var i = 0; i < columnsToBeAdded; i++) {
						addOneColumn();
					}
				} else {
					var columnsToBeRemoved = 7 - numberOfPeriods;
					for(var i = 0; i < columnsToBeRemoved; i++) {
						removeOneColumn();
					}
				}
			}

			function getNumberOfPeriods() {
				var maxPeriodNum = 0;

				for(var i = 0; i < periods.length; i++) {
					var periodNum = periods[i].period_num;
					if(periodNum > maxPeriodNum) maxPeriodNum =  periodNum;
				}

				return maxPeriodNum;
			}

			function onYearChange() {
				var yearId = yearSelect.options[yearSelect.selectedIndex].value;

				var classesWithinYear = classes[yearId];

				while (classSelect.options.length > 0) {                
		        	classSelect.remove(0);
			    }

				for(var i = 0; i < classesWithinYear.length; i++) {
					var opt = document.createElement('option');
			    	opt.value = classesWithinYear[i]['class_id'];
			    	opt.innerHTML = classesWithinYear[i]['name'];
				    classSelect.appendChild(opt);  
				}

				onClassChange();
			}

			function onClassChange() {
				var yearId = yearSelect.options[yearSelect.selectedIndex].value;
				var classId = classSelect.options[classSelect.selectedIndex].value;
				changeClassBtn.href = "timetables?year_id="+ yearId +"&class_id=" + classId;
			}

			function getTimeObj(timeStr) {
				return moment(timeStr, 'h:mm A');
			}

			function get24HourStr(timeStr) {
				return moment(timeStr, 'h:mm A').format('HH:mm:ss');
			}

			function getTimeStr(momentObj) {
				return momentObj.format('h:mm A');
			}

			function validateTimeOfPeriod(num, e) {
				console.log('unparsed num' + num);
				num = parseInt(num);
				var input = e.target;
				var value = input.value;
				console.log('num' + num);
				var currentTime = getTimeObj(value);
				if(num > 1) {
					var prev = num - 1;
					var prevKey = 't' + prev;
					console.log(prevKey);
					if(timeExists(prevKey) && currentTime) {
						var prevTime = times[prevKey];	
						console.log(prevTime);
						if(prevTime.isAfter(currentTime)) {
							input.value = getTimeStr(prevTime);
							alert('Should be later than ' + getTimeStr(prevTime));
							return;
						}
					}
				}

				var next = num + 1;
				var nextKey = 't' + next;
				console.log(nextKey);
				if(timeExists(nextKey) && currentTime) {
					var nextTime = times[nextKey];
					if(nextTime.isBefore(currentTime)) {
						input.value = getTimeStr(nextTime);
						alert('Should be sooner than ' + getTimeStr(nextTime));
						return;
					}
				}
				times['t' + num] = getTimeObj(e.target.value);
				var nextTC = $('.t'+next);
				if(nextTC.data("DateTimePicker") !== undefined) {
					nextTC.data("DateTimePicker").defaultDate(currentTime);
					nextTC.val('');
				}
			} 

			function timeExists(key) {
				return (times[key] != undefined);
			}

			function save() {
				var periods = [];
				var totalPeriods = 0;

				var radioButtons = $("input:radio[name='is_lunch_time']");

				totalPeriods = radioButtons.length;

				var lunchBreakPeriod = radioButtons.index(radioButtons.filter(':checked')) + 1;
				
				for(var i = 1; i <= 5; i++) {
					for(var j = 1; j <= totalPeriods; j++) {
						var period = {};
						period['day'] = i;
						period['period_num'] = j;
						period['start_time'] = get24HourStr($('.time-chooser.t' + j).val()); 
						period['end_time'] = get24HourStr($('.time-chooser.t' + j * 2).val());

						if(j != lunchBreakPeriod) {
							var tempPeriod = $('.r'+i+'.c'+j).children()[0];
							if(tempPeriod != undefined) {
								period['subject_class_id'] = tempPeriod.getAttribute('data-subject-class-id');
								period['room'] = tempPeriod.getElementsByClassName('room')[0].value;
							}
						} else {
							period['is_lunch_break'] = 1;
						}
						periods.push(period);
					}
				}

				console.log(periods);

				$.ajax({
					type: "POST",
			        data: {
				        "periods": periods,
						"class_id": {{ $class_id }}
			        },
					url: "addOrUpdatePeriods", 
					headers: {
				    	'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				    },
					success: function(result){
			        	console.log(result);
					},
					error: function(error) {
						console.log(error);
					}
				});

				return false;
			}

			function onRadioSelect(radioButton) {
				changeColumnsColor(currentValue, '#eee', 'white');
				currentValue = radioButton.value;
				changeColumnsColor(currentValue, '#FFD54F', '#FFD54F', true);
			}

			function removeOneColumn() {
				var period_count = ($(".lunch-break-row td").length) - 2;
				$('.c'+period_count).remove();
				$('#lt'+period_count).parent().remove();
				$('.tc'+period_count).remove();
			}

			function addOneColumn() {
				var period_count = $(".lunch-break-row td").length - 2;
				var next = period_count + 1;
				
				var lunch_break_column = '<td class="small-cell redips-mark lunch-time"><input type="radio" name="is_lunch_time" id="lt'+ next +'" value="'+ next +'" onclick="onRadioSelect(this)"/><label for="lt'+ next +'"></label></td>';

				$('.lunch-break-row td:eq('+ period_count +')').after(lunch_break_column);

				for(var i = 1; i <= 5; i++) {
					$('.row' + i).append('<td class="slot r'+i+' c'+ next +'"></td>');
				}

				var time_chooser_column = '<td class="medium-cell tc'+next+' redips-mark dark"><input type="text" class="time-chooser t'+ ((next*2)-1) +'" onchange="ontimechange(this)" required/> : <input type="text" class="time-chooser t'+(next*2)+'" onchange="ontimechange(this)" required/></td>'
				$('.time-chooser-row td:eq('+ period_count +')').after(time_chooser_column);

				var num = 0,			// number of successfully placed elements
				rd = REDIPS.drag;	// reference to the REDIPS.drag lib
				// initialization
				rd.init();
				rd.dropMode = 'single';
				// set hover color
				rd.hover.colorTd = '#9BB3DA';

				initializeTimeChooser();
			}

			function changeColumnsColor(index, bgcolor, bordercolor, isnew) {
				var columns = document.getElementsByClassName('c' + currentValue);
				for(var i = 0; i < columns.length; i++) {
					columns[i].innerHTML = "";
					columns[i].setAttribute("style", "background-color: "+ bgcolor +"; border-color: "+bordercolor);
					if(isnew) {
						columns[i].classList.add("redips-mark");
					} else {
						columns[i].classList.remove("redips-mark");
					}
				}

				if(isnew) { 
					columns[2].innerHTML = "<p style='font-size: 1.2em;text-align:center;color:white'>Lunch<br> time</p>";
				}
			}
		</script>
@endsection