<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class AuthController extends Controller {
 public function login(){return view('login');}
 public function authenticate(Request $r){$d=$r->validate(['email'=>'required|email','password'=>'required|string']);if(!Auth::attempt($d))return back()->withErrors(['email'=>'Неверные данные входа'])->onlyInput('email');$r->session()->regenerate();return redirect()->intended('/admin/orders');}
 public function logout(Request $r){Auth::logout();$r->session()->invalidate();$r->session()->regenerateToken();return redirect('/');}
}
