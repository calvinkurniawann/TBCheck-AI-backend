<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScreeningHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'selected_symptoms',
        'cf_score_raw',
        'cf_score_percentage',
        'risk_level',
    ];

    protected $casts = [
        'selected_symptoms' => 'array',
        'cf_score_raw' => 'float',
        'cf_score_percentage' => 'float',
    ];
}