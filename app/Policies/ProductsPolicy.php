<?php

namespace App\Policies;

use App\Permissions\Modules\Products;
// use App\Models\Admin Admins;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProductsPolicy
{
    use HandlesAuthorization;
    private $class_name='Product';
    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }
    public function add($user)
    {
        return $user->role->havePermission(Products::PRODUCT_ADD);

    }
    public function edit($user)
    {
        return $user->role->havePermission(Products::PRODUCT_EDIT);

    }
    public function delete($user)
    {
        return $user->role->havePermission(Products::PRODUCT_DELETE);
    }
    public function list($user)
    {
        return $user->role->havePermission(Products::PRODUCT_VIEW);
    }
}
