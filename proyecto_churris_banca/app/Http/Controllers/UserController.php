<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use App\Models\User;
use App\Models\Follower;


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
        $currentUser = Auth::user();
        $user = User::where('name', $name)->first(); 
        $friendship = $this->checkIfFollow($currentUser->id, $user->id);
        $friendshipInverse = $this->checkIfFollow($user->id, $currentUser->id);

        if ($friendship == true && $friendshipInverse == true) {
            // Despliegue información de amigos TODO: Revisar como pasar la imagen
            if ($user && $user->id !== $currentUser->id) {
                return view('show_user', ['name' => $user->name, 'email' => $user->email]);
            } else {
                return Redirect::route('dashboard');
            }
        } else {
            // Despliegue información de seguidores TODO: Revisar como pasar la imagen
            if ($user && $user->id !== $currentUser->id) {
                return view('show_user', ['name' => $user->name]);
            } else {
                return Redirect::route('dashboard');
            }
        }
    }

    // Esta funcion determina si al seguir un usuario se convierte en amigo o seguidor.
    public function followUser(Request $request, $name)
    {
        $action = $request->input('action');
        $userToFollow = User::where('name', $name)->first(); 
        $currentUser = Auth::user();
        
        // Si se sigue a un usuario
        if ($action === 'follow') {
            $existingFollower = Follower::where('follower_id', $currentUser->id)
                                        ->where('user_id', $userToFollow->id)
                                        ->first();
            if (!$existingFollower) {
                $follower = new Follower();
                $follower->follower_id = $currentUser->id;
                $follower->user_id = $userToFollow->id;
                $follower->save();

                // Verifica si son amigos
                $friendship = $this->checkIfFollow($currentUser->id, $userToFollow->id);
                $friendshipInverse = $this->checkIfFollow($userToFollow->id, $currentUser->id);
        
                if ($friendship == true && $friendshipInverse == true) {
                    // Despliegue información de amigos TODO: Revisar como pasar la imagen
                    if ($user && $user->id !== $currentUser->id) {
                        return view('show_user', ['name' => $user->name, 'email' => $user->email]);
                    } else {
                        return Redirect::route('dashboard');
                    }
                } else {
                    // Despliegue información de seguidores TODO: Revisar como pasar la imagen
                    if ($user && $user->id !== $currentUser->id) {
                        return view('show_user', ['name' => $user->name]);
                    } else {
                        return Redirect::route('dashboard');
                    }
                }
            }
        // Si se deja de seguir a un usuario
        } else if ($action === 'unfollow') {
            $existFollow = $this->checkIfFollow($currentUser->id, $userToFollow->id);
            if ($existFollow) {
                $existFollow->delete();
            }
            // Despliegue información de seguidores TODO: Revisar como pasar la imagen
            if ($user && $user->id !== $currentUser->id) {
                return view('show_user', ['name' => $user->name]);
            } else {
                return Redirect::route('dashboard');
            }
        }
    }

    // Verifica si existe una relación de seguimiento entre dos usuarios
    private function checkIfFollow($userId1, $userId2)
    {
        $friendship = Follower::where('follower_id', $userId1)
                              ->where('user_id', $userId2)
                              ->first();
        return $friendship !== null;
    }
}