<?php

namespace App\Policies;

// use App\Models\Admin Admins;

use App\Permissions\Modules\Role;
use App\Permissions\Modules\Subscription;
use App\Permissions\Modules\AdminPermissions;
use Illuminate\Auth\Access\HandlesAuthorization;

class PermissionsPolicy
{
    use HandlesAuthorization;
    private $class_name='Permission';
    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }
    public function add($user)
    {
        return $user->role->havePermission(AdminPermissions::PERMISSION_ADD);

    }
    public function edit($user)
    {
        return $user->role->havePermission(AdminPermissions::PERMISSION_EDIT);

    }
    public function delete($user)
    {
        return $user->role->havePermission(AdminPermissions::PERMISSION_DELETE);
    }
    public function list($user)
    {
        return $user->role->havePermission(AdminPermissions::PERMISSION_VIEW);
    }
    public function permissions($user){
        return $user->role->havePermission(AdminPermissions::PERMISSION_UPDATE);
    }
}
