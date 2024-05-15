<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Publication;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $posts = $user->publications()->orderByDesc('created_at')->get();
        return view('dashboard', compact('posts'));
    }

    public function storePost(Request $request)
    {
        // Validar los datos del formulario
        $request->validate([
            'post-content' => 'required|max:255', // Ajusta las reglas de validación según tus necesidades
            'post-image' => 'image|mimes:jpeg,png,jpg,gif|max:2048' // Ajusta las reglas de validación según tus necesidades
        ]);

        // Obtener el usuario actualmente autenticado
        $user = auth()->user();

        // Crear una nueva instancia de la publicación
        $publication = new Publication();
        $publication->user_id = $user->id;
        $publication->text = $request->input('post-content');

        // Guardar la imagen si se proporciona
        if ($request->hasFile('post-image')) {
            $imagePath = $request->file('post-image')->store('public/images');
            $publication->image = basename($imagePath);
        }

        // Guardar la publicación en la base de datos
        $publication->save();

        // Redireccionar o mostrar un mensaje de éxito
        return redirect()->back()->with('success', 'Publicación creada correctamente');
    }

    public function showPosts()
    {
        // Obtener el usuario actualmente autenticado
        $user = auth()->user();
    
        // Obtener las publicaciones del usuario autenticado
        $posts = $user->publications()->orderByDesc('created_at')->get();
    
        // Pasar las publicaciones a la vista
        return view('dashboard', compact('posts'));
    }
    
    
}
