<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class CompanyScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        $companyId = auth()->user()->comp_id ?? null;

        if (in_array($companyId, [3, 2])) {
            $table = $model->getTable();
            $builder->where("{$table}.company_id", $companyId);
        }
    }
}
