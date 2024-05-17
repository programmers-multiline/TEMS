<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ToolsAndEquipment;

class SearchController extends Controller
{
    public function search(Request $request){

        $searchTools = ToolsAndEquipment::leftjoin('warehouses', 'warehouses.id', 'tools_and_equipment.location')
        ->select('tools_and_equipment.*', 'warehouses.warehouse_name')
        ->where('item_description', 'LIKE', '%' . $request->searchVal . '%')
        ->where('tools_and_equipment.status', 1)
        ->limit(7)
        ->get();

        // return $searchTools;

        
        $searchedTools = '';
        foreach ($searchTools as $tool) {
            $link = $tool->wh_ps == 'wh' ? 'view_warehouse/search/'.$tool->item_description.'' : 'pages/project_site';
            $searchedTools .= '<div class="col-lg-12">
                <h6 class="mb-1">
                    <a href="'.env('APP_URL').''.$link.'">' . $tool->item_description . '</a>
                </h6>
                <div class="fs-sm text-earth mb-3">' . $tool->warehouse_name . '</div>
            </div><hr>';
        }

        return $searchedTools;

    }
}
