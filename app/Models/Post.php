<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class Post extends Model
{
    use HasFactory, SoftDeletes, HasTranslations;

    protected $table = 'posts';

    public $translatable = ['title', 'body'];

    protected $fillable = [
        'title',
        'body',
        'status',
    ];
}
