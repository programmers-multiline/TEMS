<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ToolsAndEquipment;
use Illuminate\Support\Facades\Auth;

class SearchController extends Controller
{
    public function search(Request $request){

        $searchTools = ToolsAndEquipment::leftjoin('warehouses', 'warehouses.id', 'tools_and_equipment.location')
        ->leftJoin('project_sites', 'project_sites.id', 'tools_and_equipment.current_site_id')
        ->select('tools_and_equipment.*', 'warehouses.warehouse_name', 'project_sites.customer_name', 'project_sites.project_address')
        ->where('item_description', 'LIKE', '%' . $request->searchVal . '%')
        ->where('tools_and_equipment.status', 1)
        ->where(function ($query) {
            $query->where('tools_and_equipment.current_pe', '!=', Auth::user()->id)
                  ->orWhereNull('tools_and_equipment.current_pe');
        })
        ->limit(10)
        ->get();

        // return $searchTools;

        
        $searchedTools = '';
        foreach ($searchTools as $tool) {

            $location =  $tool->warehouse_name;

            if($tool->current_pe){
                $location = $tool->customer_name .' - '. $tool->project_address;
            }


            $link = $tool->wh_ps == 'wh' ? 'view_warehouse/search' : 'view_project_site/search';
            $searchedTools .= '<div class="col-lg-12">
                <h6 class="mb-1">
                    <a href="'.env('APP_URL').''.$link.'?searchVal='.$tool->item_description.'">' . $tool->item_description . '</a>
                </h6>
                <div class="fs-sm text-earth mb-3">' . $location . '</div>
            </div><hr>';

        }

        return $searchedTools;

    }
}
