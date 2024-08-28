<?php

namespace App\Policies;

use App\Permissions\Modules\Admin;
use App\Permissions\Modules\Dashboard;
// use App\Models\Admin Admins;
use Illuminate\Auth\Access\HandlesAuthorization;

class AdminPolicy
{
    use HandlesAuthorization;
    private $class_name='Admin';
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
        return $user->role->havePermission(Admin::ADMIN_ADD);

    }
    public function edit($user)
    {
        return $user->role->havePermission(Admin::ADMIN_EDIT);

    }
    public function delete($user)
    {
        return $user->role->havePermission(Admin::ADMIN_DELETE);
    }
    public function list($user)
    {
        return $user->role->havePermission(Admin::ADMIN_VIEW);
    }
    public function DashboardData($user)
    {
        return $user->role->havePermission(Dashboard::DASHBOARD_DATA);

    }
    public function DashboardFilters($user)
    {
        return $user->role->havePermission(Dashboard::DASHBOARD_FILTERS);
    }
}
