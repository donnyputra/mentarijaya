<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\User;

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
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function username() {
        return 'username';
    }

    public function loginAPI(Request $request)
    {
        $credentials = $request->only('username', 'password');
 
        if (Auth::attempt($credentials)) {
            // Authentication passed...
            $user = User::where("username", $request->username)->first();
            $tokenResult = $user->createToken("authToken")->plainTextToken;
            $role = 2;
            if($user->authRole()->name=="admin"){
                $role = 1;
            }
            return response()->json([
                "status_code" => 200,
                "access_token" => $tokenResult,
                "token_type" => "Bearer",
                "role" => $role,
            ]);
        } else {
            return response()->json([
                "status_code" => 400,
                "access_token" => "",
                "token_type" => "",
            ])->setStatusCode(400);
        }
    }
}
