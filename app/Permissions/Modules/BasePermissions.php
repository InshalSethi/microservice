<?php
namespace App\Permissions\Modules;

use App\Classes\Permissions;
class BasePermissions{

    protected $role_permissions;
    public function __construct($role_permissions)
    {
        $this->role_permissions = $role_permissions;
    }
    public function selectNodes($id)
    {
        $select_nodes = [];
        if($this->role_permissions && in_array($id,$this->role_permissions))
            return ["selected"=>true];
        return $select_nodes;
    }
    public function apiPermissionSelected($id)
    {
        $permissions = [];
        if($this->role_permissions && in_array($id,$this->role_permissions))
            return true;
        return $permissions;

    }
}
