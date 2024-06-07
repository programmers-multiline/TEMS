<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TersUploads extends Model
{
    use HasFactory;

    protected $guarded = [];
    public function uploads(){
        return $this->belongsTo(Uploads::class,'upload_id','id');
    }
}
