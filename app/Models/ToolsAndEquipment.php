<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use App\Models\Scopes\CompanyScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ToolsAndEquipment extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected static function booted()
    {
        static::addGlobalScope(new CompanyScope);
    }

    public static function generateOldte()
        {
            return DB::transaction(function () {
                // Lock the table to ensure uniqueness
                $latest = DB::table('upload_tools_details')
                    ->where('asset_code', 'LIKE', 'OLDTE-%')
                    ->orderByRaw("CAST(SUBSTRING(asset_code, 7) AS UNSIGNED) DESC")
                    ->lockForUpdate()
                    ->first();

                // Determine next asset number
                $nextNumber = $latest ? ((int) substr($latest->asset_code, 6)) + 1 : 1;
                $newAssetCode = 'OLDTE-' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);

                return $newAssetCode;
            });
        }


    // public static function generateOldte()
    // {
    //     // Get the latest OLDTE code from the database, filtering only OLDTE codes
    //     $latest = self::where('asset_code', 'LIKE', 'OLDTE-%')
    //         ->orderByRaw("CAST(SUBSTRING(asset_code, 7) AS UNSIGNED) DESC")
    //         ->lockForUpdate()
    //         ->first();

    //     if ($latest) {
    //         // Extract the numeric part and increment it
    //         $latestNumber = (int) substr($latest->asset_code, 6);
    //         $nextNumber = $latestNumber + 1;
    //     } else {
    //         // Start from 1 if no OLDTE records exist yet
    //         $nextNumber = 1;
    //     }

    //     // Format with leading zeros (e.g., OLDTE-00001)
    //     return 'OLDTE-' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
    // }

}
