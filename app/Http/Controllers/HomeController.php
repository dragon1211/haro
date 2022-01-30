<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\User;
use App\Models\Policy;

class HomeController extends Controller
{
    public function index()
    {
        return view('dashboard');
    }

    /**
     * Login users
     */
    public function do_login(Request $request)
    {
        $email = $request->input('email');
        $password = $request->input('password');
        if (Auth::attempt(['email' => $email, 'password' => $password])) {
            // Authentication passed...
            return redirect()->intended('/dashboard');
        } else
            return redirect('/login');
    }

    /**
     * Sign up users.
     */
    public function do_signup(Request $request)
    {
        $name = $request->input('fullname');
        $email = $request->input('email');
        $password = $request->input('password');
        $account = User::create([
            'name' => $name,
            'email' => $email,
            'password' => bcrypt($password)
        ]);
        $account->save();
        return redirect('/login');
    }

    public function logout()
    {
        Auth::logout();
        return redirect('/');
    }

    public function terms_of_use()
    {
        $policy = Policy::first();
        return $policy->policy;
    }
}
