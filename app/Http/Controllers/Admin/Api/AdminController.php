<?php
namespace App\Http\Controllers\Admin\Api;
use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;
use Illuminate\Auth\Access\AuthorizationException;
class AdminController extends Controller
{
    protected $user;
    protected $url;
    public function __construct(UrlGenerator $url)
    {
        $this->url = $url;
        $this->user = Auth::guard('admin')->user();
    }
    public function index(){
        try {
            $this->authorizeForUser($this->user,'list', new Admin);
            $adminData = Admin::all();
            foreach($adminData as $admin)
            {
                $admin->image_path =  $this->url->to('/storage/profile_pictures/' . $admin->image_path);
            }

            return DataTables::of($adminData)->make(true);
        } catch (AuthorizationException $e) {
            return  $e->getMessage();
        }

    }
    public function getRoles()
    {
        $roles = Role::select('id','role_name as role')->get();
        return response()->json([
            'data' => $roles
        ], 201);
    }
    public function saveAdmin(Request $request)
    {
        try {
            $this->authorizeForUser($this->user,'add', new Admin);
            $data =[
            "name"      => $request->get('name'),
            "email"     => $request->get('email'),
            "password"  => Hash::make($request->input('password')),
            "role_id"  => $request->get('role_id')
            ];
            $admin = Admin::create($data);
            $image = $request->get('profile_pic');
            $fileName = 'profile-' . time();

            $logo = base64_decode($image);
            $ext = (explode('/', finfo_buffer(finfo_open(), $logo, FILEINFO_MIME_TYPE))[1]);

            $imageName =  $fileName . '.' . $ext;
            Storage::disk('local')->put("/public/profile_pictures/" . $imageName, $logo);

            return response()->json([
                'success' => "Data Saved! "
            ], 201);
        } catch (AuthorizationException $e) {
            return  $e->getMessage();
        }
    }
    public function editAdmin($id)
    {
        $adminData = Admin::find($id);

            if($adminData->image_path)
            $adminData->image_path =   $this->url->to('/storage/profile_pictures/' . $adminData->image_path);
            else
            $adminData->image_path='';

        return response()->json([
            'data' => $adminData
        ], 201);
    }
    public function updateAdmin($id,Request $request)
    {
        $admin = Admin::find($id);
        $admin->name        = $request->get('name');
        // $admin->email       = $request->get('email');
        if($request->input('password'))
            $admin->password    = Hash::make($request->input('password'));
        $admin->role_id    = $request->get('role_id');

        if($request->get('profile_pic'))
        {
            $image = $request->get('profile_pic');
            $fileName = 'profile-' . time();

            $logo = base64_decode($image);
            $ext = (explode('/', finfo_buffer(finfo_open(), $logo, FILEINFO_MIME_TYPE))[1]);

            $imageName =  $fileName . '.' . $ext;
            Storage::disk('local')->put("/public/profile_pictures/" . $imageName, $logo);
            $admin->image_path = $imageName;
        }

        $admin->save();
        return response()->json([
            'data' => $admin
        ], 201);
    }
    public function detailAdmin($id)
    {

        $admin = Admin::find($id);
        $admin->image_path = $this->url->to('/storage/profile_pictures/' . $admin->image_path);
        return response()->json([
            'data' => $admin
        ], 201);
    }
}
