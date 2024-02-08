@php
    use App\Models\Leave;
    $count =1;
@endphp

<table>
    <thead>
    <tr>
        <th style="font-weight: bold;">#</th>
        <th style="font-weight: bold;">Name</th>
        <th style="font-weight: bold;">Email</th>
        <th style="font-weight: bold;">Mobile</th>
        <th style="font-weight: bold;">Emp No</th>
        <th style="font-weight: bold;">Leave Type</th>
        <th style="font-weight: bold;">Leave From</th>
        <th style="font-weight: bold;">Reported Date</th>
        <th style="font-weight: bold;">Days</th>
        <th style="font-weight: bold;">Supervisor</th>
        <th style="font-weight: bold;">Leave To</th>
        <th style="font-weight: bold;">Res. Person</th>
        <th style="font-weight: bold;">Reason</th>
        <th style="font-weight: bold;">Approve Status 01</th>
        <th style="font-weight: bold;">Last Modified Date</th>
    </tr>
    </thead>
    <tbody>
    @foreach($leaves as $leave)
        <tr>
            <td>{{ $count }}</td>
            <td>{{ $leave->staff_name }}</td>
            <td>{{ $leave->staff_email }}</td>
            <td>{{ $leave->staff_mobile }}</td>
            <td>{{ $leave->staff_emp }}</td>
            <td>{{ Leave::getReadableLeaveStatus($leave->leave_type) }}</td>
            <td>{{ $leave->leave_from }}</td>
            <td>{{ $leave->report_date }}</td>
            <td>{{ $leave->days }}</td>
            <td>{{ $leave->supervisor }}</td>
            <td>{{ $leave->leave_to }}</td>
            <td>{{ $leave->res_person }}</td>
            <td>{{ $leave->reason }}</td>
            <td>{{ Leave::getReadableApproveStatus($leave->approve_status) }}</td>
            <td>{{ $leave->updated_at }}</td>
        </tr>
        @php
            $count++
        @endphp
    @endforeach
    </tbody>
</table>