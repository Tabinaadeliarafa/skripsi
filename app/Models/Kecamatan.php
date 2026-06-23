<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kecamatan extends Model
{
    protected $guarded = [];

    public function desas()
    {
        return $this->hasMany(Desa::class);
    }

    public function laporanBencanas()
    {
        return $this->hasManyThrough(LaporanBencana::class, Desa::class);
    }

    public function getRiskLevelAttribute()
    {
        $index = (float) $this->indeks_bahaya;

        if ($index <= 0.333) {
            return 'Rendah';
        } elseif ($index <= 0.666) {
            return 'Sedang';
        } else {
            return 'Tinggi';
        }
    }
}
