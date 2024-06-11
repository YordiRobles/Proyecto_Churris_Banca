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
    public function searchUsers(Request $request)
    {
        // Validar la entrada del usuario
        $request->validate([
            'query' => ['required', 'regex:/^(?!\.)[a-zA-Z]+(?:\.[a-zA-Z]+)*$/'],
        ], [
            'query.regex' => 'El nombre de usuario solo puede contener letras y puntos, y no puede tener dos puntos seguidos, terminar con un punto, ni ser un punto único.',
        ]);

        $currentUser = Auth::user();
        $query = $request->input('query');

        // Buscar usuarios que coincidan con el criterio de búsqueda
        $users = User::where('name', 'like', '%'.$query.'%')->get();
        
        // Filtrar los resultados para excluir al usuario actual
        $filteredUsers = $users->filter(function ($user) use ($currentUser) {
            return $user->id !== $currentUser->id;
        });

        return view('search_result', ['users' => $filteredUsers]);
    }

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
            if ($user && $user->id !== $currentUser->id) {
                return view('show_user', ['name' => $user->name, 'email' => $user->email, 'is_following' =>$friendship,
                            'image_data' => $imageData,'mime_type' => $mimeType]);
            } else {
                return Redirect::route('dashboard');
            }
        } else {
            if ($user && $user->id !== $currentUser->id) {
                return view('show_user', ['name' => $user->name, 'is_following' =>$friendship]);
            } else {
                return Redirect::route('dashboard');
            }
        }
    }

    public function followUser(Request $request, $name)
    {
        $follow = $request->input('follow');
        $unfollow = $request->input('unfollow');
        $userToFollow = User::where('name', $name)->first(); 
        $currentUser = Auth::user();
        $imageDetails = $this->getImage($userToFollow->image_data);
        $imageData = $imageDetails['imageData'];
        $mimeType = $imageDetails['mimeType'];
    
        if ($follow === '1' && !$unfollow){
            $existingFollower = Follower::where('follower_id', $currentUser->id)
                                        ->where('user_id', $userToFollow->id)
                                        ->first();

            if (!$existingFollower) {
                $follower = new Follower();
                $follower->follower_id = $currentUser->id;
                $follower->user_id = $userToFollow->id;
                $follower->save();

                $friendship = $this->checkIfFollow($currentUser->id, $userToFollow->id);
                $friendshipInverse = $this->checkIfFollow($userToFollow->id, $currentUser->id);

                if ($friendship == true && $friendshipInverse == true) {
                    if ($userToFollow && $userToFollow->id !== $currentUser->id) {
                        return view('show_user', ['name' => $userToFollow->name, 'email' => $userToFollow->email,
                                    'is_following' =>$friendship,'image_data' => $imageData,'mime_type' => $mimeType]);
                    } else {
                        return Redirect::route('dashboard');
                    }
                } else {
                    if ($userToFollow && $userToFollow->id !== $currentUser->id) {
                        return view('show_user', ['name' => $userToFollow->name, 'is_following' =>$friendship]);
                    } else {
                        return Redirect::route('dashboard');
                    }
                }
            } else {
                $friendship = $this->checkIfFollow($currentUser->id, $userToFollow->id);
                if ($userToFollow && $userToFollow->id !== $currentUser->id) {
                    return view('show_user', ['name' => $userToFollow->name, 'is_following' =>$friendship]);
                } else {
                    return Redirect::route('dashboard');
                }
            }

        } else if ($unfollow === '2' && !$follow) {
            $existFollow = Follower::where('follower_id', $currentUser->id)
                                        ->where('user_id', $userToFollow->id)
                                        ->first();
                                        \Log::info($existFollow);
            if ($existFollow) {
                $existFollow->delete();
            }
            $friendship = $this->checkIfFollow($currentUser->id, $userToFollow->id);
            if ($userToFollow && $userToFollow->id !== $currentUser->id) {
                return view('show_user', ['name' => $userToFollow->name,'is_following' =>$friendship]);
            } else {
                return Redirect::route('dashboard');
            }
        }
    }

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
        $defaultImagePath = public_path('img/usuario-de-perfil.png');
        $defaultImageData = file_get_contents($defaultImagePath);
        $defaultImageInfo = getimagesize($defaultImagePath);
        return [
            'imageData' => base64_encode($defaultImageData),
            'mimeType' => $defaultImageInfo['mime']
        ];
    }
    
}