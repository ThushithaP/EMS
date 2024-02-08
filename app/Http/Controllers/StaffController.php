<?php

namespace App\Http\Controllers;

use App\Exports\StaffExport;
use App\Http\Requests\StaffFormRequest;
use App\Mail\StaffCreate;
use App\Mail\StaffPasswordReset;
use App\Models\Department;
use App\Models\Permission;
use App\Models\User;
use App\Services\EncryptionService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use PhpParser\Node\Expr\Cast\String_;
use PHPUnit\Framework\MockObject\Stub\ReturnArgument;
use Maatwebsite\Excel\Facades\Excel;

class StaffController extends Controller {
    public function index() {
        $designations = User::DESIGNATION_AR;
        $status = User::STAFF_STATUS_AR;
        $departments = Department::filter();
        // $departments->transform(function ($department) {
        //     $department->id = EncryptionService::encrypt($department->id,ID_ENCRYPTION_KEY);
        //     return $department;
        // });
        $addGranted = Permission::checkPermission(Permission::ADD_STAFF);
        $statusChangeGranted = Permission::checkPermission(Permission::STATUS_CHANGE_STAFF);
        $deleteGranted = Permission::checkPermission(Permission::DELETE_STAFF);
        $exportGranted = Permission::checkPermission(Permission::EXPORT_STAFF_TO_EXCEL);

        $departments=  $departments->pluck('dep_name','id')->map(function($name,$id){
            return ['id' => EncryptionService::encrypt($id,ID_ENCRYPTION_KEY), 'name'=> $name];
        });

        return view('staff.index',['departments' => $departments, 'designations' => $designations,'status' => $status, 'addGranted' => $addGranted,
        'statusChangeGranted' => $statusChangeGranted,'deleteGranted' => $deleteGranted,'exportGranted' => $exportGranted]);
    }

    public function store(StaffFormRequest $request) {
        if($request->isMethod('post')) {
            if(isset($request->validator) && $request->validator->fails()) {
                return response()->json(['success'=> false, 'errors' => $request->validator->errors(), 'message'=> VALIDATION_FAILED],422);
            }
            $validatedData = $request->validated();
            $generatedPassword = User::generateRandomString();
            $hashPassword = Hash::make($generatedPassword);
            $user = User::create([
                'full_name' => $validatedData['full_name'],
                'first_name' => $validatedData['first_name'],
                'last_name' => $validatedData['last_name'],
                'init_name' => $validatedData['init_name'],
                'email' => $validatedData['email'],
                'nic' => $validatedData['nic'],
                'password' => $hashPassword,
                'designation' => $validatedData['designation'],
                'mobile' => $validatedData['mobile'],
                'phone' => $validatedData['phone'],
                'department' => EncryptionService::decrypt($validatedData['department'],ID_ENCRYPTION_KEY),
                'emp_no' => $validatedData['emp_no'],
                'address_1' => $validatedData['address_1'],
                'address_2' => $validatedData['address_2'],
                'district' => $validatedData['district'],

            ]);
            // if ($request->hasFile('image') && $user) {
            //     $userFolder = md5($user->id);
            //     $userFolderPath = 'storage/app/public/users/'.$userFolder;
            //     if(!is_dir($userFolderPath)) {
            //         mkdir($userFolderPath,0777,true);
            //     }

            //     $imageName = uniqid() . '_' . time() . '.' . $request->image->extension();
            //     // Store the image file in the storage/app/public directory
            //     $imagePath = $request->file('image')->storeAs('public/users/'.$userFolder,$imageName);

            //     $imageUrl = Storage::url($imagePath);
            //     $user->image = $imageUrl;
            //     $user->save();
            // }
            $mailData=[];
            // if($validatedData['email'] !== null) {
            //     $mailData = ['name' => $validatedData['first_name'].' '.$validatedData['last_name'], 'email' => $validatedData['email'] , 'password' => $generatedPassword];
            //     Mail::to($validatedData['email'])->send(new StaffCreate($mailData));
            // }
            if ($user) { 
                return response()->json(['success' => true, 'message' => STAFF_MEMBER_ADDED_SUCCESSFULLY], 200);
            } else {
                return response()->json(['success' => false, 'message' => STAFF_MEMBER_ADDED_FAILED], 500);
            }

        }
        return response()->json(['success' => false, 'message' => 'Invalid HTTP method'], 405);

    }

