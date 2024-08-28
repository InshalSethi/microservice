<?php
namespace App\Permissions\Modules;

use App\Model\UserTypes;

class Admin extends BasePermissions{

    const ADMIN = "ADMIN_";
    const ADMIN_ADD = self::ADMIN."ADD";
    const ADMIN_EDIT = self::ADMIN."EDIT";
    const ADMIN_DELETE = self::ADMIN."DELETE";
    const ADMIN_VIEW = self::ADMIN."VIEW";
    const ADMIN_SITE_SETTINGS = self::ADMIN."SITE_SETTINGS";
    const ADMIN_SECURITY = self::ADMIN."SECURITY";
    const ADMIN_PROFILE_UPDATE = self::ADMIN."PROFILE_UPDATE";


    public function permissions()
    {
        $permissions =  [
            [
                "text"=>"Admin View",
                "id"=>self::ADMIN_VIEW,
                "state" => $this->selectNodes(self::ADMIN_VIEW)
            ],
            [
                "text"=>"Admin Add",
                "id"=>self::ADMIN_ADD,
                "state" => $this->selectNodes(self::ADMIN_ADD)
            ],
            [
                "text"=>"Admin Edit",
                "id"=>self::ADMIN_EDIT,
                "state" => $this->selectNodes(self::ADMIN_EDIT)
            ],
            [
                "text"=>"Admin Delete",
                "id"=>self::ADMIN_DELETE,
                "state" => $this->selectNodes(self::ADMIN_DELETE)
            ],
            [
                "text"=>"Site Settings",
                "id"=>self::ADMIN_SITE_SETTINGS,
                "state" => $this->selectNodes(self::ADMIN_SITE_SETTINGS)
            ],
            [
                "text"=>"Security",
                "id"=>self::ADMIN_SECURITY,
                "state" => $this->selectNodes(self::ADMIN_SECURITY)
            ],
            [
                "text"=>"Profile Update",
                "id"=>self::ADMIN_PROFILE_UPDATE,
                "state" => $this->selectNodes(self::ADMIN_PROFILE_UPDATE)
            ]

        ];
        return ["text"=>"Admin","id"=>self::ADMIN,"children"=>$permissions];
    }
    public function frontEndPermissions()
    {
        $admin_permissions =
        [
            [
                "text"=>"Admin View",
                "ability" => $this->apiPermissionSelected(self::ADMIN_VIEW)
            ],
            [
                "text"=>"Admin Add",
                "ability" => $this->apiPermissionSelected(self::ADMIN_ADD)
            ],
            [
                "text"=>"Admin Edit",
                "ability" => $this->apiPermissionSelected(self::ADMIN_EDIT)
            ],
            [
                "text"=>"Admin Delete",
                "ability" => $this->apiPermissionSelected(self::ADMIN_DELETE)
            ],
            [
                "text"=>"Site Settings",
                "ability" => $this->apiPermissionSelected(self::ADMIN_SITE_SETTINGS)
            ],
            [
                "text"=>"Security",
                "ability" => $this->apiPermissionSelected(self::ADMIN_SECURITY)
            ],
            [
                "text"=>"Profile Update",
                "ability" => $this->apiPermissionSelected(self::ADMIN_PROFILE_UPDATE)
            ]

        ];

        return ["text"=>"Admins Permissions","permissions"=>$admin_permissions];
    }
}
