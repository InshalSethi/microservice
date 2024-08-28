<?php
namespace App\Permissions\Modules;

use App\Model\UserTypes;
use GuzzleHttp\Psr7\Request;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx\Rels;

class SiteSettings extends BasePermissions{

    const SITE_SETTINGS = "SITE_SETTINGS_";
    const SITE_SETTINGS_ADD = self::SITE_SETTINGS."ADD";
    const SITE_SETTINGS_EDIT = self::SITE_SETTINGS."EDIT";
    const SITE_SETTINGS_DELETE = self::SITE_SETTINGS."DELETE";
    const SITE_SETTINGS_VIEW = self::SITE_SETTINGS."VIEW";

    public function permissions()
    {
        $permissions =  [
            [
                "text"=>"Site Settings View",
                "id"=>self::SITE_SETTINGS_VIEW,
                "state" => $this->selectNodes(self::SITE_SETTINGS_VIEW)
            ],
            [
                "text"=>"Site Settings Add",
                "id"=>self::SITE_SETTINGS_ADD,
                "state" => $this->selectNodes(self::SITE_SETTINGS_ADD)
            ],
            [
                "text"=>"Site Settings Edit",
                "id"=>self::SITE_SETTINGS_EDIT,
                "state" => $this->selectNodes(self::SITE_SETTINGS_EDIT)
            ],
            [
                "text"=>"Site Settings Delete",
                "id"=>self::SITE_SETTINGS_DELETE,
                "state" => $this->selectNodes(self::SITE_SETTINGS_DELETE)
            ]
        ];
        return ["text"=>"Site Settings","id"=>self::SITE_SETTINGS,"children"=>$permissions];
    }
    public function frontEndPermissions()
    {
        $site_settings_permissions =
        [
            [
                "text"=>"Site Settings View",
                "ability" => $this->apiPermissionSelected(self::SITE_SETTINGS_VIEW)
            ],
            [
                "text"=>"Site Settings Add",
                "ability" => $this->apiPermissionSelected(self::SITE_SETTINGS_ADD)
            ],
            [
                "text"=>"Site Settings Edit",
                "ability" => $this->apiPermissionSelected(self::SITE_SETTINGS_EDIT)
            ],
            [
                "text"=>"Site Settings Delete",
                "ability" => $this->apiPermissionSelected(self::SITE_SETTINGS_DELETE)
            ]
        ];
        return ["text"=>"Site Settingss Permissions","permissions"=>$site_settings_permissions];
    }
}
