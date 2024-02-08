<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileFormRequest;
use App\Models\Department;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller {

    public function index() {
        $profile = User::find(auth()->id());
        $departments = Department::filter();
        return view('profile.index',['departments' => $departments , 'profile' => $profile]);
    }
    
    public function update(ProfileFormRequest $request) {
        if($request->method('post')) {
            if(isset($request->validator) && $request->validator->fails()) {
                return response()->json(['success'=>false,'errors' => $request->validator->errors(), 'message' => VALIDATION_FAILED]);
            }
            $validatedData = $request->validated();
            $id = auth()->id();
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
                'mobile' => $validatedData['mobile'],
                'phone' => $validatedData['phone'],
                'address_1' => $validatedData['address_1'],
                'address_2' => $validatedData['address_2'],
                'district' => $validatedData['district'],
            ]);
            if ($request->hasFile('image') && $staff) {
                $userFolder = md5($id);
                $userFolderPath = 'public/users/'.$userFolder;

                if (!Storage::exists($userFolderPath)) {
                    Storage::makeDirectory($userFolderPath);
                } else {
                    Storage::deleteDirectory($userFolderPath);
                    Storage::makeDirectory($userFolderPath);
                }

                $imageName = uniqid() . '_' . time() . '.' . $request->image->extension();
                // Store the image file in the storage/app/public directory
                $imagePath = $request->file('image')->storeAs('public/users/'.$userFolder,$imageName);

                $imageUrl = Storage::url($imagePath);
                $staff->image = $imageUrl;
                $staff->save();
            }
            if ($staff) { 
                return response()->json(['success' => true, 'message' => STAFF_MEMBER_UPDATED_SUCCESSFULLY], 200);
            } else {
                return response()->json(['success' => false, 'message' => STAFF_MEMBER_UPDATED_FAILED], 500);
            }
            return response()->json(['success' => false, 'message' => 'Invalid HTTP method'], 405);    
        }        
    }
}
