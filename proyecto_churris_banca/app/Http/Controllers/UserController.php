<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use App\Models\User;


class UserController extends Controller
{
    // Esta funcion permite la busqueda de los usuarios dentro de la aplicacion.
    public function searchUsers(Request $request)
    {
        $currentUser = Auth::user();
        $users = User::where('name', 'like', '%'.$request->input('query').'%')->get();
        
        $filteredUsers = $users->filter(function ($user) use ($currentUser) {
            return $user->id !== $currentUser->id;
        });
    
        return view('search_result', ['users' => $filteredUsers]);
    }

    // Esta funcion despliega la informacion de un usuario especifico de la busqueda.
    public function show($name)
    {
        //$user = User::findOrFail($id);
        $currentUser = Auth::user();
        $user = User::where('name', $name)->first(); 
    
        if ($user && $user->id !== $currentUser->id) {
            return view('show_user', ['user' => $user]);
        } else {
            //return view('dashboard');
            return Redirect::route('dashboard');
        }
    }
    
}