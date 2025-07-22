<?php
namespace App\Http\Controllers\Auth;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Str;

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
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    // public function __construct()
    // {
    //     $this->middleware('guest')->except('logout');
    //     $this->middleware('auth')->only('logout');
    // }

    public function showLoginForm()
    {
        $user = auth()->user();
        if ($user != null) {
            if ($user->role === UserRole::ADMIN) {
                return redirect()->route('admin.dashboard')->with('success', 'Selamat Datang Administrator');
            } else if ($user->role === UserRole::EMPLOYEE) {
                return redirect()->route('employee.dashboard')->with('success', 'Selamat Datang '  . $user->name);
            } else {
                return redirect()->route('welcome');
            }
        } else {
            return view('auth.login');
        }
    }

    public function login(Request $request)
    {
        $input = $request->all();

        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (auth()->attempt(array('email' => $input['email'], 'password' => $input['password']))) {
            $user = auth()->user();
            if ($user->role === UserRole::ADMIN) {
                return redirect()->route('admin.dashboard')->with('success', 'Selamat Datang Administrator');
            } else if ($user->role === UserRole::EMPLOYEE) {
                return redirect()->route('employee.dashboard')->with('success', 'Selamat Datang '  . $user->name);
            } else {
                return redirect()->route('welcome');
            }
        } else {
            return redirect()->route('login')
                ->with('error', 'Email-Address And Password Are Wrong.');
        }
    }
}
