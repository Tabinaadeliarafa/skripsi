<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LaporanBencana extends Model
{
    protected $guarded = [];

    public function desa()
    {
        return $this->belongsTo(Desa::class);
    }

    public function jenisBencana()
    {
        return $this->belongsTo(JenisBencana::class);
    }
}
