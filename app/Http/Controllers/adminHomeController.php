<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class adminHomeController extends Controller
{
   public function index(){
    return view('admin.deshboard');
   }
   public function logout(){
      Auth::guard('admin')->logout();
      return redirect()->route('admin.login');
  }

}
