<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class Formation extends Model
{
    protected $guarded = [];
    public function user() {
        return $this->belongsTo(User::class);
        }
        public function candidatures(){
            return $this->hasMany(Candidature::class);
        }
}
