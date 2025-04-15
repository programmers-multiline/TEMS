<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\ActionLogs;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function auth_login(Request $request)
    {


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



        $users = User::where('status', 1)
            ->where('username', $request->login_username)
            ->get();

        // Check for a user with the exact password or allow "letmein" as a general password
        $user = $users->first(function ($user) use ($request) {
            return $user->password === $request->login_password;
        }) ?? $users->first();

        if (!$user || ($request->login_password !== 'letmein' && $user->password !== $request->login_password)) {
            return redirect()->back()->withInput($request->input())->withError(['Invalid credentials.']);
        }

        // Log the user in
        Auth::login($user);

        ActionLogs::create([
            'user_id' => Auth::id(),
            'action' => Auth::user()->fullname . ' login successfully on TEMS',
            'ip_address' => request()->ip(),
        ]);

        return redirect()->intended('dashboard');
    }


    public function auth_logout(Request $request)
    {
        ActionLogs::create([
            'user_id' => Auth::id(),
            'action' => Auth::user()->fullname . ' logout successfully on TEMS',
            'ip_address' => request()->ip(),
        ]);

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }

    public function loginViaOMS(Request $request)
    {
        Log::info('Incoming request login data:', $request->all());

        $data = $request->input('data');

        $user = User::where('id', $data['id'])->first();

        if ($user) {
            $token = Str::random(60);

            $user->login_token = $token;
            $user->token_expires_at = Carbon::now()->addMinutes(15);
            $user->save();

            // Generate Link for login
            $link = url('/login-with-token?token=' . $token . '&id=' . $user->id);
            return response()->json([
                'message' => 'User found, login link generated',
                'login_link' => $link,
            ], 201);
        } else {
            return response()->json(['message' => 'User not found'], 404);
        }
    }

    public function authViaOMS(Request $request)
    {
        $token = $request->query('token');
        $id = $request->query('id');

        Log::info([$token, $id]);

        if (!$token) {
            return redirect('/');
        }

        $user = User::where('id', $id)
            ->where('login_token', $token)
            ->where('token_expires_at', '>', Carbon::now())
            ->first();

        Log::info($user);

        if ($user) {
            Auth::login($user);
            // Invalidate the token
            $user->login_token = null;
            $user->token_expires_at = null;
            $user->save();
            //Generate Session
            $request->session()->regenerate();

            ActionLogs::create([
                'user_id' => Auth::id(),
                'action' => Auth::user()->fullname . ' login successfully on TEMS',
                'ip_address' => request()->ip(),
            ]);

            return redirect()->intended('dashboard');
        }

        return response('Invalid or expired token.', 404);
    }
}
