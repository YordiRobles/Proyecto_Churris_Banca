<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Publication;

class DashboardController extends Controller
{
    public function index()
    {
        return $this->showPosts(); // Usar showPosts para manejar la vista del dashboard
    }

    public function storePost(Request $request)
    {
        $request->validate([
            'post-content' => 'required|max:255',
            'post-image' => 'image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);
    
        $user = auth()->user();
    
        $publication = new Publication();
        $publication->user_id = $user->id;
        $publication->text = $request->input('post-content');
    
        if ($request->hasFile('post-image')) {
            $image = $request->file('post-image');
            $imageData = file_get_contents($image->getRealPath());
            $publication->image_data = $imageData; // Almacena los datos de la imagen en el campo MEDIUMBLOB
        }
    
        $publication->save();
    
        return redirect()->back()->with('success', 'Se ha realizado la publicación');
    }

    public function showPosts()
    {
        $user = auth()->user();

        // Obtener las publicaciones del usuario autenticado y de las personas que sigue
        $followingIds = $user->followings()->pluck('users.id');
        $posts = Publication::whereIn('user_id', $followingIds)
                            ->orWhere('user_id', $user->id)
                            ->orderByDesc('created_at')
                            ->get();

        $posts->each(function ($post) {
            $imageDetails = $this->getImage($post->image_data);
            $post->image_data = $imageDetails['imageData'];
            $post->mime_type = $imageDetails['mimeType'];
        });

        return view('dashboard', compact('posts'));
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
        return [
            'imageData' => null,
            'mimeType' => null
        ];
    }
}
