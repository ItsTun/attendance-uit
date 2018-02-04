<html>
<head>
    <style>
        table {
            font-family: arial, sans-serif;
            border-collapse: collapse;
            width: 100%;
        }

        td, th {
            border: 1px solid #dddddd;
            text-align: left;
            padding: 8px;
        }

        tr:nth-child(even) {
            background-color: #dddddd;
        }
    </style>
</head>
<p style="font-weight: bold; margin-bottom: 15px; font-size: 1.2em; text-align: center;">Students with three or more
    days of absence
    in {{ $klass->name }}</p>
<table width="600" style="border:1px solid #333">
    <tr>
        <td>Roll No</td>
        <td>Name</td>
        <td>Absent Days</td>
    </tr>
    @foreach($students as $student)
        @for($i = 0; $i < sizeof($student['absent_dates']); $i++)
            <tr>
                <td>{{ $student['roll_no']  }}</td>
                <td>{{ $student['name']  }}</td>
                <td>{{ Carbon\Carbon::parse($student['absent_dates'][$i]['from'])->toFormattedDateString() }}
                    - {{ Carbon\Carbon::parse($student['absent_dates'][$i]['to'])->toFormattedDateString() }}
                    <br>
                    <br>
                    <span style="background: #ebebeb; color:black; margin-top: 8px; margin-bottom: 8px; padding: 3px;">{{ $student['total_absences'][$i] }}
                        days</span>
                    <br>
                </td>
            </tr>
        @endfor
    @endforeach
</table>
</html>