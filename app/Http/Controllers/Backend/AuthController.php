<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\AuthRequest as RequestsAuthRequest;
use Illuminate\Http\Request;
use App\Http\Requests\AuthRequest;
use Illuminate\Support\Facades\Auth;
use SebastianBergmann\CodeCoverage\Report\Html\Dashboard;


class AuthController extends Controller {
    public function __construct(){
        
    }
    public function index(){
        if(Auth::id()>0){
            return redirect()->route('dashboard.index');
        }
        return  view('backend.auth.login');
    }

    public function login(AuthRequest $request){
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);
        
        if (Auth::attempt($credentials)) {
            return redirect()->route('dashboard.index')->with('success','Đăng nhập thành công');
        }
        return redirect()->route('auth.admin')->with('error', 'Email hoặc mật khẩu không chính xác');
    }

    public function logout(Request $request){
        Auth::logout();
        $request ->session()->invalidate();
        $request ->session()->regenerateToken();
        return redirect()->route('auth.admin');
    }

}