<?php

namespace App\Http\Controllers\admin\Auth;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/admin';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function showLoginForm()
    {
        return view('admin.auth.login');
    }

    protected function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        $user = Admin::where($this->username(), $credentials['email'])->first();

        if ($user && Hash::check($credentials['password'], $user->password)) {
            Auth::guard('admin')->login($user, $request->has('remember'));
            return redirect($this->redirectTo);
        }

        return back()->withErrors(['email' => 'Invalid credentials']);
    }

    public function redirectPath() {
        return "/admin";
    }
}
