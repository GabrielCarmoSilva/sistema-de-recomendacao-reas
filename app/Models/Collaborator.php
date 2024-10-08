<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Collaborator extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'role',
        'institution',
        'reference',
        'rea_title',
        'interest',
        'profile',
        'item'
    ];
}
