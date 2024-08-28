<?php
namespace App\Permissions\Modules;

use App\Model\UserTypes;

class Products extends BasePermissions{

    const PRODUCT = "PRODUCT_";
    const PRODUCT_ADD = self::PRODUCT."ADD";
    const PRODUCT_EDIT = self::PRODUCT."EDIT";
    const PRODUCT_DELETE = self::PRODUCT."DELETE";
    const PRODUCT_VIEW = self::PRODUCT."VIEW";


    public function permissions()
    {
        $permissions =  [
            [
                "text"=>"Product View",
                "id"=>self::PRODUCT_VIEW,
                "state" => $this->selectNodes(self::PRODUCT_VIEW)
            ],
            [
                "text"=>"Product Add",
                "id"=>self::PRODUCT_ADD,
                "state" => $this->selectNodes(self::PRODUCT_ADD)
            ],
            [
                "text"=>"Product Edit",
                "id"=>self::PRODUCT_EDIT,
                "state" => $this->selectNodes(self::PRODUCT_EDIT)
            ],
            [
                "text"=>"Product Delete",
                "id"=>self::PRODUCT_DELETE,
                "state" => $this->selectNodes(self::PRODUCT_DELETE)
            ]
        ];
        return ["text"=>"Product","id"=>self::PRODUCT,"children"=>$permissions];
    }
    public function frontEndPermissions()
    {
        $products_permissions =
        [
            [
                "text"=>"Product View",
                "ability" => $this->apiPermissionSelected(self::PRODUCT_VIEW)
            ],
            [
                "text"=>"Product Add",
                "ability" => $this->apiPermissionSelected(self::PRODUCT_ADD)
            ],
            [
                "text"=>"Product Edit",
                "ability" => $this->apiPermissionSelected(self::PRODUCT_EDIT)
            ],
            [
                "text"=>"Product Delete",
                "ability" => $this->apiPermissionSelected(self::PRODUCT_DELETE)
            ]
        ];

        return ["text"=>"Products Permissions","permissions"=>$products_permissions];
    }
}
