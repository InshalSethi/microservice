<?php

namespace App\Policies;

// use App\Models\Admin Admins;

use App\Permissions\Modules\Role;
use Illuminate\Auth\Access\HandlesAuthorization;

class RolePolicy
{
    use HandlesAuthorization;
    private $class_name='Role';
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
        return $user->role->havePermission(Role::ROLE_ADD);

    }
    public function edit($user)
    {
        return $user->role->havePermission(Role::ROLE_EDIT);

    }
    public function delete($user)
    {
        return $user->role->havePermission(Role::ROLE_DELETE);
    }
    public function list($user)
    {
        return $user->role->havePermission(Role::ROLE_VIEW);
    }
}
