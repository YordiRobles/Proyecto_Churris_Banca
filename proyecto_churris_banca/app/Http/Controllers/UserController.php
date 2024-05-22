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
        $imageDetails = $this->getImage($user->image_data);
        $imageData = $imageDetails['imageData'];
        $mimeType = $imageDetails['mimeType'];

        if ($friendship == true && $friendshipInverse == true) {
            // Despliegue información de amigos
            if ($user && $user->id !== $currentUser->id) {
                return view('show_user', ['name' => $user->name, 'email' => $user->email, 'is_following' =>$friendship,
                            'image_data' => $imageData,'mime_type' => $mimeType]);
            } else {
                return Redirect::route('dashboard');
            }
        } else {
            // Despliegue información de seguidores
            if ($user && $user->id !== $currentUser->id) {
                return view('show_user', ['name' => $user->name, 'is_following' =>$friendship]);
            } else {
                return Redirect::route('dashboard');
            }
        }
    }

    // Esta funcion determina si al seguir un usuario se convierte en amigo o seguidor.
    public function followUser(Request $request, $name)
    {
        $follow = $request->input('follow');
        $unfollow = $request->input('unfollow');
        $userToFollow = User::where('name', $name)->first(); 
        $currentUser = Auth::user();
        $imageDetails = $this->getImage($userToFollow->image_data);
        $imageData = $imageDetails['imageData'];
        $mimeType = $imageDetails['mimeType'];
    
        // Si se sigue a un usuario
        if ($follow === '1' && !$unfollow){
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
                    if ($userToFollow && $userToFollow->id !== $currentUser->id) {
                        return view('show_user', ['name' => $userToFollow->name, 'email' => $userToFollow->email,
                                    'is_following' =>$friendship,'image_data' => $imageData,'mime_type' => $mimeType]);
                    } else {
                        return Redirect::route('dashboard');
                    }
                } else {
                    // Despliegue información de seguidores
                    if ($userToFollow && $userToFollow->id !== $currentUser->id) {
                        return view('show_user', ['name' => $userToFollow->name, 'is_following' =>$friendship]);
                    } else {
                        return Redirect::route('dashboard');
                    }
                }
            } else {
                // En caso de que se consiga seguir una persona dos veces, se atrapa el error.
                $friendship = $this->checkIfFollow($currentUser->id, $userToFollow->id);
                if ($userToFollow && $userToFollow->id !== $currentUser->id) {
                    return view('show_user', ['name' => $userToFollow->name, 'is_following' =>$friendship]);
                } else {
                    return Redirect::route('dashboard');
                }
            }

        // Si se deja de seguir a un usuario
        } else if ($unfollow === '2' && !$follow) {
            $existFollow = Follower::where('follower_id', $currentUser->id)
                                        ->where('user_id', $userToFollow->id)
                                        ->first();
                                        \Log::info($existFollow);
            if ($existFollow) {
                $existFollow->delete();
            }
            $friendship = $this->checkIfFollow($currentUser->id, $userToFollow->id);
            // Despliegue información de seguidores
            if ($userToFollow && $userToFollow->id !== $currentUser->id) {
                return view('show_user', ['name' => $userToFollow->name,'is_following' =>$friendship]);
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

    private function getImage($image_data)
    {
        if ($image_data) {
            $imageInfo = getimagesizefromstring($image_data);
            if ($imageInfo) {
                return [
                    'imageData' => base64_encode($image_data),
                    'mimeType' => $imageInfo['mime']
                ];
            }
        }
        // Ruta a la imagen predeterminada
        $defaultImagePath = public_path('img/usuario-de-perfil.png');
        $defaultImageData = file_get_contents($defaultImagePath);
        $defaultImageInfo = getimagesize($defaultImagePath);
        return [
            'imageData' => base64_encode($defaultImageData),
            'mimeType' => $defaultImageInfo['mime']
        ];
    }
    
}