    public function list(Request $request) {
        if($request->ajax() && $request->method('get')) {
            $filters =[
                'search' => $request->input('search.value'),
                'status' => $request->input('status'),
                'designation' => $request->input('staff_designation')
            ];
            $query = User::filter($filters);
            $totalRecords = $query->count();
            
            $columnNames = ['id', 'first_name', 'last_name', 'email','mobile','designation','emp_no', 'status','updated_at']; 
            if ($request->has('order') && $request->has('columns')) {
                $order = $request->input('order')[0];
                $columnIndex = $order['column'];
                
                if (isset($columnNames[$columnIndex])) {
                    $column = $columnNames[$columnIndex];
                    $direction = $order['dir'];
                    $query->orderBy($column, $direction);
                }
            }
            
            $pageLength = $request->input('length','{{DEFAULT_TABLE_ROW}}');
            $page = $request->input('start', 0) / $pageLength + 1; 
            $query->offset(($page - 1) * $pageLength)->limit($pageLength);
            
            $users = $query->get();

            $data = [];
            foreach ($users as $user) {
                $encryptId = EncryptionService::encrypt($user->id,ID_ENCRYPTION_KEY);
                $buttonText ='';
                if(Permission::checkPermission(Permission::ADD_STAFF)) {
                    $buttonText .= '<a type="button" href="javascript:viewRecord(\'' .$encryptId. '\');" data-placement="top" 
                        title="View Staff" class="me-3 btn1 btn-outline-white btn-rounded btn-sm js-init-tooltip"><i class="bi bi-eye"></i></a>';
                }
                if(Permission::checkPermission(Permission::EDIT_STAFF)) {
                    $buttonText .= '<a type="button" data-placement="top" href="javascript:editRecord(\'' .$encryptId. '\');"
                    class="me-3 edit btn1 btn-outline-white btn-rounded btn-sm js-init-tooltip"
                    title="Edit Staff"><i class="bi bi-pencil"></i></a>';
                }
                // <i class="bi bi-calendar-event"></i>
                if(Permission::checkPermission(Permission::STATUS_CHANGE_STAFF)) {
                    if (in_array($user->status, [User::ON_LEAVE, User::SUSPENDED, User::TERMINATED], true)) {
                        $buttonText .= '<a type="button" data-placement="top" href="javascript:availableMember(\'' .$encryptId. '\');"
                            class="me-3 edit btn1 btn-outline-white btn-rounded btn-sm js-init-tooltip" title="Available Staff"><i class="bi bi-check-circle"></i></a>';
                    }
                    if($user->status == User::AVAILABLE) {
                        $buttonText .= '<a type="button" data-placement="top" href="javascript:resignedMember(\'' .$encryptId. '\');"
                        class="me-3 edit btn1 btn-outline-white btn-rounded btn-sm js-init-tooltip" title="Resigned Staff"><i class="bi bi-arrow-right-circle"></i></a>';

                        $buttonText .= '<a type="button" data-placement="top" href="javascript:terminateMember(\'' .$encryptId. '\');"
                        class="me-3 edit btn1 btn-outline-white btn-rounded btn-sm js-init-tooltip" title="Terminated Staff"><i class="bi bi-x-circle"></i></a>';

                        $buttonText .= '<a type="button" data-placement="top" href="javascript:suspendedMember(\'' .$encryptId. '\');"
                        class="me-3 edit btn1 btn-outline-white btn-rounded btn-sm js-init-tooltip" title="Suspended Staff"><i class="bi bi-pause-circle"></i></a>';
                    }
                }
                if(Permission::checkPermission(Permission::CHANGE_STAFF_PERMISSION)) {
                    $buttonText .= '<a type="button" data-placement="top" href="javascript:changePermission(\'' .$encryptId. '\');"
                    class="me-3 edit btn1 btn-outline-white btn-rounded btn-sm js-init-tooltip" title="Change Permission"><i class="bi bi-shield-plus"></i></a>';
                }
                if(Permission::checkPermission(Permission::RESET_STAFF_PASSWORD)) {
                    $buttonText .= '<a type="button" data-placement="top" href="javascript:resetPassword(\'' .$encryptId. '\');"
                    class="me-3 edit btn1 btn-outline-white btn-rounded btn-sm js-init-tooltip" title="Reset Password"><i class="bi bi-key"></i></a>';
                }
                if(Permission::checkPermission(Permission::DELETE_STAFF)) {
                    $buttonText .= '<a type="button" data-placement="top" href="javascript:deleteRecord(\'' .$encryptId. '\');"
                    class="me-3 edit btn1 btn-outline-white btn-rounded btn-sm js-init-tooltip" title="Delete Staff"><i class="bi bi-trash"></i></a>';
                }
                
                $data[] = [
                    'encid' => '<input type="checkbox" class="js-row-select" value="row_'.$encryptId.'">',
                    // 'photo' => '<img class="rounded-circle" src="' .asset($user->image) .'" width="60px">',
                    'photo' => '<img class="rounded-circle" src="' . ($user->image ? asset($user->image) : asset('image/user.png')) . '" width="30px">',
                    
                    'name' => $user->first_name.' '.$user->last_name,
                    'email' => '<a href="mailto:'.$user->email.'">'.$user->email.'</a>',
                    'mobile' => $user->mobile,
                    'designation' => User::getReadableDesignation($user->designation),
                    'emp_no' => $user->emp_no,
                    'status' => User::getReadableStaffStatus($user->status),
                    'lmd' => ($user->updated_at !==null) ?$user->updated_at->format('Y-m-d H:i:s') : '',
                    'action' => $buttonText
                ];
            }
            return response()->json([
                'data' => $data,
                'recordsTotal' =>  $totalRecords,
                'recordsFiltered' => $totalRecords,
            ]);
        }
        return abort(404);
    }

