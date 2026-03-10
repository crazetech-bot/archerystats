<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class ClubScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        if (! app()->has('currentClub')) {
            return;
        }

        $clubId = app('currentClub')->id;

        // Models with a many-to-many clubs pivot (Archer, Coach)
        if (method_exists($model, 'clubs')) {
            $builder->whereHas('clubs', fn($q) => $q->where('clubs.id', $clubId));
            return;
        }

        // Fallback for models with a direct club_id FK
        $builder->where($model->getTable() . '.club_id', $clubId);
    }
}
