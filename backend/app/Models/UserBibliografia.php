<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserBibliografia extends Model
{
    use HasFactory;

    protected $table = 'bibliografia'; // Указываем имя таблицы

    protected $fillable = [
        'journal_title',
        'journal_link',
    ];
}
