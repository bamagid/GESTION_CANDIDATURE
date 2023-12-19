<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class Role extends Model
{
    protected $guarded=[];
    public function users()
    {
        return $this->hasMany(User::class);
    }
}