    public function show(Request $request) {
        if($request->ajax() && $request->isMethod('post')) {
            $id = trim(EncryptionService::decrypt($request->input('id'),ID_ENCRYPTION_KEY));
            $user = User::select('users.*', 'departments.dep_name')
                ->join('departments', 'users.department', '=', 'departments.id')
                ->find($id);
            // $user = User::with('department')->find($id);
            $user->department = EncryptionService::encrypt($user->department, ID_ENCRYPTION_KEY);
            $user->encId = EncryptionService::encrypt($user->id, ID_ENCRYPTION_KEY);
            $user->desig_name = User::getReadableDesignation($user->designation);
            unset($user->id);
            return response()->json(['user'=>$user]);
        } 
        return response()->json(['error' => 'Unknown error occoured'],403);
    }

    public function update(StaffFormRequest $request) {
        if($request->method('post')) {
            if(isset($request->validator) && $request->validator->fails()) {
                return response()->json(['success'=>false,'errors' => $request->validator->errors(), 'message' => VALIDATION_FAILED]);
            }
            $validatedData = $request->validated();
            $id = trim(EncryptionService::decrypt($request->input('enc_staffid'),ID_ENCRYPTION_KEY));
            $staff = User::find($id); 
            if(!$staff) {
                return response()->json(['success' => false, 'message' => STAFF_MEMBER_NOT_FOUND], 404);
            }
            $staff->update([
                'full_name' => $validatedData['full_name'],
                'first_name' => $validatedData['first_name'],
                'last_name' => $validatedData['last_name'],
                'init_name' => $validatedData['init_name'],
                'email' => $validatedData['email'],
                'nic' => $validatedData['nic'],
                'designation' => $validatedData['designation'],
                'mobile' => $validatedData['mobile'],
                'phone' => $validatedData['phone'],
                'department' => EncryptionService::decrypt($validatedData['department'],ID_ENCRYPTION_KEY),
                'emp_no' => $validatedData['emp_no'],
                'address_1' => $validatedData['address_1'],
                'address_2' => $validatedData['address_2'],
                'district' => $validatedData['district'],
            ]);
            if ($staff) { 
                return response()->json(['success' => true, 'message' => STAFF_MEMBER_UPDATED_SUCCESSFULLY], 200);
            } else {
                return response()->json(['success' => false, 'message' => STAFF_MEMBER_UPDATED_FAILED], 500);
            }
            return response()->json(['success' => false, 'message' => 'Invalid HTTP method'], 405);    
        }        
    }

