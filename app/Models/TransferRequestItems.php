<?php

namespace App\Models;

use App\Models\TransferRequest;
use App\Models\ToolsAndEquipment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TransferRequestItems extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function tools(){
        return $this->belongsTo(ToolsAndEquipment::class, 'tool_id', 'id');
    }
    public function transfer_request() {
        return $this->belongsTo(TransferRequest::class, 'pe', 'pe');
    }
}
