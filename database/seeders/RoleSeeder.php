<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {$permissionsArray = explode(',',"DASHBOARD_FILTERS,DASHBOARD_DATA,PRODUCT_VIEW,PRODUCT_ADD,PRODUCT_EDIT,PRODUCT_DELETE,ADMIN_VIEW,ADMIN_ADD,ADMIN_EDIT,ADMIN_DELETE,ADMIN_SITE_SETTINGS,ADMIN_SECURITY,ADMIN_PROFILE_UPDATE,ROLE_VIEW,ROLE_ADD,ROLE_EDIT,ROLE_DELETE,ROLE_PERMISSIONS,PERMISSION_VIEW,PERMISSION_ADD,PERMISSION_EDIT,PERMISSION_DELETE,PERMISSION_UPDATE,SITE_SETTINGS_VIEW,SITE_SETTINGS_ADD,SITE_SETTINGS_EDIT,SITE_SETTINGS_DELETE");
        Role::create([
            'role_name' => 'admin',
            'role_guard' => 'admin',
            'permissions' => $permissionsArray
        ]);
    }
}
