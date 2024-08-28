<?php

namespace App\Http\Controllers\Admin\Api;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Auth\Access\AuthorizationException;

class HomeController extends Controller
{
    protected $url;
    protected $user;
    public function __construct(UrlGenerator $url)
    {
        $this->url = $url;
        $this->user = Auth::guard('admin')->user();
    }
    public function updateAdminProfile(Admin $admin, Request $request)
    {
        try {
            $this->authorizeForUser($this->user, 'edit', new Admin);
            $admin->update($request->all());
            return response()->json([
                'message' => 'Admin updated successfully',
                'telemed' => $admin
            ]);
        } catch (AuthorizationException $e) {
            return  $e->getMessage();
        }
    }
}
