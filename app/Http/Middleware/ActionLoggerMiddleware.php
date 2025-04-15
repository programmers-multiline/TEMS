<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Helpers\ActionLogger;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ActionLoggerMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $path = $request->path();

        $tab = '';

        switch ($path) {
            case 'dashboard':
                $tab = 'Dashboard';
                break;

            // RFTTE related paths
            case 'pages/rftte':
                $tab = 'RFTTE - Ongoing';
                break;
            case 'pages/rftte_signed_form_proof':
                $tab = 'RFTTE - Proof of Receiving';
                break;
            case 'pages/not_serve_items':
                $tab = 'RFTTE - Not Serve Items';
                break;
            case 'pages/rftte_completed':
                $tab = 'RFTTE - Completed';
                break;

            // Pull-Out Request related paths
            case 'pages/pullout_warehouse':
                $tab = 'Pull-Out Request - Schedule';
                break;
            case 'pages/pullout_for_receiving':
                $tab = 'Pull-Out Request - For Receiving';
                break;
            case 'pages/pullout_completed':
                $tab = 'Pull-Out Request - Completed';
                break;
            case 'pages/pullout_ongoing':
                $tab = 'Pull-Out Request - Ongoing';
                break;

            // QR Code related paths
            case 'pages/qrcode_generator':
                $tab = 'QR Code Generator';
                break;
            case 'pages/qrcode_scanner':
                $tab = 'QR Code Scanner';
                break;

            // Tool Extension Request
            case 'pages/tool_extension_request':
                $tab = 'Tool Extension Request';
                break;

            // RFTEIS related paths
            case 'pages/rfteis':
                $tab = 'RFTEIS - Ongoing';
                break;
            case 'pages/rfteis_approved':
                $tab = 'RFTEIS - Approved ';
                break;
            case 'pages/rfteis_disapproved':
                $tab = 'RFTEIS - Disapproved';
                break;
            case 'pages/rfteis_acc':
                $tab = 'RFTEIS - Accounting';
                break;

            // RTTTE related paths
            case 'pages/rttte_acc':
                $tab = 'RTTTE - Accounting';
                break;

            // Other Request and Report paths
            case 'pages/acc_approved_request':
                $tab = 'Approved Request - Accounting';
                break;
            case 'pages/list_of_requests':
                $tab = 'List of Request';
                break;
            case 'view_warehouse':
                $tab = 'Warehouses';
                break;
            case 'view_project_site':
                $tab = 'Project Sites';
                break;
            case 'pages/report_pe_logs':
                $tab = 'Item Logs';
                break;
            case 'pages/report_te_logs':
                $tab = 'Tools & Equipment Logs';
                break;
            case 'pages/upload_tools':
                $tab = 'Upload Tools';
                break;
            case 'pages/list_of_upload_tools':
                $tab = 'List of Upload Tools';
                break;
            case 'pages/daf':
                $tab = 'DAF';
                break;

            // Site Transfer related paths
            case 'pages/site_to_site_transfer':
                $tab = 'Site to Site Transfer - Ongoing';
                break;
            case 'pages/sts_request_completed':
                $tab = 'Site to Site Transfer - Completed ';
                break;

            case 'pages/request_ongoing':
                $tab = 'Request - Ongoing';
                break;
            case 'pages/request_for_receiving':
                $tab = 'Request - For Receiving';
                break;
            case 'pages/ps_request_for_receiving':
                $tab = 'Project site Request - For Receiving';
                break;
            case 'pages/request_completed':
                $tab = 'Request - Completed';
                break;
            case 'pages/barcode_scanner':
                $tab = 'Barcode Scanner';
                break;
            case 'pages/approved_pullout':
                $tab = 'Pullout - Approved';
                break;
            case 'pages/site_to_site_approved':
                $tab = 'Site-to-Site Approved';
                break;
            case 'pages/users_management':
                $tab = 'Users Management';
                break;
            case 'pages/pullout_receiving':
                $tab = 'Pullout Receiving';
                break;
            case 'pages/pullout_completed_warehouse':
                $tab = 'Pullout Completed (Warehouse)';
                break;
            case 'view_my_te':
                $tab = 'My Tools and Equipment';
                break;
            case 'project_tagging':
                $tab = 'Project Assignment';
                break;

            // Default case
            default:
                $tab = 'Unknown Page';
                break;
        }


        // Log the action
        if (auth()->check()) {
            $user = Auth::user();
            $action = "{$user->fullname} (EMP#: {$user->emp_id}) Visited: " . $tab;


            ActionLogger::log($action);
        }

        // Continue processing the request
        return $next($request);
    }
}
