<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $teacher->name }} Hours Report - {{ $date->format('F Y') }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #333;
            line-height: 1.4;
            margin: 0;
            padding: 20px;
            font-size: 13px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 10px;
            border-bottom: 2px solid #2d3748;
        }
        .header h1 {
            margin: 0 0 5px 0;
            color: #1a202c;
            font-size: 24px;
        }
        .header p {
            margin: 0;
            color: #718096;
            font-size: 14px;
        }
        .summary-box {
            background-color: #f7fafc;
            border: 1px solid #e2e8f0;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        .summary-grid {
            width: 100%;
            margin-bottom: 10px;
        }
        .summary-grid td {
            width: 50%;
            vertical-align: top;
        }
        .summary-title {
            font-weight: bold;
            color: #4a5568;
            margin-bottom: 5px;
        }
        .summary-value {
            font-size: 16px;
            color: #2b6cb0;
            font-weight: bold;
        }
        .section-title {
            background-color: #2d3748;
            color: #fff;
            padding: 8px 12px;
            font-size: 14px;
            font-weight: bold;
            margin-top: 25px;
            margin-bottom: 15px;
            border-radius: 4px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #e2e8f0;
            padding: 8px 10px;
            text-align: left;
        }
        th {
            background-color: #edf2f7;
            color: #4a5568;
            font-size: 12px;
            text-transform: uppercase;
        }
        tr:nth-child(even) {
            background-color: #fbfbfc;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .badge {
            display: inline-block;
            padding: 3px 6px;
            font-size: 10px;
            font-weight: bold;
            border-radius: 3px;
        }
        .badge-warning { background: #feebc8; color: #c05621; }
        .badge-danger { background: #fed7d7; color: #c53030; }
        .badge-success { background: #c6f6d5; color: #276749; }
        .financial-summary {
            background-color: #ebf8ff;
            border: 1px solid #bee3f8;
            padding: 15px;
            margin-top: 30px;
            border-radius: 5px;
        }
        .financial-table {
            width: 100%;
        }
        .financial-table td {
            border: none;
            padding: 5px;
            font-size: 16px;
        }
        .total-row {
            font-weight: bold;
            font-size: 18px;
            color: #2c5282;
            border-top: 2px solid #bee3f8 !important;
        }
        .page-break {
            page-break-before: always;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>Teacher Hours Report</h1>
        <p>{{ $teacher->name }} | {{ $date->format('F Y') }}</p>
    </div>

    <div class="summary-box">
        <table class="summary-grid">
            <tr>
                <td>
                    <div class="summary-title">Total Attendances (Teacher Present)</div>
                    <div class="summary-value">{{ $stats['total_attendances'] }}</div>
                </td>
                <td>
                    <div class="summary-title">Total Teacher Absences</div>
                    <div class="summary-value" style="color: #c53030;">{{ $stats['teacher_absences'] }}</div>
                </td>
            </tr>
            <tr>
                <td style="padding-top: 15px;">
                    <div class="summary-title">Total Student Absences</div>
                    <div class="summary-value" style="color: #dd6b20;">{{ $stats['student_absences'] }}</div>
                </td>
                <td style="padding-top: 15px;">
                    <div class="summary-title">Classes Waited Half Time</div>
                    <div class="summary-value">{{ $stats['waited_half_time'] }}</div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Student Breakdown -->
    <div class="section-title">Student Breakdown</div>
    @if(count($studentStats) > 0)
        <table>
            <thead>
                <tr>
                    <th>Student Name</th>
                    <th class="text-center">Total Classes</th>
                    <th class="text-center">Attended</th>
                    <th class="text-center">Missed</th>
                    <th class="text-center">Waited Half Time</th>
                    <th class="text-center">Total Hours</th>
                </tr>
            </thead>
            <tbody>
                @foreach($studentStats as $student)
                <tr>
                    <td>
                        {{ $student['name'] }}
                        @if(!empty($student['durations']))
                            <div style="font-size: 10px; color: #718096; margin-top: 4px;">
                                Attended: 
                                @foreach($student['durations'] as $duration => $count)
                                    {{ $count }} session{{ $count > 1 ? 's' : '' }} of {{ $duration }}@if(!$loop->last), @endif
                                @endforeach
                            </div>
                        @endif
                    </td>
                    <td class="text-center">{{ $student['total_classes'] }}</td>
                    <td class="text-center"><span class="badge badge-success">{{ $student['attended'] }}</span></td>
                    <td class="text-center"><span class="badge {{ $student['missed'] > 0 ? 'badge-danger' : '' }}">{{ $student['missed'] }}</span></td>
                    <td class="text-center"><span class="badge {{ $student['waited_half_time'] > 0 ? 'badge-warning' : '' }}">{{ $student['waited_half_time'] }}</span></td>
                    <td class="text-center"><strong>{{ $student['total_hours'] }} hrs</strong></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p>No student classes recorded this month.</p>
    @endif

    <!-- Teacher Absences -->
    @if(count($teacherAbsencesList) > 0)
        <div class="section-title" style="background-color: #c53030;">Teacher Absences</div>
        <table>
            <thead>
                <tr>
                    <th style="width: 180px;">Student</th>
                    <th style="width: 180px;">Date & Time</th>
                    <th>Excuse / Remark</th>
                </tr>
            </thead>
            <tbody>
                @foreach($teacherAbsencesList as $absence)
                <tr>
                    <td>{{ $absence['student'] }}</td>
                    <td>{{ $absence['session'] }}</td>
                    <td>{{ $absence['remark'] }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <!-- Student Absences -->
    @if(count($studentAbsencesList) > 0)
        <div class="section-title" style="background-color: #dd6b20;">Student Absences</div>
        <table>
            <thead>
                <tr>
                    <th style="width: 180px;">Student Name</th>
                    <th style="width: 180px;">Date & Time</th>
                    <th>Excuse / Remark</th>
                </tr>
            </thead>
            <tbody>
                @foreach($studentAbsencesList as $absence)
                <tr>
                    <td>{{ $absence['student'] }}</td>
                    <td>{{ $absence['session'] }}</td>
                    <td>{{ $absence['remark'] }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <div class="financial-summary">
        <table class="financial-table">
            <tr>
                <td><strong>Total Regular Hours:</strong></td>
                <td class="text-right">{{ number_format($totalHours - $bonusHours, 2) }} hrs</td>
            </tr>
            @if($bonusHours > 0)
            <tr>
                <td><strong>Evaluation Bonus Hours:</strong></td>
                <td class="text-right">{{ number_format($bonusHours, 2) }} hrs</td>
            </tr>
            @endif
            <tr>
                <td><strong>Hourly Rate:</strong></td>
                <td class="text-right">${{ number_format($hourlyRate, 2) }}</td>
            </tr>
            <tr class="total-row">
                <td style="padding-top: 15px;">Final Account (Salary):</td>
                <td class="text-right" style="padding-top: 15px;">${{ number_format($totalEarnings, 2) }}</td>
            </tr>
        </table>
    </div>

    <div style="margin-top: 50px; text-align: center; color: #a0aec0; font-size: 10px;">
        Generated on {{ now()->format('Y-m-d H:i:s') }}
    </div>

</body>
</html>
