@php
    use App\Models\Department;
    $count =1;
@endphp

<table>
    <thead>
    <tr>
        <th style="font-weight: bold;">#</th>
        <th style="font-weight: bold;">Department Name</th>
        <th style="font-weight: bold;">Email</th>
        <th style="font-weight: bold;">Description</th>
        <th style="font-weight: bold;">Status</th>
        <th style="font-weight: bold;">Last Modified Date</th>
    </tr>
    </thead>
    <tbody>
    @foreach($departments as $department)
        <tr>
            <td>{{ $count }}</td>
            <td>{{ $department->dep_name }}</td>
            <td>{{ $department->dep_email }}</td>
            <td>{{ $department->description }}</td>
            <td>{{ Department::getReadableStatus($department->dep_status) }}</td>
            <td>{{ $department->updated_at }}</td>
        </tr>
        @php
            $count++
        @endphp
    @endforeach
    </tbody>
</table>