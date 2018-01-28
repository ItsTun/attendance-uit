@extends('layouts.admin_layout')

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
                        <div class="col-md-1 col-lg-1 col-sm-12 col-xs-12">
                            <label for="go-btn">-</label>
                            <input type="submit" href="#" class="btn btn-info form-control"
                                   style="background-color:#1e88e5 !important" id="go-btn" value="Go"/>
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
    </script>
@endsection