<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;
    protected $fillable = ['nom','libelle','id_sous_direction'];

    public function sous_direction()
    {
        return $this->belongsTo(Sous_direction::class, 'id_sous_direction');
    }
}
