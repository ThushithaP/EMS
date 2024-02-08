<?php

namespace App\Models;

use App\Traits\Contributor;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model {

    use HasFactory , Contributor;

    //status
    const OPERATIVE = "O";
    const INOPERATIVE = "I";
    const DELETE = "D";

    const STATUS_AR = [
        self::OPERATIVE => "Operative",
        self::INOPERATIVE => "Inoperative",
        self::DELETE => "Delete",
    ];

    public static function getReadableStatus($cStatus) {
        return isset(self::STATUS_AR[$cStatus]) ? self::STATUS_AR[$cStatus] : "Unknown";
    }
    
    protected $fillable = [
        'dep_name',
        'dep_email',
        'description',
        'dep_status',
    ];

    public function scopeFilter($query, $filters = []) {

        return $query->where('dep_status','!=','D')
        
        ->when(isset($filters['search']), function ($query) use ($filters) {
                $query->where(function ($query) use ($filters) {
                    $query->where('dep_name', 'LIKE', "%{$filters['search']}%")
                          ->orWhere('dep_email', 'LIKE', "%{$filters['search']}%");
                });
            })
        ->when(isset($filters['id_array']), function ($query) use ($filters) {
            $query->whereIn('id', explode(",",$filters['id_array']));
        })
        ->when(isset($filters['status']), function ($query) use ($filters) {
            $query->where('dep_status', $filters['status']);
        });
    }
}
