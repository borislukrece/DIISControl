<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pointage extends Model
{
    use HasFactory;
    protected $fillable = ['id_agent','id_admin','heure_arrivee','heure_depart', 'total_heure', 'date_actuelle', 'motif'];
}
