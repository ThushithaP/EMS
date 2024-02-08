@extends('home')

@section('section')
@php
    use Illuminate\Support\Facades\Auth;
@endphp
<h2 class="ml-8 mb-4">Hello, {{Auth::user()->first_name.' '.Auth::user()->last_name}}</h2>

<div class="mt-4">
    <h4 class="mt-0 fw-semibold">      
        <span>Departments Summary</span>
    </h4>
    <div class="row">
        <div class="col">
            <div class="card rounded p-2 flex-row">
                <span class="fw-semibold me-3"> {{ $departmentCount->totalDepCount }}</span>
                <span class="text-dark">Total Departments</span>
            </div>
        </div>
        <div class="col">
            <div class="card rounded p-2 flex-row">
                <span class="fw-semibold me-3">{{ $departmentCount->opDepCount }}</span>
                <span class="text-success">Operative Departments</span>
            </div>
        </div>
        <div class="col">
            <div class="card rounded p-2 flex-row">
                <span class="fw-semibold me-3">{{ $departmentCount->ipDepCount }}</span>
                <span class="text-warning">Inoperative Departments</span>
            </div>
        </div>   
    </div>
</div>
<div class="mt-4">
    <h4 class="mt-0 fw-semibold">      
        <span>Staff Summary</span>
    </h4>
    <div class="row">
        <div class="col">
            <div class="card rounded p-2 flex-row">
                <span class="fw-semibold me-3"> {{ $staffCount->totalStaff }}</span>
                <span class="text-dark">Total Staff</span>
            </div>
        </div>
        <div class="col">
            <div class="card rounded p-2 flex-row">
                <span class="fw-semibold me-3">{{ $staffCount->avbUserCount }}</span>
                <span class="text-success">Available Staff</span>
            </div>
        </div>
        <div class="col">
            <div class="card rounded p-2 flex-row">
                <span class="fw-semibold me-3">{{ $staffCount->onLeaveUserCount}}</span>
                <span class="text-warning">Leave Staff</span>
            </div>
        </div>
        <div class="col">
            <div class="card rounded p-2 flex-row">
                <span class="fw-semibold me-3">{{ $staffCount->susUserCount}}</span>
                <span class="text-info">Suspended Staff</span>
            </div>
        </div>
        <div class="col">
            <div class="card rounded p-2 flex-row">
                <span class="fw-semibold me-3">{{ $staffCount->termUserCount}}</span>
                <span class="text-danger">Terminated Staff</span>
            </div>
        </div>    
    </div>
</div>

@endsection
