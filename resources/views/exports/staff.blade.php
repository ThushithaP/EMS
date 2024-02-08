@php
    use App\Models\User;
    $count =1;
@endphp

<table>
    <thead>
    <tr>
        <th style="font-weight: bold;">#</th>
        <th style="font-weight: bold;">Full Name</th>
        <th style="font-weight: bold;">First Name</th>
        <th style="font-weight: bold;">Last Name</th>
        <th style="font-weight: bold;">Name With Initials</th>
        <th style="font-weight: bold;">Status</th>
        <th style="font-weight: bold;">Email</th>
        <th style="font-weight: bold;">Phone</th>
        <th style="font-weight: bold;">Mobile</th>
        <th style="font-weight: bold;">Designation</th>
        <th style="font-weight: bold;">Emp No</th>
        <th style="font-weight: bold;">Department</th>
        <th style="font-weight: bold;">NIC</th>
        <th style="font-weight: bold;">Address 01</th>
        <th style="font-weight: bold;">Address 02</th>
        <th style="font-weight: bold;">Last Modified Date</th>
    </tr>
    </thead>
    <tbody>
    @foreach($staff as $member)
        <tr>
            <td>{{ $count }}</td>
            <td>{{ $member->full_name }}</td>
            <td>{{ $member->first_name }}</td>
            <td>{{ $member->last_name }}</td>
            <td>{{ $member->init_name }}</td>
            <td>{{ User::getReadableStaffStatus($member->status) }}</td>
            <td>{{ $member->email }}</td>
            <td>{{ $member->phone }}</td>
            <td>{{ $member->mobile }}</td>
            <td>{{ User::getReadableDesignation($member->designation) }}</td>
            <td>{{ $member->emp_no }}</td>
            <td>{{ $member->dep_name }}</td>
            <td>{{ $member->nic }}</td>
            <td>{{ $member->address_1 }}</td>
            <td>{{ $member->address_2 }}</td>
            <td>{{ $member->updated_at }}</td>
        </tr>
        @php
            $count++
        @endphp
    @endforeach
    </tbody>
</table>