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
    /**
     * @OA\Post(
     *     path="/api/admin/login",
     *     summary="User login",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             required={"email", "password"},
     *             @OA\Property(
     *                 property="email",
     *                 type="string",
     *                 format="email",
     *                 example="testuser8@example.com"
     *             ),
     *             @OA\Property(
     *                 property="password",
     *                 type="string",
     *                 format="password",
     *                 example="password123"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful login",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="userAbilityRules",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(
     *                         property="action",
     *                         type="string",
     *                         example="manage"
     *                     ),
     *                     @OA\Property(
     *                         property="subject",
     *                         type="string",
     *                         example="all"
     *                     )
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="accessToken",
     *                 type="string",
     *                 example="eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvYXBpL2FkbWluL2xvZ2luIiwiaWF0IjoxNzI0OTMwNTEzLCJleHAiOjE3MjUwMTY5MTMsIm5iZiI6MTcyNDkzMDUxMywianRpIjoiR0ZkdHc0cFZkMHpJbEFiUCIsInN1YiI6IjExIiwicHJ2IjoiZGY4ODNkYjk3YmQwNWVmOGZmODUwODJkNjg2YzQ1ZTgzMmU1OTNhOSJ9.U-5llflEKkALpISH1g8cOLWaDCpjP_Od2y2TfK6ruQo"
     *             ),
     *             @OA\Property(
     *                 property="userData",
     *                 type="object",
     *                 @OA\Property(
     *                     property="id",
     *                     type="integer",
     *                     example=11
     *                 ),
     *                 @OA\Property(
     *                     property="fullName",
     *                     type="string",
     *                     example="Test User"
     *                 ),
     *                 @OA\Property(
     *                     property="username",
     *                     type="string",
     *                     example=null
     *                 ),
     *                 @OA\Property(
     *                     property="avatar",
     *                     type="string",
     *                     example="/images/avatars/avatar-1.png"
     *                 ),
     *                 @OA\Property(
     *                     property="email",
     *                     type="string",
     *                     format="email",
     *                     example="testuser8@example.com"
     *                 ),
     *                 @OA\Property(
     *                     property="role",
     *                     type="string",
     *                     example="admin"
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="permissions",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(
     *                         property="text",
     *                         type="string",
     *                         example="Dashboard Permissions"
     *                     ),
     *                     @OA\Property(
     *                         property="permissions",
     *                         type="array",
     *                         @OA\Items(
     *                             type="object",
     *                             @OA\Property(
     *                                 property="text",
     *                                 type="string",
     *                                 example="Dashboard Filters"
     *                             ),
     *                             @OA\Property(
     *                                 property="ability",
     *                                 type="boolean",
     *                                 example=true
     *                             )
     *                         )
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request - invalid credentials"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error"
     *     )
     * )
     */
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

    /**
     * @OA\Post(
     *     path="/api/admin/register",
     *     summary="User registration",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             required={"name", "email", "password", "password_confirmation"},
     *             @OA\Property(
     *                 property="name",
     *                 type="string",
     *                 example="Test User"
     *             ),
     *             @OA\Property(
     *                 property="email",
     *                 type="string",
     *                 format="email",
     *                 example="testuser8@example.com"
     *             ),
     *             @OA\Property(
     *                 property="password",
     *                 type="string",
     *                 format="password",
     *                 example="password123"
     *             ),
     *             @OA\Property(
     *                 property="password_confirmation",
     *                 type="string",
     *                 format="password",
     *                 example="password123"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Successful registration",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="userAbilityRules",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(
     *                         property="action",
     *                         type="string",
     *                         example="manage"
     *                     ),
     *                     @OA\Property(
     *                         property="subject",
     *                         type="string",
     *                         example="all"
     *                     )
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="accessToken",
     *                 type="string",
     *                 example="eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvYXBpL2FkbWluL3JlZ2lzdGVyIiwiaWF0IjoxNzI0ODg0MDk1LCJleHAiOjE3MjQ5NzA0OTUsIm5iZiI6MTcyNDg4NDA5NSwianRpIjoiazE5Um1QNzlNOFlLQzd0OCIsInN1YiI6IjExIiwicHJ2IjoiZGY4ODNkYjk3YmQwNWVmOGZmODUwODJkNjg2YzQ1ZTgzMmU1OTNhOSJ9.kQgMLsl2b4wnMTfUOiDmVxXIrtBZZZOUMACsrrtIymo"
     *             ),
     *             @OA\Property(
     *                 property="userData",
     *                 type="object",
     *                 @OA\Property(
     *                     property="id",
     *                     type="integer",
     *                     example=11
     *                 ),
     *                 @OA\Property(
     *                     property="fullName",
     *                     type="string",
     *                     example="Test User"
     *                 ),
     *                 @OA\Property(
     *                     property="avatar",
     *                     type="string",
     *                     example="/images/avatars/avatar-1.png"
     *                 ),
     *                 @OA\Property(
     *                     property="email",
     *                     type="string",
     *                     format="email",
     *                     example="testuser8@example.com"
     *                 ),
     *                 @OA\Property(
     *                     property="role",
     *                     type="string",
     *                     example="admin"
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="permissions",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(
     *                         property="text",
     *                         type="string",
     *                         example="Dashboard Permissions"
     *                     ),
     *                     @OA\Property(
     *                         property="permissions",
     *                         type="array",
     *                         @OA\Items(
     *                             type="object",
     *                             @OA\Property(
     *                                 property="text",
     *                                 type="string",
     *                                 example="Dashboard Filters"
     *                             ),
     *                             @OA\Property(
     *                                 property="ability",
     *                                 type="boolean",
     *                                 example=true
     *                             )
     *                         )
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request - validation errors or already registered email"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error"
     *     )
     * )
     */

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
