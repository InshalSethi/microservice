<?php

namespace App\Http\Controllers\Admin\Api;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Access\AuthorizationException;
class SiteSettingsController extends Controller
{
    protected $url;
    protected $user;
    public function __construct(UrlGenerator $url)
    {
        $this->url = $url;
        $this->user = Auth::guard('admin')->user();
    }
    public function getSiteSettings(Request $request)
    {
        try{
            $this->authorizeForUser($this->user,'list', new Setting);
            $settings = Setting::first();
            $favicon = $this->url->to("/" . $settings->favicon);
            $logo = $this->url->to("/assets/logo/" . $settings->logo);
            $settings['favicon'] = $favicon;
            $settings['logo'] = $logo;
            return response()->json([
                'settings_data' => $settings
            ]);
        } catch (AuthorizationException $e) {
            return  $e->getMessage();
        }
    }
    public function updateSettings($id, Request $request)
    {
        try{
            $this->authorizeForUser($this->user,'edit', new Setting);
            $settings = Setting::find($id);
            //upload website logo
            $fileName = 'logo-' . time();
            $logoPath = public_path() . '/assets/logo/';
            $imageName = $this->uploadImage($request->get('logo'), $fileName, $logoPath);
            ////////////////
            //upload favicon
            $fileName = 'favicon-' . time();
            $faviconPath =  public_path('/');
            $faviconImageName = $this->uploadImage($request->get('favicon'), $fileName, $faviconPath);
            /////////////////////////////
            $settings->plan_main_title = $request->get('plan_main_title');
            $settings->plan_description = $request->get('plan_description');
            $settings->plan_description_pargraph = $request->get('plan_description_pargraph');
            if ($request->get('logo'))
                $settings->logo = $imageName;
            $settings->footer_text = $request->get('footer_text');
            if ($request->get('favicon'))
                $settings->favicon = $faviconImageName;
            $settings->header_title = $request->get('header_title');
            $settings->domain_name = $request->get('domain_name');
            $settings->save();
            return response()->json([
                'msg' => "Settings updated "
            ]);
        } catch (AuthorizationException $e) {
            return  $e->getMessage();
        }
    }
    public function uploadImage($image, $fileName, $path)
    {
        try{
            $this->authorizeForUser($this->user,'edit', new Setting);
            $logo = base64_decode($image);
            $filename = (explode('/', finfo_buffer(finfo_open(), $logo, FILEINFO_MIME_TYPE))[0]);
            $ext = (explode('/', finfo_buffer(finfo_open(), $logo, FILEINFO_MIME_TYPE))[1]);
            $imageName =  $fileName . '.' . $ext;
            $path =  $path . $imageName;
            file_put_contents($path, $logo);
            return $imageName;
        } catch (AuthorizationException $e) {
            return  $e->getMessage();
        }
    }
    public function passwordReset(Request $request)
    {
        $userId = Auth::guard('admin')->user()->id;
        $user = Admin::find($userId);
        if (Hash::check($request->get('password'), $user->password)) {
            $password = $request->get('new_password');
            $user->password = bcrypt($password);
            $user->save();
            return response()->json([
                'msg' => "Password updated"
            ]);
        } else {
            return response()->json([
                'msg' => "Password does not match",
                'status' => 'error'
            ]);
        }
    }
}
