<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Publication;
use App\Models\Rating;
use App\Models\User;

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
            $publication->image_data = $imageData;
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
    
        // Procesar las imágenes de las publicaciones
        $posts->each(function ($post) {
            // Obtener detalles de la imagen adjunta a la publicación
            $imageDetails = $this->getImage($post->image_data);
            $post->image_data = $imageDetails['imageData'];
            $post->mime_type = $imageDetails['mimeType'];
            // Obtener la imagen de perfil del usuario que hizo la publicación
            $profileImageDetails = $this->getUserImage($post->user->image_data);
            $post->user->image_data = $profileImageDetails['imageData'];
            $post->user->mime_type = $profileImageDetails['mimeType'];
        });
    
        return view('dashboard', compact('posts', 'user'));
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
        // Ruta a la imagen predeterminada
        $defaultImagePath = public_path('img/usuario-de-perfil.png');
        $defaultImageData = file_get_contents($defaultImagePath);
        $defaultImageInfo = getimagesize($defaultImagePath);
        return [
            'imageData' => base64_encode($defaultImageData),
            'mimeType' => $defaultImageInfo['mime']
        ];
    }

    public function likePost(Request $request)
    {
        $user = auth()->user();
        $post = Publication::findOrFail($request->post_id);
    
        // Verifica si el usuario ya ha dado "like" a la publicación
        $existingRating = $post->likes()->where('user_id', $user->id)->first();
        $existingDislikeRating = $post->dislikes()->where('user_id', $user->id)->first();
    
        if ($existingDislikeRating) {
            // Eliminar el dislike del usuario autenticado
            $existingDislikeRating->delete();
            $post->decrement('dislikes_count');
        }
    
        if (!$existingRating) {
            // El usuario no ha dado "like" previamente, se agrega el "like"
            $post->likes()->create([
                'user_id' => $user->id,
                'action' => 1
            ]);
            $post->increment('likes_count');
        } else {
            // El usuario ya ha dado "like" previamente, se elimina el "like"
            $existingRating->delete();
            $post->decrement('likes_count');
        }
    
        return response()->json([
            'likes_count' => $post->likes_count,
            'dislikes_count' => $post->dislikes_count
        ]);
    }
    
    public function dislikePost(Request $request)
    {
        $user = auth()->user();
        $post = Publication::findOrFail($request->post_id);
    
        // Verifica si el usuario ya ha dado "dislike" a la publicación
        $existingRating = $post->dislikes()->where('user_id', $user->id)->first();
        $existingLikeRating = $post->likes()->where('user_id', $user->id)->first();
    
        if ($existingLikeRating) {
            // Eliminar el like del usuario autenticado
            $existingLikeRating->delete();
            $post->decrement('likes_count');
        }
    
        if (!$existingRating) {
            $post->dislikes()->create([
                'user_id' => $user->id,
                'action' => 0
            ]);
            $post->increment('dislikes_count');
        } else {
            $existingRating->delete();
            $post->decrement('dislikes_count');
        }
    
        return response()->json([
            'likes_count' => $post->likes_count,
            'dislikes_count' => $post->dislikes_count
        ]);
    }

    // Se agrega metodo para borrar
    public function destroy($id)
    {
        $publication = Publication::findOrFail($id);

        // Verificar si el usuario actual es el dueño de la publicación
        if (auth()->user()->id !== $publication->user_id) {
            return redirect()->route('dashboard')->with('error', 'No tienes permiso para eliminar esta publicación.');
        }

        $publication->delete();

        return redirect()->route('dashboard')->with('success', 'Publicación eliminada correctamente.');
    }
}
