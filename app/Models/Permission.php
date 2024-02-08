<?php
namespace App\Models;

use App\Traits\Contributor;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Permission extends Model {
    use HasFactory, Contributor;
    protected $fillable = [
        'action_id',
        'user_id',
        'perm_status',
    ];

    //Department
    const LIST_DEPARTMENT = 1;
    const ADD_DEPARTMENT = 2;
    const EDIT_DEPARTMENT = 3;
    const VIEW_DEPARTMENT = 4;
    const DELETE_DEPARTMENT = 5;
    const EXPORT_DEPARTMENT_TO_EXCEL = 6;
    const STATUS_CHANGE_DEPARTMENT = 7;

    //Staff
    const LIST_STAFF = 8;
    const ADD_STAFF = 9;
    const EDIT_STAFF = 10;
    const VIEW_STAFF = 11;
    const DELETE_STAFF = 12;
    const EXPORT_STAFF_TO_EXCEL = 13;
    const STATUS_CHANGE_STAFF = 14;
    const CHANGE_STAFF_PERMISSION = 15;
    const RESET_STAFF_PASSWORD = 16;

    //Leave
    const LIST_LEAVE = 17;
    const ADD_LEAVE = 18;
    const EDIT_LEAVE = 19;
    const VIEW_LEAVE = 20;
    const DELETE_LEAVE = 21;
    const EXPORT_LEAVE_TO_EXCEL = 22;
    const APPROVE_LEAVE = 23;
    const UNAPPROVE_LEAVE = 24;

    public static function checkPermission($permission) {
        $userId = auth()->id();
        $permissionGranted = Permission::where('action_id',$permission)->where('user_id',$userId)->where('perm_status','A')->exists();
        return $permissionGranted;    
    }

    public static function listPermission($id) {
        $permSections = DB::table('perm_sections')
            ->join('perm_actions','perm_sections.id', '=', 'perm_actions.section_id')
            ->select('perm_sections.id as section_id','section_name','perm_actions.id as action_id','action_name')
            ->get();
        
        $sections = [];

        foreach ($permSections as $permSection) {
            $sectionId = $permSection->section_id;
 
            if (!isset($sections[$sectionId])) {
                $sections[$sectionId] = [
                    'section_id' => $permSection->section_id,
                    'section_name' => $permSection->section_name,
                    'actions' => []
                ];
            }
    
            $sections[$sectionId]['actions'][] = [
                'action_id' => $permSection->action_id,
                'action_name' => $permSection->action_name
            ];
        }

        $setActions = DB::table('permissions')->where('user_id',$id)->select('action_id')->get();

        $result =['sections'=>$sections , 'setActions'=>$setActions];
        return $result;
    }

}

