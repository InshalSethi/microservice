<?php
namespace App\Permissions;

use App\Permissions\Modules\Dashboard;
use App\Permissions\Modules\Products;
use App\Permissions\Modules\Admin;
use App\Permissions\Modules\Role;
use App\Permissions\Modules\AdminPermissions;
use App\Permissions\Modules\SiteSettings;

class Permissions
{
    private $modules=[
        Dashboard::class,
        Products::class,
        Admin::class,
        Role::class,
        AdminPermissions::class,
        SiteSettings::class,
    ];
    public $role_permissions;
    public function __construct($role_permissions)
    {
        $this->role_permissions = $role_permissions;
    }
    public function getPermissions()
    {
        $permissions = [];
        foreach($this->modules as $module_class)
        {
            $module = new $module_class($this->role_permissions);
            $permissions[] = $module->permissions();
        }
        return [
            "text"=>"Permissions",
            "a_attr"=> [
                "class"=>"no_checkbox"
            ],
            "state"=>["opened"=>true],
            "children"=>$permissions
        ];
    }
    public function permissionsApi()
    {
        $permissions = [];
        foreach($this->modules as $module_class)
        {
            $module = new $module_class($this->role_permissions);
            $permissions[] = $module->frontEndPermissions();
        }
        return $permissions;
    }
}
