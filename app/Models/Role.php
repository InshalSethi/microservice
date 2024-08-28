<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $table = "roles";
    protected $fillable = [
        'role_name',
        'role_guard',
        'permissions'
    ];
    protected $casts = [
        'permissions' => 'array',
    ];
    public function havePermission($name)
    {
        $permissions = $this->permissions;
        if(!$permissions)
             return false;
        if(in_array($name,$permissions))
        {
            return true;
        }
        else
            {
                foreach($permissions as $permission){
                    if(strpos($permission,$name)===0)
                        return true;
                }
            }
        return false;
    }
}
