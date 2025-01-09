<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Record extends Model
{
    protected $fillable = ['name', 'image', 'category'];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
