<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class repeat_sequences extends Model
{
    use HasFactory;

    protected $table = 'repeat_sequence'; 

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $guarded = [
        'id',
        'created_at',
        'updated_at',
    ];

    protected $fillable = [
        'name',
        'author_id',
        'strain_id',
    ];


    public function author(){

        return $this->belongsTo(User::class, 'author_id');
    }

    public function strain()
    {
        return $this->belongsTo(Strain::class, 'strain_id');
    }
}