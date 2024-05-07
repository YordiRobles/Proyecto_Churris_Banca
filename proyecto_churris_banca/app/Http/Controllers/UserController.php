<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use App\Models\User;


class UserController extends Controller
{
    // Esta funcion permite la busqueda de los usuarios dentro de la aplicacion.
    public function searchUsers(Request $request): View
    {
        $users = User::where('name', 'like', '%'.$request->input('query').'%')->get();
        return view('search_result', ['users' => $users]);
    }

    // Esta funcion despliega la informacion de un usuario especifico de la busqueda.
    public function show($name): View {
        //$user = User::findOrFail($id);
        $user = User::where('name', $name)->first(); 
        return view('show_user', ['user' => $user]);
    }
    
}