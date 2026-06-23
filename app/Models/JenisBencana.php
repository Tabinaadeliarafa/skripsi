<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JenisBencana extends Model
{
    protected $guarded = [];

    public function laporanBencanas()
    {
        return $this->hasMany(LaporanBencana::class);
    }
}
