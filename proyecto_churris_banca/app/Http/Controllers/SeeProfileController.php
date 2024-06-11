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
        $currentUser = Auth::user();
        $user = User::with('publications')->findOrFail($id);
        if ($currentUser->id === $user->id) {
            $followersCount = $user->followers()->count();
            $profileImageDetails = $this->getUserImage($user->image_data);
            $user->image_data = $profileImageDetails['imageData'];
            $user->mime_type = $profileImageDetails['mimeType'];
            return view('seeprofile', compact('user', 'followersCount'));
        }
        if ($currentUser->id != $user->id) {
            return redirect()->back()->with('failed', 'No se puede visualizar la información de ese usuario');
        }

    }
    
    private function getUserImage($image_data)
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

