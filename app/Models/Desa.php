<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Desa extends Model
{
    protected $guarded = [];

    public function kecamatan()
    {
        return $this->belongsTo(Kecamatan::class);
    }

    public function laporanBencanas()
    {
        return $this->hasMany(LaporanBencana::class);
    }
}
