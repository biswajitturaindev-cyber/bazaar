<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Models\User;

use Carbon\Carbon;

class AuthController extends Controller
{
    //
    public function adminLoginForm()
    {
        return view('admin.auth.login');
    }

    public function adminLogin(Request $request)
    {
        // dd($request->all());
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        if (Auth::guard('admin')->attempt([
            'username' => $credentials['username'],
            'password' => $credentials['password'],
        ])) {
            $request->session()->regenerate();
            return redirect()->route('admin.dashboard');
        }

        return back()->withErrors([
            'username' => 'Invalid username or password.',
        ])->onlyInput('username');
    }

    public function adminLogout(Request $request)
    {

        Auth::guard('admin')->logout();
        Session::flush();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login.form');
    }
    
    public function change_password(Request $request)
    {
        return view('admin.change_password');
    }
    
    public function updateadminPassword(Request $request)
    {
    $request->validate([
        'new_password' => 'required|min:6',
        'confirm_password' => 'required|same:new_password'
    ]);

    $admin = auth()->guard('admin')->user();

    $admin->password = Hash::make($request->new_password);
    $admin->save();

    return response()->json([
        'success' => true,
        'message' => 'Password updated successfully'
    ]);
}




public function sendPasswordOtp(Request $request)
{
    $admin = auth()->guard('admin')->user();

    if(!$admin){
        return response()->json([
            'success'=>false,
            'message'=>'Admin not authenticated'
        ]);
    }

    $otp = rand(100000,999999);

    Session::put('password_otp', $otp);
    Session::put('password_otp_time', now());
    Session::put('new_password', $request->new_password);

    Mail::raw("Your OTP for password change is: ".$otp, function($message) use ($admin){
        $message->to($admin->email)
        ->subject('Password Change OTP');
    });

    return response()->json([
        'success'=>true,
        'message'=>'OTP sent to your email'
    ]);
}


public function verifyPasswordOtp(Request $request)
{
    $otp = Session::get('password_otp');
    $otpTime = Session::get('password_otp_time');

    if(!$otp || !$otpTime){
        return response()->json([
            'success'=>false,
            'message'=>'OTP expired. Please request again.'
        ]);
    }

    // OTP expiry check (5 minutes)
    if(now()->diffInMinutes($otpTime) > 5){
        Session::forget(['password_otp','password_otp_time','new_password']);

        return response()->json([
            'success'=>false,
            'message'=>'OTP expired. Please resend OTP.'
        ]);
    }

    if($request->otp != $otp){
        return response()->json([
            'success'=>false,
            'message'=>'Invalid OTP'
        ]);
    }

    $admin = auth()->guard('admin')->user();

    $admin->password = Hash::make(Session::get('new_password'));
    $admin->save();

    Session::forget(['password_otp','password_otp_time','new_password']);

    return response()->json([
        'success'=>true,
        'message'=>'Password updated successfully'
    ]);
}

    
}
