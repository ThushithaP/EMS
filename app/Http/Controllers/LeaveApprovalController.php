<?php

namespace App\Http\Controllers;

use App\Models\Leave;
use App\Models\Permission;
use App\Services\EncryptionService;
use Illuminate\Http\Request;

class LeaveApprovalController extends Controller {
    public function index() {
        
        $leaves = Leave::LEAVE_AR;
        $status = Leave::APPROVE_STATUS_AR;

        $addGranted = Permission::checkPermission(Permission::ADD_STAFF);
        $editGranted = Permission::checkPermission(Permission::EDIT_STAFF);
        $deleteGranted = Permission::checkPermission(Permission::DELETE_STAFF);
        $exportGranted = Permission::checkPermission(Permission::EXPORT_STAFF_TO_EXCEL);

        return view('leave.approval',['leaves' => $leaves,'status' => $status, 'addGranted' => $addGranted,'deleteGranted' => $deleteGranted,
        'editGranted' => $editGranted,'exportGranted' => $exportGranted]);
    }

    public function list(Request $request) {
        if($request->ajax() && $request->method('get')) {
            $filters =[
                'search' => $request->input('search.value'),
                'status' => $request->input('status'),
                'type' => $request->input('type')
            ];
            $query = Leave::find(auth()->id());
            $query = Leave::filter($filters);
            $totalRecords = $query->count();
            
            $columnNames = ['id','name','emp_no','email','staff_mobile','leave_type','supervisor','approve_status','updated_at']; 
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
                if(Permission::checkPermission(Permission::VIEW_STAFF)) {
                    $buttonText .= '<a type="button" href="javascript:viewRecord(\'' .$encryptId. '\');" data-placement="top" 
                    title="View Leave" class="me-3 btn1 btn-outline-white btn-rounded btn-sm js-init-tooltip"><i class="bi bi-eye"></i></a>';
                }
                if($leave->approve_status !== Leave::APPROVED && Permission::checkPermission(Permission::VIEW_STAFF)) {
                    $buttonText .= '<a type="button" data-placement="top" href="javascript:approveRecord(\'' .$encryptId. '\');"
                    class="me-3 edit btn1 btn-outline-white btn-rounded btn-sm js-init-tooltip" title="Approve Leave"><i class="bi bi-file-check"></i></a>';

                }
                if(($leave->approve_status == Leave::PENDING) && Permission::checkPermission(Permission::VIEW_STAFF)) {
                    $buttonText .= '<a type="button" data-placement="top" href="javascript:unapproveRecord(\'' .$encryptId. '\');"
                    class="me-3 edit btn1 btn-outline-white btn-rounded btn-sm js-init-tooltip" title="Unpprove Leave"><i class="bi bi-file-x"></i></a>';

                }
                // $columnNames = ['id','name','emp_no','email','mobile','leave_type','supervisor','approve_status','updated_at']; 
                $data[] = [
                    'encid' => '<input type="checkbox" class="js-row-select" value="row_'.$encryptId.'">',
                    'name' => $leave->staff_name,
                    'emp_no' => $leave->staff_emp,
                    'email' => $leave->staff_email,
                    'mobile' => $leave->staff_mobile,
                    'leave_type' => Leave::getReadableLeaveStatus($leave->leave_type),
                    'supervisor' => $leave->supervisor,
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

    public function status(Request $request) {
        if($request->ajax() && $request->isMethod('post')) {
            $encIds = $request->input('id');        
            $id = trim(EncryptionService::decrypt($encIds,ID_ENCRYPTION_KEY));
            $act = trim($request->input('act'));
            $leave = Leave::find($id); 
            $updateArray =[];        
            $message = "";
            if($act == 'approve') { 
                $updateArray = ['approve_status' => Leave::APPROVED];
                $message = LEAVE_APPROVED_SUCCESSFULLY;
            }
            if($act == 'notapprove') { 
                $updateArray = ['approve_status' => Leave::NOT_APPROVED];
                $message = LEAVE_NOT_APPROVED_SUCCESSFULLY;
            }

            if ($leave->update($updateArray)) { 
                return response()->json(['success' => true, 'message' => $message ], 200);
            } else {
                return response()->json(['success' => false, 'message' => ($act=='approve') ? LEAVE_APPROVED_FAILED : LEAVE_NOT_APPROVED_FAILED], 500);
            }
        }
        return response()->json(['success' => false, 'message' => 'Invalid HTTP method'], 405);
    }

}
