<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Traits\Contributor;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable , Contributor;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'email',
        'password',
        'full_name',
        'first_name',
        'last_name',
        'init_name',
        'nic',
        'designation',
        'mobile',
        'phone',
        'department',
        'emp_no',
        'address_1',
        'address_2',
        'district',
        'status',
        'image',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
    // Designation
    const ADMIN = 'A';
    const EXECUTIVE = 'E';
    const MANAGER ='M';
    const AUDITOR = 'O';

    const DESIGNATION_AR = [
        self::EXECUTIVE => 'Executive',
        self::MANAGER => 'Manager',
        self::AUDITOR => 'Auditor',
    ];

    // Status
    const AVAILABLE = 'A';
    const ON_LEAVE = 'L';
    const SUSPENDED = 'S';
    const RESIGNED = 'R';
    const TERMINATED = 'T';
    const DELETE = 'D';

    const STAFF_STATUS_AR = [
        self::AVAILABLE => 'Available',
        self::ON_LEAVE => 'On Leave',
        self::SUSPENDED => 'Suspended',
        self::RESIGNED => 'Resigned',
        self::TERMINATED => 'Terminated',
    ];

    public static function getReadableDesignation($cDesignation) {
        return isset(self::DESIGNATION_AR[$cDesignation]) ? self::DESIGNATION_AR[$cDesignation] : "Unknown";
    }

    public static function getReadableStaffStatus($cStatus) {
        return isset(self::STAFF_STATUS_AR[$cStatus]) ? self::STAFF_STATUS_AR[$cStatus] : "Unknown";
    }

    public static function generateRandomString() {
        $bytes = random_bytes(8);
        return substr(str_replace(['/', '+', '='], '', base64_encode($bytes)), 0, 8);
    }

    public function scopeFilter($query, $filters=[]) {
        return $query->where('status','!=','D')
        ->when(isset($filters['search']), function ($query) use ($filters) {
            $query->where(function ($query) use ($filters) {
                $query->where('first_name', 'LIKE', "%{$filters['search']}%")
                ->orWhere('last_name', 'LIKE', "%{$filters['search']}%")
                ->orWhere('emp_no', 'LIKE', "%{$filters['search']}%");
            });
        })
        ->when(isset($filters['status']), function ($query) use ($filters) {
            $query->where('status', $filters['status']);
        })
        ->when(isset($filters['designation']), function ($query) use ($filters) {
            $query->where('designation', $filters['designation']);
        });
    }
}
// arun pydh ubma vczx