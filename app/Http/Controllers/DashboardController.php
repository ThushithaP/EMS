<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller {
    public function index() {

        $departmentCount = Department::selectRaw('SUM(dep_status ="'.Department::OPERATIVE.'") as opDepCount, SUM(dep_status ="'.Department::INOPERATIVE.'")
         as ipDepCount,SUM(dep_status != "'.Department::DELETE.'") as totalDepCount')->first();
        $staffCount = User::selectRaw('SUM(status = "'.User::AVAILABLE.'") as avbUserCount, SUM(status = "'.User::ON_LEAVE.'") as onLeaveUserCount,
        SUM(status = "'.User::SUSPENDED.'") as susUserCount, SUM(status = "'.User::TERMINATED.'") as termUserCount, SUM(status != "'.User::DELETE.'") as totalStaff',)->first();
        
        // dd($staffCount);
        return view('dashboard',['departmentCount' => $departmentCount, 'staffCount' => $staffCount]);
    }

}
