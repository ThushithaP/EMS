<?php

namespace App\Models;

use App\Traits\Contributor;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Leave extends Model {
    use HasFactory, Contributor;

    protected $fillable = [
        'staff_id',               
        'staff_name',               
        'staff_email',               
        'staff_mobile',               
        'staff_emp',               
        'leave_type',               
        'leave_from',               
        'report_date',               
        'days',               
        'supervisor',               
        'leave_to',               
        'res_person',               
        'reason',               
        'approve_status',               
        'approved_by',               
    ];

    // Leave Type
    const CASUAL = "C";
    const SICK = "S";
    const ANNUAL = "A";
    const UNPAID = "U";

    const LEAVE_AR = [
        self::CASUAL => "Casual Leave",
        self::SICK => "Sick Leave",
        self::ANNUAL => "Annual Leave",
        self::UNPAID => "Unpaid Leave",
    ];

    // Aprrove status
    const PENDING = "P";
    const NOT_APPROVED = "N";
    const APPROVED = "A";
    const DELETE = "D";

    const APPROVE_STATUS_AR = [
        self::PENDING => "Pending",
        self::NOT_APPROVED => "Not Approved",
        self::APPROVED => "Approved",
    ];

    public static function getReadableLeaveStatus($cLeave) {
        return isset(self::LEAVE_AR[$cLeave]) ? self::LEAVE_AR[$cLeave] : "Unknown";
    }
    public static function getReadableApproveStatus($cStatus) {
        return isset(self::APPROVE_STATUS_AR[$cStatus]) ? self::APPROVE_STATUS_AR[$cStatus] : "Unknown";
    }

    public function scopeFilter($query, $filters = []) {
        
        return $query->where('approve_status','!=','D')
        ->when(isset($filters['search']) , function($query) use ($filters){
            $query->where(function($query) use ($filters){
                $query->where('res_person', 'LIKE', "%{$filters['search']}%")
                ->orWhere('leave_from', 'LIKE', "%{$filters['search']}%")
                ->orWhere('leave_to', 'LIKE', "%{$filters['search']}%")
                ->orWhere('staff_email', 'LIKE', "%{$filters['search']}%")
                ->orWhere('staff_emp', 'LIKE', "%{$filters['search']}%")
                ->orWhere('staff_name', 'LIKE', "%{$filters['search']}%");
            });
        })
        ->when(isset($filters['id_array']), function($query) use ($filters) {
            $query->whereIn('id',explode(",",$filters['id_array']));
        })
        ->when(isset($filters['staff_id']), function($query) use ($filters) {
            $query->where('staff_id',$filters['staff_id']);
        })
        ->when(isset($filters['type']), function($query) use ($filters) {
            $query->where('leave_type',$filters['type']);
        })
        ->when(isset($filters['status']), function($query) use ($filters) {
            $query->where('approve_status',$filters['status']);
        });
    }
}
