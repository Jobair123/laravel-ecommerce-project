<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;



class AdminloginController extends Controller
{
    function adminlogin(){
        return view('admin.login');
    }
    public function authenticate(Request $request){
        $validator = Validator::make($request->all(),[
            'email' => 'required|email' ,
            'password' => 'required'
        ]);
        if($validator->passes()){
        if(Auth::guard('admin')->attempt(['email'=>$request->email,
         'password'=>$request->password],$request->get('remember'))){
            $admin = Auth::guard('admin')->user();
            if($admin->role ==2){
                 return redirect()->route('admin.deshboard');
            }
            else{
                Auth::guard('admin')->logout();
                return redirect()->route('admin.login')->with('error','You are not authorized to access the admin panel ');
            }
           
         }
         else{
            return redirect()->route('admin.login')->with('error','Either Email or Password is Incorrect! ');
         }
        }
        else{
          return redirect()->route('admin.login')
          ->withErrors($validator)
          ->withInput($request->only('email'));
        }
    }
}
