<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Scopes\CompanyScope;

class PulloutRequest extends Model
{
    use HasFactory;
    protected $guarded = [];
    
    protected static function booted()
    {
        static::addGlobalScope(new CompanyScope);
    }

    public function approvers()
    {
        return $this->hasMany(RequestApprover::class, 'request_id');
    }
}
