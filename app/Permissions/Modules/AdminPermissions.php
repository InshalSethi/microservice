<?php
namespace App\Permissions\Modules;

use App\Model\UserTypes;

class AdminPermissions extends BasePermissions{

    const PERMISSION = "PERMISSION_";
    const PERMISSION_ADD = self::PERMISSION."ADD";
    const PERMISSION_EDIT = self::PERMISSION."EDIT";
    const PERMISSION_DELETE = self::PERMISSION."DELETE";
    const PERMISSION_VIEW = self::PERMISSION."VIEW";
    const PERMISSION_UPDATE = self::PERMISSION."UPDATE";


    public function permissions()
    {
        $permissions =  [
            [
                "text"=>"Permissions View",
                "id"=>self::PERMISSION_VIEW,
                "state" => $this->selectNodes(self::PERMISSION_VIEW)
            ],
            [
                "text"=>"Permissions Add",
                "id"=>self::PERMISSION_ADD,
                "state" => $this->selectNodes(self::PERMISSION_ADD)
            ],
            [
                "text"=>"Permissions Edit",
                "id"=>self::PERMISSION_EDIT,
                "state" => $this->selectNodes(self::PERMISSION_EDIT)
            ],
            [
                "text"=>"Permissions Delete",
                "id"=>self::PERMISSION_DELETE,
                "state" => $this->selectNodes(self::PERMISSION_DELETE)
            ],
            [
                "text"=>"Permissions Update",
                "id"=>self::PERMISSION_UPDATE,
                "state" => $this->selectNodes(self::PERMISSION_UPDATE)
            ]
        ];
        return ["text"=>"Permissions","id"=>self::PERMISSION,"children"=>$permissions];
    }
    public function frontEndPermissions()
    {
        $permissions_permissions =
        [
            [
                "text"=>"Permissions View",
                "ability" => $this->apiPermissionSelected(self::PERMISSION_VIEW)
            ],
            [
                "text"=>"Permissions Add",
                "ability" => $this->apiPermissionSelected(self::PERMISSION_ADD)
            ],
            [
                "text"=>"Permissions Edit",
                "ability" => $this->apiPermissionSelected(self::PERMISSION_EDIT)
            ],
            [
                "text"=>"Permissions Delete",
                "ability" => $this->apiPermissionSelected(self::PERMISSION_DELETE)
            ],
            [
                "text"=>"Permissions Update",
                "ability" => $this->apiPermissionSelected(self::PERMISSION_UPDATE)
            ]
        ];
        return ["text"=>"Permissionss","permissions"=>$permissions_permissions];
    }
}
