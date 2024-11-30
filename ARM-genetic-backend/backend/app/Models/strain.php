<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class strain extends Model
{
    use HasFactory;

    protected $table = 'strain'; 

    protected $casts = [
        'place_of_allocation' => 'string',
        'year_of_allocation' => 'string',
        'type_of_bacteria' => 'string',
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
        'place_of_allocation',
        'year_of_allocation',
        'type_of_bacteria',
        'author_id',
    ];


    public function author(){

        return $this->belongsTo(User::class, 'author_id');
    }
    
    public function repeatSequences()
    {
        return $this->hasMany(RepeatSequence::class, 'strain_id');
    }

    public function protein()
    {
        return $this->hasOne(Protein::class, 'strain_id');
    }

    // В модели Strain
    public function analyzeStrains()
    {
        return $this->hasMany(AnalyzeStrain::class);
    }

}