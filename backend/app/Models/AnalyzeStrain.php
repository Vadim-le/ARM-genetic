<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnalyzeStrain extends Model
{
    use HasFactory;

    // Укажите имя таблицы, если оно не совпадает с именем модели в нижнем регистре и во множественном числе
    protected $table = 'analyze_strain';

    // Укажите, какие поля могут быть массово заполнены
    protected $fillable = [
        'author_id',
        'strain_id',
        'repeat_sequence',
        'repeat_positions',
        'spacer_sequence',
        'spacer_positions',
        'is_known',
        'status',
    ];

    // Если вы хотите использовать другие типы данных для некоторых полей, укажите их в кастах
    protected $casts = [
        'repeat_positions' => 'array',
        'spacer_positions' => 'array',
        'is_known' => 'boolean',
    ];

    // Определите связи с другими моделями, если нужно
    public function strain()
    {
        return $this->belongsTo(Strain::class);
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }
}