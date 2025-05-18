<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Medication extends Model
{
    protected $fillable = ['rxcui', 'name', 'user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
