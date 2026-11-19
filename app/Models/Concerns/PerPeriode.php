<?php

namespace App\Models\Concerns;

use App\Models\PeriodeKurban;

trait PerPeriode
{
    protected static function bootPerPeriode(): void
    {
        static::creating(function ($model) {
            if (empty($model->periode_id)) {
                $model->periode_id = PeriodeKurban::where('is_aktif', true)->value('id');
            }
        });
    }

    public function scopeDiPeriode($query, $periodeId)
    {
        return $query->where($this->getTable() . '.periode_id', $periodeId);
    }
}