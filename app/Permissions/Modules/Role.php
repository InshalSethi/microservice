<?php
namespace App\Permissions\Modules;

use App\Model\UserTypes;
use GuzzleHttp\Psr7\Request;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx\Rels;

class Role extends BasePermissions{

    const ROLE = "ROLE_";
    const ROLE_ADD = self::ROLE."ADD";
    const ROLE_EDIT = self::ROLE."EDIT";
    const ROLE_DELETE = self::ROLE."DELETE";
    const ROLE_VIEW = self::ROLE."VIEW";
    const ROLE_PERMISSIONS = self::ROLE."PERMISSIONS";


    public function permissions()
    {
        $permissions =  [
            [
                "text"=>"Role View",
                "id"=>self::ROLE_VIEW,
                "state" => $this->selectNodes(self::ROLE_VIEW)
            ],
            [
                "text"=>"Role Add",
                "id"=>self::ROLE_ADD,
                "state" => $this->selectNodes(self::ROLE_ADD)
            ],
            [
                "text"=>"Role Edit",
                "id"=>self::ROLE_EDIT,
                "state" => $this->selectNodes(self::ROLE_EDIT)
            ],
            [
                "text"=>"Role Delete",
                "id"=>self::ROLE_DELETE,
                "state" => $this->selectNodes(self::ROLE_DELETE)
            ],
            [
                "text"=>"Role Permissions",
                "id"=>self::ROLE_PERMISSIONS,
                "state" => $this->selectNodes(self::ROLE_PERMISSIONS)
            ]


        ];
        return ["text"=>"Role","id"=>self::ROLE,"children"=>$permissions];
    }
    public function frontEndPermissions()
    {
        $roles_permissions =
        [
            [
                "text"=>"Role View",
                "ability" => $this->apiPermissionSelected(self::ROLE_VIEW)
            ],
            [
                "text"=>"Role Add",
                "ability" => $this->apiPermissionSelected(self::ROLE_ADD)
            ],
            [
                "text"=>"Role Edit",
                "ability" => $this->apiPermissionSelected(self::ROLE_EDIT)
            ],
            [
                "text"=>"Role Delete",
                "ability" => $this->apiPermissionSelected(self::ROLE_DELETE)
            ],
            [
                "text"=>"Role Permissions",
                "ability" => $this->apiPermissionSelected(self::ROLE_PERMISSIONS)
            ]
        ];
        return ["text"=>"Roles Permissions","permissions"=>$roles_permissions];
    }
}
