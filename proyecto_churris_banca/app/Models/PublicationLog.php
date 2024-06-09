<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PublicationLog extends Model
{
    use HasFactory;

    protected $table = 'publication_logs';

    protected $fillable = [
        'publication_id',
        'date',
        'time',
        'action',
        'text',
        'user_id',
        'created_at',
        'updated_at'
    ];

    public $timestamps = false;
}