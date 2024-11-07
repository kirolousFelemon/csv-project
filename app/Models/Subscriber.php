<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscriber extends Model
{
    use HasFactory;

    protected $fillable = [
        'email',
    'id',
    'license_id',
    'specialty',
    'organization_name',
    'telephone',
    'name',
    'national_id',
    'city',
    ];
}
