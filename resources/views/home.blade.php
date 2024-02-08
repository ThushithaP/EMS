@extends('layouts.app')

@section('content')

@php
    function isRouteActive($route) {
        return request()->is($route) ? 'active' : '';
    }
@endphp

<!-- The sidebar -->
<div class="">
    <div class="sidebar">                   
        <a class="menu-tag {{ isRouteActive('dashboard') }}" href="{{ url('/dashboard') }}">
            <span class="me-4">
                <i class="bi bi-display h5 menu-icon"></i>
            </span>
            <span class="menu-name">Dashboard</span>  
        </a>
        <a class="menu-tag {{ isRouteActive('department') }}" href="{{ url('/department') }}">
            <span class="me-4">
                <i class="bi bi-building h5 menu-icon"></i>
            </span>
            <span class="menu-name">Department</span>  
        </a>
        <a class="menu-tag {{ isRouteActive('staff') }}" href="{{ url('/staff') }}">
            <span class="me-4">
                <i class="bi bi-people h5 menu-icon"></i>
            </span>
            <span class="menu-name">Staff</span>  
        </a>
        <a class="menu-tag {{ isRouteActive('leave') }}" href="{{ url('/leave') }}">
            <span class="me-4">
                <i class="bi bi-calendar4-week h5 menu-icon"></i>
            </span>
            <span class="menu-name">My Leave</span>  
        </a>
        <a class="menu-tag {{ isRouteActive('leave/approval') }}" href="{{ url('/leave/approval') }}">
            <span class="me-4">
                <i class="bi bi-list-check h5 menu-icon"></i>
            </span>
            <span class="menu-name">Leave Approval</span>  
        </a>
           
    </div>

    <!-- Page content -->
    <div class="content">
        @yield('section')
    </div>
</div>
@endsection
