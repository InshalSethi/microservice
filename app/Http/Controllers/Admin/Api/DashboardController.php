<?php

namespace App\Http\Controllers\Admin\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Permissions\Permissions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Support\Facades\Storage;

class DashboardController extends Controller
{

    protected $url;
    protected $user;
    public function __construct(UrlGenerator $url)
    {
        $this->url = $url;
        $this->user = Auth::guard('admin')->user();
    }
    public function getAdminDetails(Request $request)
    {
        $user = Auth::guard('admin')->user();
        $permissionManager = new  Permissions($user->role->permissions);
        $permissions = $permissionManager->permissionsApi();
        if (isset($user->image_path))
            $user->image_path = $this->url->to('/storage/profile_pictures/' . $user->image_path);
        else
            $user->image_path = Null;
        return response()->json([
            'admin_details' => $user,
            'permissions'=>$permissions
        ]);
    }
    public function updateAdminDetails(Request $request)
    {
        $userId = Auth::guard('admin')->user()->id;
        $user = User::find($userId);

        if($request->get('image'))
        {
            $image = $request->get('image');
            $fileName = 'profile-' . time();

            $logo = base64_decode($image);
            $ext = (explode('/', finfo_buffer(finfo_open(), $logo, FILEINFO_MIME_TYPE))[1]);

            $imageName =  $fileName . '.' . $ext;
            Storage::disk('local')->put("/public/profile_pictures/" . $imageName, $logo);
        }

        $user->name = $request->get('first_name');
        $user->last_name = $request->get('last_name');
        $user->phone_no = $request->get('phone_no');
        if ($request->get('image'))
            $user->image_path = $imageName;
        $user->save();
        return response()->json([
            'admin_details' => $user
        ]);
    }
    public function uploadImage($image, $fileName, $path)
    {
        $logo = base64_decode($image);
        $filename = (explode('/', finfo_buffer(finfo_open(), $logo, FILEINFO_MIME_TYPE))[0]);
        $ext = (explode('/', finfo_buffer(finfo_open(), $logo, FILEINFO_MIME_TYPE))[1]);
        $imageName =  $fileName . '.' . $ext;
        $path =  $path . $imageName;
        file_put_contents($path, $logo);
        return $imageName;
    }
}
