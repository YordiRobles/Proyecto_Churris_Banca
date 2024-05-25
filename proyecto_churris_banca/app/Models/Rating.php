<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Rating extends Model
{
    use HasFactory;

    protected $fillable = ['publication_id', 'user_id', 'action'];

    // Relación con la publicación
    public function publication()
    {
        return $this->belongsTo(Publication::class);
    }

    // Relación con el usuario
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
