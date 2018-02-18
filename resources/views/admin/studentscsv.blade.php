@extends ('layouts.admin_layout')

@section ('title')
    Import Students With CSV
@endsection

@section ('content')
    <div class="card" style="margin: 7px;">
        <div class="card-block">
            <div class="container">
                <div class="row">
                    <div class="col-md-4">
                        <label><strong>Select Class</strong></label>
                        <select id="classesSelect" class="form-control form-control-line" style="margin-top: 5px;"
                                onchange="showStudents()">
                            @foreach ($classes_ary as $key => $value)
                                <optgroup label="{{ $key }}">
                                    @foreach ($value as $v)
                                        <option id="{{ $v['class_id'] }}" value="{{ $v['short_form'] }}"
                                                name="{{ $v['name'] }}">{{ $v['name'] }}</option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-8">
                        <form class="csv-upload-form"
                              enctype="multipart/form-data" method="POST" onsubmit="return upload()">
                            {{ csrf_field() }}
                            <label>Select csv file to import</label>
                            <div class="row">
                                <div class="col-md-6">
                                    <input type="file" name="students_csv" accept=".csv" class="form-control"
                                           style="margin-top: 2px;">
                                </div>
                                <div class="col-md-6">
                                    <button class="btn btn-success" type="submit" style="margin-top: 6px;">Import File
                                    </button>
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
                            <button class="btn btn-success" onclick="saveStudents(); return false;">Save</button>
                        </form>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                            <tr>
                                <th>Options</th>
                                <th>Roll No</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Class</th>
                            </tr>
                            </thead>
                            <tbody id="students">
                            </tbody>
                        </table>
                    </div>
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

        window.onload = function () {
            var studentsResult = document.getElementById("studentsResult");
            studentsResult.style.display = 'none';
        }

        function upload() {
            var form = $('.csv-upload-form')[0];
            var fd = new FormData(form);
            $.ajax({
                url: "{{ route('admin.getStudentArrayFromCSV') }}",
                data: fd,
                processData: false,
                contentType: false,
                type: 'POST',
                success: function (data) {
                    students = data;

                    showStudentResultDiv();
                    showStudents();
                },
                error: function (data, status) {
                    alert(data['responseText']);
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
            students.forEach(function (element) {
                if (!element) {
                    counter++;
                    return;
                }

                var row = studentsTableBody.insertRow();
                row.id = "row_" + element['roll_no'] + "_" + counter;

                var rowRemoveCol = row.insertCell(0);
                rowRemoveCol.innerHTML = "<button id='remove_" + element['roll_no'] + "_" + counter + "' type='button' class='btn btn-danger' onclick='removeRow(" + counter + ", " + element['roll_no'] + ")'>Remove</button>";

                var rollNoCol = row.insertCell(1);
                rollNoCol.id = "col_" + element['roll_no'] + "_" + counter;
                rollNoCol.innerHTML = selectedClass + "-" + "<input class='form-control form-control-line' type='text' value='" + element['roll_no'] + "' onblur='onBlur(this, " + counter + ', "roll_no", ' + element['roll_no'] + ")' pattern='[A-Za-z]{3}' style='width: 100px;'>";

                var nameCol = row.insertCell(2);
                nameCol.innerHTML = "<input class='form-control form-control-line' type='text' value='" + element['name'] + "'>";

                var emailCol = row.insertCell(3);
                emailCol.id = "col_" + element['email'] + "_" + counter;
                emailCol.innerHTML = "<input class='form-control form-control-line' type='email' value='" + element['email'] + "' onblur='onBlur(this, " + counter + ', "email", ' + '"' + element['email'] + '"' + ")'>";

                var classCol = row.insertCell(4);
                classCol.innerHTML = classOptions[classOptions.selectedIndex].getAttribute('name');

                showError(counter, 'roll_no', element['roll_no']);
                showError(counter, 'email', element['email']);

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
            var table = document.getElementById("students");
            students[index] = null;
            table.removeChild(document.getElementById("row_" + id + "_" + index));

            markErrors('roll_no');
            markErrors('email');
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

        function showError(index, type, id) {
            var foundIndexes = findOccurences(index, type);
            document.getElementById("col_" + id + "_" + index).className = '';

            if (foundIndexes.length !== 0) {
                document.getElementById("col_" + id + "_" + index).className = 'table-danger';
            }

            if (type === 'roll_no' && !validateRollNo(id))
                document.getElementById("col_" + id + "_" + index).className = 'table-danger';

            if (type === 'email' && !validateEmail(id))
                document.getElementById("col_" + id + "_" + index).className = 'table-danger';
        }

        function onBlur(x, index, type, id) {
            students[index][type] = x.value;
            x.parentElement.id = "col_" + x.value + "_" + index;

            markErrors(type);
        }

        function markErrors(type) {
            for (var i = 0; i < students.length; i++) {
                if (students[i] != null) {
                    showError(i, type, students[i][type]);
                }
            }
        }

        function validateRollNo(rollNo) {
            var re = /^[1-9][0-9]*$/;
            return re.test(rollNo);
        }

        function validateEmail(email) {
            //var re = /^\w+@uit.edu.mm$/
            return true;
        }

        function saveStudents() {
            if (!isErrorClean()) {
                alert("Errors must be cleaned first!");
                return;
            }

            studentsAry = getStudents();
            if (studentsAry.length === 0) {
                alert("No data to save!");
                return false;
            }

            checkIfRollNoExists(studentsAry);
        }

        function isErrorClean() {
            var errorElements = document.getElementById('students').getElementsByClassName('table-danger');
            return errorElements.length === 0
        }

        function getStudents() {
            var studentsAry = [];
            students.forEach(function (element) {
                if (element !== null) {
                    var student = {};
                    student['roll_no'] = selectedClass + '-' + element['roll_no'];
                    student['name'] = element['name'];
                    student['email'] = element['email'];
                    student['class_id'] = classOptions[classOptions.selectedIndex].id;
                    studentsAry.push(student);
                }
            });
            return studentsAry;
        }

        function checkIfRollNoExists(studentsAry) {
            var roll_nos = [];
            studentsAry.forEach(function (element) {
                roll_nos.push(element['roll_no']);
            });

            $.get("{{ route('admin.checkIfRollNoExists') }}", {
                roll_nos: JSON.stringify(roll_nos)
            }, function (data, status) {
                if (data == null) return;

                if (data.length != 0) {
                    alert("Roll No Duplication! Please check these roll no(s) : " + data);
                    return;
                }
                checkIfEmailExists(studentsAry);
            });
        }

        function checkIfEmailExists(studentsAry) {
            var emails = [];
            studentsAry.forEach(function (element) {
                emails.push(element['email']);
            });

            $.get("{{ route('admin.checkIfEmailExists') }}", {
                emails: JSON.stringify(emails)
            }, function (data, status) {
                if (data == null) return;

                if (data.length != 0) {
                    alert("Email Duplication! Please check these emails : " + data);
                    return;
                }
                sendStudentsForm();
            });
        }

        function sendStudentsForm() {
            var input = document.createElement("input");
            input.setAttribute("type", "hidden");
            input.setAttribute("name", "students");
            input.setAttribute("value", JSON.stringify(studentsAry));

            var form = document.getElementById('form');
            form.appendChild(input);
            form.submit();
        }
    </script>
@endsection