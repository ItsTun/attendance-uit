@extends('layouts.teacher_layout')

@section('title')
    Attendance
@endsection

@section('styles')

@endsection

@section('content')
    <div class="container" style="padding-top: 20px;">
        <div class="card">
            <div class="card-block">
                <form action="#" method="get">
                    <div class="row">
                        <div class="col-md-4 col-lg-4 col-sm-12 col-xs-12">
                            <label for="class-select">Class</label>
                            <select class="form-control class-select" id="class-select" name="class_id">
                                <option disabled selected>Select a class</option>
                                @foreach($years as $year)
                                    <optgroup label="{{ $year->name }}">
                                        @foreach($year->klasses as $klass)
                                            <option value="{{ $klass->class_id }}"
                                                    @if($klass->class_id==$class_id) selected="selected" @endif>{{ $klass->name }}</option>
                                        @endforeach
                                    </optgroup>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 col-lg-4 col-sm-12 col-xs-12">
                            <label for="month-select">Month</label>
                            <select class="form-control class-select" id="month-select" name="month">
                                <option disabled selected>Select month</option>
                                @foreach ($months as $month) 
                                    <option value="{{ $month->month }}, {{ $month->year }}"
                                        @if ($month->month == $selected_month)
                                            selected="selected" 
                                        @endif>
                                        {{ App\Utils::getMonthString($month->month) }}, {{ $month->year }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-1 col-lg-1 col-sm-12 col-xs-12">
                            <label for="go-btn" style="color: white;">-</label>
                            <input type="submit" href="#" class="btn btn-info form-control"
                                   style="background-color:#1e88e5 !important" id="go-btn" value="Go"/>
                        </div>
                        <div class="col-md-2 col-lg-2 col-sm-12 col-xs-12">
                            <label for="export-csv" style="color: white;">-</label>
                            <input type="button" class="btn btn-info form-control"
                                   style="background-color:#1e88e5 !important" id="export-csv" value="Export CSV"/>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="card results">

        </div>
    </div>
@endsection

@section('scripts')
    <script>
        var _table_ = document.createElement('table'),
            _tr_ = document.createElement('tr'),
            _th_ = document.createElement('th'),
            _td_ = document.createElement('td');

        // Builds the HTML Table out of myList json data from Ivy restful service.
        function buildHtmlTable(arr) {
            var table = _table_.cloneNode(false),
                columns = addAllColumnHeaders(arr, table);
            for (var i = 0, maxi = arr.length; i < maxi; ++i) {
                var tr = _tr_.cloneNode(false);
                for (var j = 0, maxj = columns.length; j < maxj; ++j) {
                    var td = _td_.cloneNode(false);
                    cellValue = arr[i][columns[j]];
                    if(j != 0 && parseFloat(cellValue) < 75.00) {
                        td.style.color = "#fc4b6c";
                    }
                    td.appendChild(document.createTextNode(arr[i][columns[j]] || ''));
                    tr.appendChild(td);
                }
                table.appendChild(tr);
            }
            table.className += " table";
            return table;
        }

        // Adds a header row to the table and returns the set of columns.
        // Need to do union of keys from all records as some records may not contain
        // all records
        function addAllColumnHeaders(arr, table) {
            var columnSet = [],
                tr = _tr_.cloneNode(false);
            for (var i = 0, l = arr.length; i < l; i++) {
                for (var key in arr[i]) {
                    if (arr[i].hasOwnProperty(key) && columnSet.indexOf(key) === -1) {
                        columnSet.push(key);
                        var th = _th_.cloneNode(false);
                        th.appendChild(document.createTextNode(key.replace('##', '')));
                        tr.appendChild(th);
                    }
                }
            }
            table.appendChild(tr);
            return columnSet;
        }

        var json = '{!! json_encode($studentsAttendance) !!}';
        var arr = JSON.parse(json);
        arr.sort(function(a, b) {
            return (a['Roll No'].length - b['Roll No'].length) || (a['Roll No'].localeCompare(b['Roll No']));
        });
        $('.results').html((buildHtmlTable(arr)));

        if (arr != null && arr.length != 0) {
            $('#export-csv').show();
        } else {
            $('#export-csv').hide();
        }

        $('#export-csv').click(function() {
            if (arr == null || arr.length == 0) {
                alert('No data to export!');
                return;
            }
            var file_name = prompt('Enter File Name', '.csv');
            if (file_name == null) 
                return;
            exportCSV(arr, file_name);
        });

        function exportCSV(data, file_name) {
            let csvContent = "data:text/csv;charset=utf-8,";
            var header_ary = Object.keys(data[0]);
            let header_str = "";
            for (var i in header_ary) {
                header_str += header_ary[i].replace('##', '') + ',';
            }
            header_str = header_str.slice(0, -1);
            csvContent += header_str + "\r\n";

            data.forEach(function(obj) {
                var arr = Object.values(obj);
                let row = arr.join(',');
                csvContent += row + "\r\n";
            });

            var encodedUri = encodeURI(csvContent);
            var link = document.createElement("a");
            link.setAttribute("href", encodedUri);
            link.setAttribute("download", file_name);
            link.click();
        }
    </script>
@endsection