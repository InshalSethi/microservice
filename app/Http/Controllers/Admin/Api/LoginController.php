<?php

namespace App\Http\Controllers\Admin\Api;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Permissions\Permissions;

class LoginController extends Controller
{
    public function loginApi(Request $request)
    {
        $credentials = $request->only('email', 'password');

        // Validate the request
        $validator = Validator::make($credentials, [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Find the user by email
        $admin = Admin::where('email', $credentials['email'])->first();

        if (!$admin || !Auth::guard('admin')->validate($credentials)) {
            return response()->json(['error' => 'Invalid email or password'], 401);
        }

        // Gen$admin->role;erate the JWT token
        $token = JWTAuth::fromUser($admin);
        $permissionManager = new  Permissions($admin->role->permissions);
        $permissions = $permissionManager->permissionsApi();

        // Construct user data
        $userData = [
            'id' => $admin->id,
            'fullName' => $admin->name, // Assuming 'name' field contains the full name
            'username' => $admin->username, // Assuming you have a 'username' field
            'avatar' => '/images/avatars/avatar-1.png', // Static for example; replace with dynamic if available
            'email' => $admin->email,
            'role' =>  strtolower($admin->role->role_name), // Assuming the role is 'admin',

        ];
        // Construct the response
        return response()->json([
            'userAbilityRules' => [
                [
                    'action' => 'manage',
                    'subject' =>'all'
                ]
            ],
            'accessToken' => $token,
            'userData' => $userData,
            'permissions'=>$permissions
        ]);
    }

    public function register(Request $request)
    {

        $credentials = $request->only('name', 'email', 'password', 'password_confirmation');

        // Validate the request
        $validator = Validator::make($credentials, [
            'name' => 'required|max:55',
            'email' => 'required|email|unique:admins',
            'password' => 'required|confirmed|min:8' // Optional: Add min length for better password security
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        $role = Role::where('role_name','admin')->firstOrFail();
        // If validation passes, hash the password and prepare data for insertion
        $userData = [
            'name' => $credentials['name'],
            'email' => $credentials['email'],
            'password' => bcrypt($credentials['password']),
            'role_id' => $role->id, // Assuming default role_id = 1
        ];
        
        $user = Admin::create($userData);

        $token = JWTAuth::fromUser($user);
        $permissionManager = new  Permissions($user->role->permissions);
        $permissions = $permissionManager->permissionsApi();

        // Construct user data
        $userData = [
            'id' => $user->id,
            'fullName' => $user->name, // Assuming 'name' field contains the full name
            'avatar' => '/images/avatars/avatar-1.png', // Static for example; replace with dynamic if available
            'email' => $user->email,
            'role' =>  strtolower($user->role->role_name), // Assuming the role is 'admin',

        ];
        // Construct the response
        return response()->json([
            'userAbilityRules' => [
                [
                    'action' => 'manage',
                    'subject' =>'all'
                ]
            ],
            'accessToken' => $token,
            'userData' => $userData,
            'permissions'=>$permissions
        ]);
    }

}
