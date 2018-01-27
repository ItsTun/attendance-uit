@extends ('layouts.admin_layout')

@section ('title')
	Import Teachers With CSV
@endsection

@section ('styles')
	<style type="text/css">
		.default-width {
			width: 80%;
		}
	</style>
@endsection

@section ('content')
	<div class="card" style="margin: 7px;">
		<div class="card-block">
			<div class="container">
				<div class="row">
					<div class="col-md-4">
						<label><strong>Select Faculty</strong></label>
						<select id="select_faculty" class="form-control" style="margin-top: 5px; width: 90%">
							@foreach ($faculties as $value)
								<option id="{{ $value['faculty_id'] }}">{{ $value['name'] }}</option>
							@endforeach
						</select>
					</div>
					<div class="col-md-4">
						<form class="csv-upload-form" 
							enctype="multipart/form-data" method="POST" onsubmit="return upload()">
							{{ csrf_field() }}
							<label>Select CSV file</label>
							<div class="row">
								<div class="col-md-10">
									<input type="file" name="teachers_csv" accept=".csv" class="form-control" style="margin-top: 2px;">	
								</div>
								<div class="col-md-2">
									<button class="btn btn-primary" type="submit" style="margin-top: 6px;">Import</button>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="card" style="margin: 7px;">
		<div class="card-block">
			<form>
				<div class="container form-group">
					<div class="row" style="text-align: right;">
						<div class="col-md-4"></div>
						<div class="col-md-4"></div>
						<div class="col-md-4">
							<button class="btn btn-primary">Save</button>
						</div>
					</div>
					<hr>
					<div class="row">
						<table class="table">
							<thead>
								<tr>
									<th>Options</th>
									<th>Name</th>
									<th>Email</th>
									<th>Faculty</th>
								</tr>
							</thead>
							<tbody></tbody>
						</table>
					</div>
				</div>
			</form>
		</div>
	</div>
@endsection

@section ('scripts')
	<script type="text/javascript">
		var teachers = [];

		$(function() {
			$('#select_faculty').change(function() {
				if (teachers.length > 0) {
					removeRowsInTableBody();
					showTeachers();
				}
			});
		});

		function upload() {
			var form = $('.csv-upload-form')[0];
			var fd = new FormData(form);
			$.ajax({
			  url: 'getTeacherArrayFromCSV',
			  data: fd,
			  processData: false,
			  contentType: false,
			  type: 'POST',
			  success: function(data) {
			  	teachers = data;
			  	removeRowsInTableBody();
			  	showTeachers();
			  },
			  error: function(data, status) {
			  	alert(data['responseText']);
			  }
			});
			return false;
		}

		function showTeachers() {
			teachers.forEach(function(teacher, index) {
				if (teacher == null) {
					return;
				}
				var row = $('<tr></tr>').attr({ id: 'row_' + index }).appendTo($('tbody'));
				var col_option = $('<td></td>').appendTo(row);
				var col_name = $('<td></td>').appendTo(row);
				var col_email = $('<td></td>').appendTo(row);
				var col_faculty = $('<td></td>').appendTo(row);

				$('<button></button>').attr({ 
					type: 'button', 
					class: 'btn btn-danger',
					id: 'btn_' + index
				})
				.text('Remove')
				.appendTo(col_option);

				$('#btn_' + index).click(function() {
					teachers[index] = null;
					removeRowInTableBody(index);
				});

				$('<input>').attr({ 
					type: 'text', 
					class: 'form-control default-width', 
					value: teacher['name'] })
				.appendTo(col_name);

				$('<input>').attr({ 
					type: 'text', 
					class: 'form-control default-width', 
					value: teacher['email'] })
				.appendTo(col_email);

				$('<span></span>').text($('#select_faculty').find(':selected').val()).appendTo(col_faculty);
			});
		}

		function removeRowsInTableBody() {
			$('tbody tr').remove();
		}

		function removeRowInTableBody(index) {
			$('#row_' + index).remove();
		}
	</script>
@endsection