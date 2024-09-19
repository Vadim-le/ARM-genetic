<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Protein extends Model
{
    use HasFactory;

    protected $table = 'proteins'; 

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
        'link',
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