    public function status(Request $request) {
        if($request->ajax() && $request->isMethod('post')) {
            $encIds = $request->input('id');
            if(is_array($encIds)) {
                foreach($encIds as $recId) {
                    $idAr[] = EncryptionService::decrypt(str_replace("row_","",$recId),ID_ENCRYPTION_KEY);
                }
                $idStr = implode(",",$idAr);
            } else {
                $idStr = trim(EncryptionService::decrypt($encIds,ID_ENCRYPTION_KEY));
            }
            $act = trim($request->input('act'));
            $staffMembers = User::whereIn('id', explode(",",$idStr))->get(); 

            $updateArray =[];
            
            if($act == 'available') { $updateArray = ['status' => User::AVAILABLE];}
            if($act == 'resigned') { $updateArray = ['status' => User::RESIGNED];}
            if($act == 'terminated') {$updateArray = ['status' => User::TERMINATED];}
            if($act == 'suspended') {$updateArray = ['status' => User::SUSPENDED];}
            if($act == 'delete') { $updateArray = ['status' => User::DELETE];}
            $successCount = 0;
            foreach ($staffMembers as $member) {
                if($member->update($updateArray)) {
                    $successCount++;
                }
            }
            if ($successCount == count($staffMembers)) { 
                return response()->json(['success' => true, 'message' =>is_array($encIds) ? STAFF_MEMBER_CHANGE_STATUS_SUCCESS_BULK : STAFF_MEMBER_CHANGE_STATUS_SUCCESS ], 200);
            } else {
                return response()->json(['success' => false, 'message' =>is_array($encIds) ? STAFF_MEMBER_CHANGE_STATUS_FAILED_BULK : STAFF_MEMBER_CHANGE_STATUS_FAILED], 500);
            }
        }
        return response()->json(['success' => false, 'message' => 'Invalid HTTP method'], 405);
    }

    public function export(Request $request) {
        try {   
            $filters = [];
            $encIds = $request->input('id');
            $idAr = [];
            if(is_array($encIds) && !empty($encIds)) {
                foreach($encIds as $recId) {
                    $idAr[] = EncryptionService::decrypt(str_replace("row_","",$recId),ID_ENCRYPTION_KEY);
                }
                $idStr = implode(",",$idAr);
                $staffMembers = User::select('users.*', 'departments.dep_name')
                ->join('departments', 'users.department', '=', 'departments.id')
                ->whereIn('users.id', explode(",",$idStr))->get();
            } 
            
            return Excel::download(new StaffExport($staffMembers), 'staff.xlsx');
        } catch (\Throwable $e) {
            // dd($e);
            return response()->json(['error' => 'Unknown error occoured'], 500);
        }
    }

    public function listPermission(Request $request) {
        $id = trim(EncryptionService::decrypt($request->input('id'),ID_ENCRYPTION_KEY));
        $result = Permission::listPermission($id);
        return response()->json(['permissions'=>$result['sections'], 'setActions'=>$result['setActions']]); 
    }

    public function setPermission(Request $request) {
        $actionsAr = $request->input('actions');
        $id = trim(EncryptionService::decrypt($request->input('enc_userId'),ID_ENCRYPTION_KEY));
        Permission::where('user_id',$id)->delete();
        try {
            if (!empty($actionsAr)) {
            foreach ($actionsAr as $action) {
                Permission::create([
                    'user_id'=>$id,
                    'action_id'=>$action
                ]);
            }
            return response()->json(['success' => true,'message' => PERMISSION_UPDATE_SUCCESS]);
        }
        } catch (\Throwable $e) {
            // dd($e);
            return response()->json(['success' => true,'message' => PERMISSION_UPDATE_FAILED]);
        }
    }

    public function resetPassword(Request $request) {
        try {
            $id = trim(EncryptionService::decrypt($request->input('id'), ID_ENCRYPTION_KEY));
            $generatedPassword = User::generateRandomString();
            $hashPassword = Hash::make($generatedPassword);
            
            $staff = User::findOrFail($id);
    
            $staff->update([
                'password' => $hashPassword,
            ]);
    
            if (!empty($generatedPassword)) {
                $mailData = [
                    'name' => $staff->first_name . ' ' . $staff->last_name,
                    'email' => $staff->email,
                    'password' => $generatedPassword
                ];
                Mail::to($staff->email)->send(new StaffPasswordReset($mailData));
            }
    
            return response()->json(['success' => true, 'message' => 'Password reset successfully']);
        } catch (ModelNotFoundException $e) {
            return response()->json(['success' => false, 'message' => 'User not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'An error occurred while resetting password'], 500);
        }
    }
    
}
