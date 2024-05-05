<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use App\Models\User;


class UserController extends Controller
{
    public function searchUsers(Request $request): View
    {
        $users = User::where('name', 'like', '%'.$request->input('query').'%')->get();
        return view('search_result', ['users' => $users]);
    }
}