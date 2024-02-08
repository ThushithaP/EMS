<?php

namespace App\Http\Controllers;

use App\Exports\LeaveExport;
use Illuminate\Http\Request;

use App\Exports\StaffExport;
use App\Http\Requests\LeaveFormRequest;
use App\Http\Requests\StaffFormRequest;
use App\Mail\StaffCreate;
use App\Models\Department;
use App\Models\Leave;
use App\Models\Permission;
use App\Models\User;
use App\Services\EncryptionService;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use PHPUnit\Framework\MockObject\Stub\ReturnArgument;
use Maatwebsite\Excel\Facades\Excel;

class LeaveController extends Controller {
    public function index() {
        $leaves = Leave::LEAVE_AR;
        $status = Leave::APPROVE_STATUS_AR;

        $addGranted = Permission::checkPermission(Permission::ADD_LEAVE);
        $editGranted = Permission::checkPermission(Permission::EDIT_LEAVE);
        $deleteGranted = Permission::checkPermission(Permission::DELETE_LEAVE);
        $exportGranted = Permission::checkPermission(Permission::EXPORT_LEAVE_TO_EXCEL);

        return view('leave.index',['leaves' => $leaves,'status' => $status, 'addGranted' => $addGranted,'deleteGranted' => $deleteGranted,
        'editGranted' => $editGranted,'exportGranted' => $exportGranted]);
    }
    
    public function store(LeaveFormRequest $request) {
        if($request->isMethod('post')) {
            if(isset($request->validator) && $request->validator->fails()) {
                return response()->json(['success'=> false, 'errors' => $request->validator->errors(), 'message'=> VALIDATION_FAILED],422);
            }
            $validatedData = $request->validated();
            $leave = Leave::create([
                'staff_id' => auth()->id(),
                'staff_name' => auth()->user()->init_name,
                'staff_email' => auth()->user()->email,
                'staff_mobile' => auth()->user()->mobile,
                'staff_emp' => auth()->user()->emp_no,
                'leave_type' => $validatedData['leave_type'],
                'leave_from' => $validatedData['leave_from'],
                'report_date' => $validatedData['report_date'],
                'days' => $validatedData['days'],
                'supervisor' => $validatedData['supervisor'],
                'leave_to' => $validatedData['leave_to'],
                'res_person' => $validatedData['res_person'],
                'reason' => $validatedData['reason'],
            ]);
 
            if ($leave) { 
                return response()->json(['success' => true, 'message' => LEAVE_REQUEST_ADDED_SUCCESSFULLY], 200);
            } else {
                return response()->json(['success' => false, 'message' => LEAVE_REQUEST_ADDED_FAILED], 500);
            }
        }
        return response()->json(['success' => false, 'message' => 'Invalid HTTP method'], 405);

    }

