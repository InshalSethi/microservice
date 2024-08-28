<?php

namespace App\Policies;

use App\Permissions\Modules\Admin;
use App\Permissions\Modules\Dashboard;
// use App\Models\Admin Admins;
use Illuminate\Auth\Access\HandlesAuthorization;

class DashboardPolicy
{
    use HandlesAuthorization;
    private $class_name='Dashboard';
    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }
    public function data($user)
    {
        return $user->role->havePermission(Dashboard::DASHBOARD_DATA);

    }
    public function filters($user)
    {
        return $user->role->havePermission(Dashboard::DASHBOARD_FILTERS);

    }
}
