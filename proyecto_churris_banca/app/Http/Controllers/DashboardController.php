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
            'post-content' => 'required|max:255', // Ajusta las reglas de validación según tus necesidades
            'post-image' => 'image|mimes:jpeg,png,jpg,gif|max:2048' // Ajusta las reglas de validación según tus necesidades
        ]);

        $user = auth()->user();

        $publication = new Publication();
        $publication->user_id = $user->id;
        $publication->text = $request->input('post-content');

        if ($request->hasFile('post-image')) {
            $imagePath = $request->file('post-image')->store('public/images');
            $publication->image = basename($imagePath);
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

        return view('dashboard', compact('posts'));
    }
}
