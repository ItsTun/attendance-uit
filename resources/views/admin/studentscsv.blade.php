@extends ('layouts.admin_layout')

@section ('title')
	Students
@endsection

@section ('content')
	<div class="card" style="margin: 7px;">
		<div class="card-block">
			<div class="container">
				<div class="row">
					<div class="col-md-4">
						Select class
						<select id="classesSelect" class="form-control form-control-line" style="margin-top: 5px;" onchange="showStudents()">
							@foreach ($classes_ary as $key => $value) 
								<optgroup label="{{ $key }}">
									@foreach ($value as $v)
										<option id="{{ $v['class_id'] }}" value="{{ $v['short_form'] }}" name="{{ $v['name'] }}">{{ $v['name'] }}</option>
									@endforeach
								</optgroup>
							@endforeach
						</select>
					</div>
					<div class="col-md-8">
						<form action="/admin/students/csv" class="csv-upload-form" 
							enctype="multipart/form-data" method="POST" onsubmit="return upload()">
							{{ csrf_field() }}
							Select csv file to import
							<div class="row">
								<div class="col-md-6">
									<input type="file" name="students_csv" accept=".csv" class="form-control" style="margin-top: 2px;">	
								</div>	
								<div class="col-md-6">
									<button class="btn btn-md btn-success" type="submit">Import File</button>	
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="card" style="margin: 7px;" id="studentsResult">
		<div class="card-block">
			<div class="container">
				<div class="row" style="text-align: right;">
					<div class="col-md-4"></div>
					<div class="col-md-4"></div>
					<div class="col-md-4">
						<form id="form" action="saveStudentsFromCSV" method="POST">
							{{ csrf_field() }}
							<button class="btn btn-md btn-success" onclick="saveStudents()">Save</button>
						</form>
					</div>
				</div>
				<br>
				<div class="row">
					<table class="table table-bordered">
						<thead>
							<tr>
								<td></td>
								<td><strong>Roll No</strong></td>
								<td><strong>Name</strong></td>
								<td><strong>Email</strong></td>
								<td><strong>Class</strong></td>
							</tr>
						</thead>
						<tbody id="students">
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
@endsection