    public function list(Request $request) {
        if($request->ajax() && $request->method('get')) {
            $filters =[
                'search' => $request->input('search.value'),
                'status' => $request->input('status'),
                'type' => $request->input('type'),
                'staff_id' => auth()->id()
            ];

            $query = Leave::filter($filters);
            $totalRecords = $query->count();
            
            $columnNames = ['id', 'leave_type', 'leave_from', 'leave_to','supervisor','res_person','approve_status','updated_at']; 
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
            
            $leaves = $query->get();

            $data = [];
            foreach ($leaves as $leave) {
                $encryptId = EncryptionService::encrypt($leave->id,ID_ENCRYPTION_KEY);
                $buttonText ='';
                if(Permission::checkPermission(Permission::VIEW_LEAVE)) {
                    $buttonText .= '<a type="button" href="javascript:viewRecord(\'' .$encryptId. '\');" data-placement="top" 
                    title="View Leave" class="me-3 btn1 btn-outline-white btn-rounded btn-sm js-init-tooltip"><i class="bi bi-eye"></i></a>';
                }
                if($leave->approve_status == Leave::PENDING) {
                    if(Permission::checkPermission(Permission::EDIT_LEAVE)) {
                        $buttonText .= '<a type="button" data-placement="top" href="javascript:editRecord(\'' .$encryptId. '\');"
                        class="me-3 edit btn1 btn-outline-white btn-rounded btn-sm js-init-tooltip" title="Edit Leave"><i class="bi bi-pencil"></i></a>';
                    }
                    if(Permission::checkPermission(Permission::DELETE_LEAVE)) {
                        $buttonText .= '<a type="button" data-placement="top" href="javascript:deleteRecord(\'' .$encryptId. '\');"
                        class="me-3 edit btn1 btn-outline-white btn-rounded btn-sm js-init-tooltip" title="Delete Leave"><i class="bi bi-trash"></i></a>';
                    }
                }
                $data[] = [
                    'encid' => '<input type="checkbox" class="js-row-select" value="row_'.$encryptId.'">',
                    'leave_type' => Leave::getReadableLeaveStatus($leave->leave_type),
                    'leave_from' => $leave->leave_from,
                    'leave_to' => $leave->leave_to,
                    'supervisor' => $leave->supervisor,
                    'res_person' => $leave->res_person,
                    'approve_status' => Leave::getReadableApproveStatus($leave->approve_status),                 
                    'lmd' => ($leave->updated_at !==null) ? $leave->updated_at->format('Y-m-d H:i:s') : '',
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
            $leave = Leave::find($id);
            $leave->leave_rtype = Leave::getReadableLeaveStatus($leave->leave_type);
            $leave->encId = EncryptionService::encrypt($leave->id, ID_ENCRYPTION_KEY);
            unset($leave->id);
            unset($leave->staff_id);
            return response()->json(['leave'=>$leave]);
        } 
        return response()->json(['error' => 'Unknown error occoured'],403);
    }

    public function update(LeaveFormRequest $request) {
        if($request->method('post')) {
            if(isset($request->validator) && $request->validator->fails()) {
                return response()->json(['success'=>false,'errors' => $request->validator->errors(), 'message' => VALIDATION_FAILED]);
            }
            $validatedData = $request->validated();
            $id = trim(EncryptionService::decrypt($request->input('enc_leaveID'),ID_ENCRYPTION_KEY));
            $leave = Leave::find($id); 
            if(!$leave) {
                return response()->json(['success' => false, 'message' => LEAVE_NOT_FOUND], 404);
            }
            $leave->update([
                'leave_type' => $validatedData['leave_type'],
                'leave_from' => $validatedData['leave_from'],
                'report_date' => $validatedData['report_date'],
                'days' => $validatedData['days'],
                'supervisor' => $validatedData['supervisor'],
                'leave_to' => $validatedData['leave_to'],
                'res_person' => $validatedData['res_person'],
                'reason' => $validatedData['reason'],
            ]);
            if ($leave) { 
                return response()->json(['success' => true, 'message' => LEAVE_UPDATED_SUCCESSFULLY], 200);
            } else {
                return response()->json(['success' => false, 'message' => LEAVE_UPDATED_FAILED], 500);
            }
            return response()->json(['success' => false, 'message' => 'Invalid HTTP method'], 405);    
        }        
    }

    public function status(Request $request) {
        if($request->ajax() && $request->isMethod('post')) {
            $encIds = $request->input('id');        
            $id = trim(EncryptionService::decrypt($encIds,ID_ENCRYPTION_KEY));
            $act = trim($request->input('act'));
            $leave = Leave::find($id); 
            $updateArray =[];        
            if($act == 'delete') { $updateArray = ['approve_status' => Leave::DELETE];}

            if ($leave->update($updateArray)) { 
                return response()->json(['success' => true, 'message' => LEAVE_DELETED_SUCCESSFULLY ], 200);
            } else {
                return response()->json(['success' => false, 'message' => LEAVE_DELETED_FAILED], 500);
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
                $filters = ['id_array' => $idStr];
                $query  = Leave::filter($filters);
                $leaves = $query->get();
            } 
            
            return Excel::download(new LeaveExport($leaves), 'leaves.xlsx');
        } catch (\Throwable $e) {
            // dd($e);
            return response()->json(['error' => 'Unknown error occoured'], 500);
        }
    }
}
