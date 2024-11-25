<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    public function register_admin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'full_name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:admins,email',
            'username' => 'required|string|unique:admins,username',
            'password' => 'required|string|confirmed', // Ensure the password is confirmed
            // 'status' => 'required|string',
            'admin_id' => 'required|string|max:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()->first(),
                'status' => '401',
            ], 401);
        }

        // Create a new admin
        $admin = Admin::create([
            'full_name' => $request->full_name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            // 'status' => $request->status,
            'admin_id' => $request->admin_id,
        ]);

        // Generate a token for the newly created admin
        $token = $admin->createToken('AdminToken')->plainTextToken;

        return response()->json([
            'message' => 'Admin registered successfully.',
            'admin' => $admin,
            'token' => $token,
            'status' => '200',
        ], 200);
    }

    function fetch_all_admins(Request $req){
        
    $perPage = 30; 
    
    $admins = Admin::orderBy("id", "desc")->paginate($perPage);
    
    $adminData = $admins->items();
    
    $totalCount = Admin::count(); 
    
    return response()->json(['status' => '200', 'Admins' => $adminData, 'total_admins' => $totalCount], 200);

    }

    function delete_admin_by_id($id) {
        
    $admin = Admin::find($id);
    
        if (!$admin) {
            return response([
                "status" => '401',
                "message" => 'Admin not found'
            ], 401);
        }
    
        $admin->delete();
    
        return response([
            "status" => '200',
            "message" => 'Admin deleted successfully',
        ]);
    }

    /**
     * Update an admin's details
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update_admin_by_id(Request $request, $id){

        $admin = Admin::find($id);
        if (!$admin) {
            return response()->json([
                'message' => 'Admin not found',
                'status' => '401',
            ], 401);
        }

        $rules = [
            'full_name' => 'nullable|string|max:255',
            'email' => 'nullable|string|email|unique:admins,email,' . $id,
            'username' => 'nullable|string|unique:admins,username,' . $id,
            //'password' => 'nullable|string|min:7|confirmed', 
            'status' => 'nullable|string',
            'admin_id' => 'nullable|string|max:6',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()->first(),
                'status' => '401',
            ], 401);
        }

        $updatedData = $request->only([
            'full_name',
            'username',
            'email',
            'status',
            'admin_id'
        ]);

        // if ($request->has('password')) {
        //     $updatedData['password'] = Hash::make($request->password);
        // }

        foreach ($updatedData as $key => $value) {
            if (is_null($value)) {
                unset($updatedData[$key]);
            }
        }

        $admin->update($updatedData);

        return response()->json([
            'message' => 'Admin updated successfully.',
            'status' => '200',
        ], 200);
    }

    /**
     * Admin login with higher performance
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login_admin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|max:255',
            'password' => 'required|string|min:7',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()->first(),
                'status' => '401',
            ], 401);
        }

        $admin = Admin::where('username', $request->username)
                      ->where('status', 'true')
                      ->first();

        if (!$admin) {
            return response()->json([
                'message' => 'Invalid credentials or account inactive.',
                'status' => '401',
            ], 401);
        }

        if (!Hash::check($request->password, $admin->password)) {
            return response()->json([
                'message' => 'Invalid credentials',
                'status' => '401',
            ], 401);
        }

        $token = $admin->createToken('AdminToken')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'admin' => $admin,
            'token' => $token,
            'status' => '200',
        ], 200);
    }

}
