<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use App\Models\User;

class SeeProfileController extends Controller
{
    public function show($id)
    {
        // Obtener el usuario por su ID
        $user = User::findOrFail($id);
        
        // Contar los seguidores del usuario
        $followersCount = $user->followers()->count();
        
        // Retornar la vista con los datos del usuario y el número de seguidores
        return view('seeprofile', compact('user', 'followersCount'));
    }
}
