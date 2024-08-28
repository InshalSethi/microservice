<?php
namespace App\Http\Controllers\Admin\Api;
use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Permissions\Permissions;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Support\Facades\Auth;
class PermissionsController extends Controller
{
    protected $url;
    protected $user;
    public function __construct(UrlGenerator $url)
    {
        $this->url = $url;
        $this->user = Auth::guard('admin')->user();
    }

    public function index()
    {
        try{
            $this->authorizeForUser($this->user,'list', new Role);
            $roleList = Role::all();
            return Datatables::of($roleList)
            ->toJson();
        } catch (AuthorizationException $e) {
            return  $e->getMessage();
        }

    }
    public function storeRole(Request $request)
    {
        try{
            $this->authorizeForUser($this->user,'add', new Role);
            Role::create(
                [
                    'role_name' => $request->input('role_name'),
                    'role_guard' => $request->input('role_guard')
                ]
            );
            return response()->json([
                'success' => 'Data Saved!'
            ], 201);
        } catch (AuthorizationException $e) {
            return  $e->getMessage();
        }
    }
    public function editRoles($id)
    {
        try{
            $this->authorizeForUser($this->user,'edit', new Role);
            return response()->json([
                'data' => Role::find($id)
            ], 201);
        } catch (AuthorizationException $e) {
            return  $e->getMessage();
        }
    }
    public function updateRoles($id,Request $request)
    {
        try{
            $this->authorizeForUser($this->user,'edit', new Role);
            $permission = Role::find($id);
            $permission->role_name = $request->input('role_name');
            $permission->role_guard = $request->input('role_guard');
            $permission->save();
            return response()->json([
                'data' => Role::find($id)
            ], 201);
        } catch (AuthorizationException $e) {
            return  $e->getMessage();
        }
    }
    public function deleteRoles($id){
        try{
            $this->authorizeForUser($this->user,'delete', new Role);
            Role::find($id)->delete();
            return response()->json([
                'success' => 'role Deleted Successfully !'
            ], 201);
        } catch (AuthorizationException $e) {
            return  $e->getMessage();
        }
    }
    public function updatePermissions($id,Request $request)
    {
        $permission = Role::find($id);
    }
    public function getPermissions($id)
    {
        try{
            $this->authorizeForUser($this->user,'list', new Role);
            $role = Role::find($id);
            $rolePermissions = $role->permissions;
            $permissionManager = new  Permissions($rolePermissions);
            $permissions = $permissionManager->getPermissions();
            // $permissions = $permissionManager->permissionsApi();
            return response()->json([
                'data' => $permissions
            ], 201);
        } catch (AuthorizationException $e) {
            return  $e->getMessage();
        }
    }
    public function savePermissions($id,Request $request)
    {
        try{
            $this->authorizeForUser($this->user,'edit', new Role);
        // $permissions = 'DASHBOARD_,DASHBOARD_FILTERS,DASHBOARD_DATA,PRODUCT_,PRODUCT_VIEW,PRODUCT_ADD,PRODUCT_EDIT,PRODUCT_DELETE,PROVIDER_,PROVIDER_VIEW,PROVIDER_ADD,PROVIDER_EDIT,PROVIDER_DELETE,ADMIN_,ADMIN_VIEW,ADMIN_ADD,ADMIN_EDIT,ADMIN_DELETE,ADMIN_SITE_SETTINGS,ADMIN_SECURITY';
            $permissions = $request->input('permisssions');
            $permissionsArray = explode(',',$permissions);

            $permissionsData = Role::find($id);
            $permissionsData->permissions = $permissionsArray;
            $permissionsData->save();
            return response()->json([
                'success' => "permissions saved !"
            ], 201);
        } catch (AuthorizationException $e) {
            return  $e->getMessage();
        }
    }
}
