<?php

namespace App\Http\Controllers;

use App\Exports\DepartmentExport;
use App\Http\Requests\DepartmentFormRequest;
use App\Models\Department;
use App\Models\Permission;
use App\Services\EncryptionService;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class DepartmentController extends Controller {

    public function index() {

        return view('department.index');
    }
    
    public function store(DepartmentFormRequest $request) {
        if($request->isMethod('post')) {
            if (isset($request->validator) && $request->validator->fails()) {
                return response()->json(['success' => false, 'errors' => $request->validator->errors(),'message' => VALIDATION_FAILED], 422);
            }
            $validatedData = $request->validated();

            $department = Department::create([
                'dep_name' => $validatedData['dep_name'],
                'dep_email' => $validatedData['dep_email'],
                'description' => $validatedData['dep_desc'],
            ]);

            if ($department) { 
                return response()->json(['success' => true, 'message' => DEPARTMENT_CREATE_SUCCESSFULLY], 200);
            } else {
                return response()->json(['success' => false, 'message' => 'Unknown Error Occoured'], 500);
            }

        }
        return response()->json(['success' => false, 'message' => 'Invalid HTTP method'], 405);
    }

    public function list(Request $request) {
        if($request->ajax() && $request->isMethod('get')) {
            $filters = [
                'search' => $request->input('search.value'),
                'status' => $request->input('status'),
            ];
        
            $query = Department::filter($filters);
            $totalRecords = $query->count();

            $columnNames = ['id', 'dep_name', 'dep_email', 'dep_status','updated_at']; 
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

            $departments = $query->get();

            $data = [];
            foreach ($departments as $department) {
                $encryptId = EncryptionService::encrypt($department->id,ID_ENCRYPTION_KEY);
                $buttonText = '';
                if(Permission::checkPermission(Permission::VIEW_DEPARTMENT)) {
                    $buttonText .= '<a type="button" href="javascript:viewRecord(\'' .$encryptId. '\');" data-placement="top" 
                    title="View Department" class="me-3 btn1 btn-outline-white btn-rounded btn-sm js-init-tooltip"><i class="bi bi-eye"></i></a>';
                }
                if(Permission::checkPermission(Permission::EDIT_DEPARTMENT)) {
                    $buttonText .= '<a type="button" data-placement="top" href="javascript:editRecord(\'' .$encryptId. '\');"
                    class="me-3 edit btn1 btn-outline-white btn-rounded btn-sm js-init-tooltip"
                    title="Edit Department"><i class="bi bi-pencil"></i></a>';
                }
                if(Permission::checkPermission(Permission::STATUS_CHANGE_DEPARTMENT)) {
                    if($department->dep_status == Department::OPERATIVE) {
                        $buttonText .= '<a type="button" data-placement="top" href="javascript:inoperativeRecord(\'' .$encryptId. '\');"
                        class="me-3 edit btn1 btn-outline-white btn-rounded btn-sm js-init-tooltip"
                        title="Inoperative Department"><i class="bi bi-exclamation-circle"></i></a>';
                    }
                    if($department->dep_status == Department::INOPERATIVE) {
                        $buttonText .= '<a type="button" data-placement="top" href="javascript:operativeRecord(\'' .$encryptId. '\');"
                        class="me-3 edit btn1 btn-outline-white btn-rounded btn-sm js-init-tooltip"
                        title="Operative Department"><i class="bi bi-check-circle"></i></a>';
                    }
                }             
                if(Permission::checkPermission(Permission::DELETE_DEPARTMENT)) {
                    $buttonText .= '<a type="button" data-placement="top" href="javascript:deleteRecord(\'' .$encryptId. '\');"
                    class="me-3 edit btn1 btn-outline-white btn-rounded btn-sm js-init-tooltip" title="Delete Department"><i class="bi bi-trash"></i></a>';
                }
                $data[] = [
                    'encid' => '<input type="checkbox" class="js-row-select" value="row_'.$encryptId.'">',
                    'name' => $department->dep_name,
                    'email' => '<a href="mailto:'.$department->dep_email.'">'.$department->dep_email.'</a>',
                    'status' => ($department->dep_status == Department::OPERATIVE) ? 'Operative' : 'Inoperative',
                    'lmd' => ($department->updated_at !==null) ?$department->updated_at->format('Y-m-d H:i:s') : '',
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
            $department = Department::find($id);
            return response()->json(['department'=>$department]);
        } 
        return response()->json(['error' => 'Unknown error occoured'],403);
    }

    public function update(DepartmentFormRequest $request) {
        if($request->isMethod('post')) {
            if (isset($request->validator) && $request->validator->fails()) {
                return response()->json(['success' => false, 'errors' => $request->validator->errors(),'message' => VALIDATION_FAILED], 422);
            }
            $validatedData = $request->validated();

            $id = trim(EncryptionService::decrypt($request->input('enc_depid'),ID_ENCRYPTION_KEY));
            $department = Department::find($id); 
            if (!$department) {
                return response()->json(['success' => false, 'message' => 'Department not found'], 404);
            }

            $department->update([
                'dep_name' => $validatedData['dep_name'],
                'dep_email' => $validatedData['dep_email'],
                'description' => $validatedData['dep_desc'],
                'dep_status' => $validatedData['dep_status'],
            ]);

            if ($department) { 
                return response()->json(['success' => true, 'message' => DEPARTMENT_UPDATED_SUCCESSFULLY], 200);
            } else {
                return response()->json(['success' => false, 'message' => DEPARTMENT_UPDATED_FAILED], 500);
            }

        }
        return response()->json(['success' => false, 'message' => 'Invalid HTTP method'], 405);
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
            $departments = Department::whereIn('id', explode(",",$idStr))->get(); 

            $updateArray =[];
            
            if($act == 'inoperative') { $updateArray = ['dep_status' => Department::INOPERATIVE];}
            if($act == 'operative') {$updateArray = ['dep_status' => Department::OPERATIVE];}
            if($act == 'delete') { $updateArray = ['dep_status' => Department::DELETE];}
            $successCount = 0;
            foreach ($departments as $department) {
                if($department->update($updateArray)) {
                    $successCount++;
                }
            }
            if ($successCount == count($departments)) { 
                return response()->json(['success' => true, 'message' => DEPARTMENT_UPDATED_SUCCESSFULLY], 200);
            } else {
                return response()->json(['success' => false, 'message' => DEPARTMENT_UPDATED_FAILED], 500);
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
                $query  = Department::filter($filters);
                $departments = $query->get();
            } 
            
            return Excel::download(new DepartmentExport($departments), 'departments.xlsx');
        } catch (\Throwable $e) {
            // dd($e);
            return response()->json(['error' => 'Unknown error occoured'], 500);
        }
    }
}
