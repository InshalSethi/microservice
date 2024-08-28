<?php

namespace App\Policies;

// use App\Models\Admin Admins;

use App\Permissions\Modules\Role;
use App\Permissions\Modules\SiteSettings;
use Illuminate\Auth\Access\HandlesAuthorization;

class SiteSettingsPolicy
{
    use HandlesAuthorization;
    private $class_name='Setting';
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
        return $user->role->havePermission(SiteSettings::SITE_SETTINGS_ADD);
    }
    public function edit($user)
    {
        return $user->role->havePermission(SiteSettings::SITE_SETTINGS_EDIT);
    }
    public function delete($user)
    {
        return $user->role->havePermission(SiteSettings::SITE_SETTINGS_DELETE);
    }
    public function list($user)
    {
        return $user->role->havePermission(SiteSettings::SITE_SETTINGS_VIEW);
    }
}
