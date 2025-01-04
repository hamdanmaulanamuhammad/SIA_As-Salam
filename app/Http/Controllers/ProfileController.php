<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function showAdminProfile()
    {
        $user = Auth::user();
        return view('admin.profile-admin',compact('user'));
    }


    public function showPengajarProfile()
    {
        $user = Auth::user();
        return view('pengajar.profile-pengajar',compact('user'));
    }
}
