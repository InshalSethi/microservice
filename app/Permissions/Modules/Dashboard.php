<?php
namespace App\Permissions\Modules;

use App\Model\UserTypes;

class Dashboard extends BasePermissions{

    const DASHBOARD = "DASHBOARD_";
    const DASHBOARD_FILTERS = self::DASHBOARD."FILTERS";
    const DASHBOARD_DATA = self::DASHBOARD."DATA";


    public function permissions()
    {
        $permissions =  [
            [
                "text"=>"Dashboard Filters",
                "id"=>self::DASHBOARD_FILTERS,
                "state" => $this->selectNodes(self::DASHBOARD_FILTERS)
            ],

            [
                "text"=>"Dashboard Data",
                "id"=>self::DASHBOARD_DATA,
                "state" => $this->selectNodes(self::DASHBOARD_DATA)
            ]

        ];
        return ["text"=>"Dashboard","id"=>self::DASHBOARD,"children"=>$permissions];
    }
    public function frontEndPermissions()
    {
        $dashboard_permissions = [
            [
                "text"=>"Dashboard Filters",
                "ability"=>$this->apiPermissionSelected(self::DASHBOARD_FILTERS)
            ],
            [
                "text"=>"Dashboard Data",
                "ability"=>$this->apiPermissionSelected(self::DASHBOARD_DATA)
            ]
        ];

        return ["text"=>"Dashboard Permissions","permissions"=>$dashboard_permissions];
    }
}