@section('scripts')
	<script>
		var students = [];
		var classOptions = document.getElementById("classesSelect").options;
		var selectedClass;
		var errorIndexes = [];

		window.onload = function() {
			var studentsResult = document.getElementById("studentsResult");
			studentsResult.style.display = 'none';
		}

		function upload() {
			var form = $('.csv-upload-form')[0];
			var fd = new FormData(form);    
			$.ajax({
			  url: 'getArrayFromCSV',
			  data: fd,
			  processData: false,
			  contentType: false,
			  type: 'POST',
			  success: function(data){
			    students = data;

			    showStudentResultDiv();
			    showStudents();
			  }
			});
			return false;
		}

		function showStudentResultDiv() {
			document.getElementById("studentsResult").style.display = "block";
		}

		function showStudents() {
			selectedClass = classOptions[classOptions.selectedIndex].value;

			var studentsTableBody = document.getElementById("students");
			removeRowsInTable();

			var counter = 0;
			students.forEach(function(element) {
				if(!element) {
					counter++;
					return;
				}

				var row = studentsTableBody.insertRow();
				row.id = "row_" + element['roll_no'] + "_" + counter;

				var rowRemoveCol = row.insertCell(0);
				rowRemoveCol.innerHTML = "<button id='remove_" + element['roll_no'] + "_" + counter + "' type='button' class='btn btn-danger' onclick='removeRow(" + counter + ", " + element['roll_no'] + ")'>Remove</button>";

				var rollNoCol = row.insertCell(1);
				rollNoCol.id = "col_" + element['roll_no'] + "_" + counter;
				rollNoCol.innerHTML = selectedClass + "-" + "<input class='form-control form-control-line' type='text' value='" + element['roll_no'] + "' onfocusin='onFocus(" + counter + ', "roll_no", ' + element['roll_no'] + ")' onblur='onBlur(this, " + counter + ', "roll_no", ' + element['roll_no'] + ")' pattern='[A-Za-z]{3}' style='width: 100px;'>";

				var nameCol = row.insertCell(2);
				nameCol.innerHTML = "<input class='form-control form-control-line' type='text' value='" + element['name'] + "'>";

				var emailCol = row.insertCell(3);
				emailCol.id = "col_" + element['email'] + "_" + counter;
				emailCol.innerHTML = "<input class='form-control form-control-line' type='email' value='" + element['email'] + "' onfocusin='onFocus(" + counter + ', "email", ' + '"' +  element['email'] + '"' + ")' onblur='onBlur(this, " + counter + ', "email", ' + '"' + element['email'] + '"' + ")'>";

				var classCol =  row.insertCell(4);
				classCol.innerHTML = classOptions[classOptions.selectedIndex].getAttribute('name');

				showDuplicateError(counter, 'roll_no', element['roll_no']);
				showDuplicateError(counter, 'email', element['email']);

				counter++;
			});
		}

		function removeRowsInTable() {
			var table = document.getElementById("students");
			for (var i = 0; i < table.rows.length;) {
				table.deleteRow(i);
			}
		}

		function removeRow(index, id) {
			var foundIndexes = findOccurences(index, 'roll_no');
			if (foundIndexes.length === 1) {
				document.getElementById("col_" + students[foundIndexes[0]]['roll_no'] + "_" + foundIndexes[0]).className = '';
			}

			foundIndexes = findOccurences(index, 'email');
			if (foundIndexes.length === 1) {
				document.getElementById("col_" + students[foundIndexes[0]]['email'] + "_" + foundIndexes[0]).className = '';
			}

			var table = document.getElementById("students");
			students[index] = null;
			table.removeChild(document.getElementById("row_" + id + "_" + index));
		}

		function findOccurences(index, type) {
			foundIndexes = [];
			for (var i = 0; i < students.length; i++) {
				if (students[index] === null || students[i] === null)
					continue;
				if (students[index][type] === students[i][type] && index !== i) {
					foundIndexes.push(i);
				}
			}
			return foundIndexes;
		}

		function showDuplicateError(index, type, id) {
			var foundIndexes = findOccurences(index, type);
			if (foundIndexes.length !== 0) {
				document.getElementById("col_" + id + "_" + index).className = 'table-danger';
			}
		}

		function onFocus(index, type, id) {
			errorIndexes = findOccurences(index, type);
		}

		function onBlur(x, index, type, id) {
			students[index][type] = x.value;
			x.parentElement.id = "col_" + x.value + "_" + index;

			var foundIndexes = findOccurences(index, type);
			if (foundIndexes.length === 0) {
				document.getElementById("col_" + x.value + "_" + index).className = '';
			} else {
				document.getElementById("col_" + x.value + "_" + index).className = 'table-danger';
				for (var i = 0; i < foundIndexes.length; i++) {
					document.getElementById("col_" + students[foundIndexes[i]][type] + "_" + foundIndexes[i]).className = 'table-danger';
				}
			}
			if (errorIndexes.length === 1 && foundIndexes.length === 0) {
				document.getElementById("col_" + students[errorIndexes[0]][type] + "_" + errorIndexes[0]).className = '';
			}

			if (type === 'roll_no' && !validateRollNo(x.value)) {
				document.getElementById("col_" + x.value + "_" + index).className = 'table-danger';
			}

			if (type === 'email' && !validateEmail(x.value)) {
				document.getElementById("col_" + x.value + "_" + index).className = 'table-danger';
			}
		}

		function validateRollNo(rollNo) {
			var re = /^[1-9]+$/;
    		return re.test(rollNo);
		}

		function validateEmail(email) {
			var re = /^\w+@uit.edu.mm$/
			return re.test(email);
		}

		function saveStudents() {
			if (!isErrorClean()) {
				alert("Errors must be cleaned first!");
				return;
			}
			studentsAry = getStudents();
			if (studentsAry.length === 0) {
				alert("No data to save!");
			}

			var input = document.createElement("input");
			input.setAttribute("type", "hidden");
			input.setAttribute("name", "students");
			input.setAttribute("value", JSON.stringify(studentsAry));

			var form = document.getElementById('form');
		    form.appendChild(input);
		    form.submit();
		}

		function isErrorClean() {
			var errorElements = document.getElementById('students').getElementsByClassName('table-danger');
			return errorElements.length === 0
		}

		function getStudents() {
			var studentsAry = [];
			students.forEach(function(element) {
				if (element !== null) {
					studentsAry.push(element);
					studentsAry[studentsAry.length - 1]['roll_no'] = selectedClass + '-' + element['roll_no'];
					studentsAry[studentsAry.length - 1]['class_id'] = classOptions[classOptions.selectedIndex].id;
				}
			});
			return studentsAry;
		}
	</script>
@endsection