<?php

namespace App\Http\Controllers;

use App\Models\ProjectSites;
use App\Models\User;
use App\Models\Companies;
use App\Models\PmGroupings;
use App\Models\RequestType;
use Illuminate\Http\Request;
use App\Models\SetupApprover;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function approvers_setup()
    {

        $companies = Companies::where('status', 1)->get();

        $request_types = RequestType::where('status', 1)->get();

        return view('pages/approvers_setup', compact('companies', 'request_types'));
    }

    public function fetch_approvers(Request $request)
    {
        if($request->RT){
            $approvers = SetupApprover::leftJoin('users', 'setup_approvers.user_id', 'users.id')
            ->leftJoin('companies', 'companies.id', 'setup_approvers.company_id')
            ->leftJoin('positions', 'positions.id', 'users.pos_id')
            ->select('setup_approvers.sequence', 'users.fullname', 'positions.position', 'companies.code', 'users.id', 'setup_approvers.id as sa_id')
            ->where('setup_approvers.status', 1)
            ->where('users.status', 1)
            ->where('positions.status', 1)
            ->where('companies.status', 1)
            ->where('request_type', $request->RT)
            ->where('company_id', $request->company)
            ->orderBy('sequence', 'asc')
            ->get();
        }else{
            $approvers = SetupApprover::leftJoin('users', 'setup_approvers.user_id', 'users.id')
            ->leftJoin('companies', 'companies.id', 'setup_approvers.company_id')
            ->leftJoin('positions', 'positions.id', 'users.pos_id')
            ->select('setup_approvers.sequence', 'users.fullname', 'positions.position', 'companies.code', 'users.id', 'setup_approvers.id as sa_id')
            ->where('setup_approvers.status', 1)
            ->where('users.status', 1)
            ->where('positions.status', 1)
            ->where('companies.status', 1)
            ->where('request_type', $request->requestType)
            ->where('company_id', $request->company)
            ->where('setup_approvers.area', $request->area)
            ->where('requestor', $request->requestor)
            ->orderBy('sequence', 'asc')
            ->get();
        }
     
        // return view('pages.components.approvers_list', compact('approvers'))->render();
        // return $html;
            $total_count = $approvers->count();


        return DataTables::of($approvers)

            ->addColumn('action', function ($row) use ($total_count) {

                if($total_count == $row->sequence){
                    return '<button type="button" data-id="'. $row->sa_id .'" data-fn="'. $row->fullname .'" data-comp="'. $row->code .'" data-pos="'. $row->position .'" data-triggerby="edit" class="editApprover btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#approverSetupModal"><i class="fa fa-pen"></i></button>
                    <button type="button" data-id="'. $row->sa_id .'" class="deleteApprover btn btn-sm btn-danger"><i class="fa fa-xmark"></i></button>';
                }else{
                    return '<button type="button" data-id="'. $row->sa_id .'" data-fn="'. $row->fullname .'" data-comp="'. $row->code .'" data-pos="'. $row->position .'" data-triggerby="edit" class="editApprover btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#approverSetupModal"><i class="fa fa-pen"></i></button>';
                }


            })
            ->rawColumns(['action'])
            ->toJson();

    }

    public function fetch_users(Request $request)
    {
        $users = User::leftjoin("positions", "positions.id", "users.pos_id")
            ->select("users.*", "positions.position")
            ->where("positions.status", 1)
            ->where("users.status", 1)
            ->where("comp_id", $request->comp)
            ->get();

        $html = "";
        foreach ($users as $user) {
            $html .= '<option value=' . $user->id . '>' . $user->fullname . ' - ' . $user->position . '</option>';

        }

        return $html;

    }

    public function add_approvers(Request $request)
    {
        
        $approvers = json_decode($request->arrApprover);


        if ($request->hiddenTriggerBy == 'edit') {
            $updateApprover = SetupApprover::where('status', 1)
                ->where('id', $request->hiddenId)
                ->first();

            $updateApprover->user_id = $approvers;
            $updateApprover->update();
        } else {
            $fetch_approver_count = SetupApprover::where('status', 1)
                ->where('company_id', $request->selectedComp)
                ->where('request_type', $request->selectedRT)
                ->where('area', $request->selectedArea)
                // ->orderBy('sequence', 'desc')
                ->count();

            
            if($request->selectedRT == 1){
                $sequence = 3;
            }else{
                $sequence = 1;
            }

            if ($fetch_approver_count) {
                $sequence = $fetch_approver_count + 1;
            }


            foreach ($approvers as $approver) {
                SetupApprover::create([
                    'user_id' => $approver,
                    'request_type' => $request->selectedRT,
                    'area' => $request->selectedArea,
                    'requestor' => $request->selectedRequestor,
                    'sequence' => $sequence++,
                    'company_id' => $request->selectedComp,

                ]);
            }
        }




    }

    public function delete_approver(Request $request)
    {
        $approver = SetupApprover::find($request->setupApproverId);

        $approver->status = 0;

        $approver->update();
    }


    public function user_per_area(Request $request)
    {

        $requestors = User::where('status', 1)->where('area', $request->selectArea)->where('user_type_id', 4)->get(['id', 'fullname']);
        
        $html = "";
            $html .= '<option selected disabled>Select Requestor</option>';
        foreach ($requestors as $requestor) {
            $html .= '<option value=' . $requestor->id . '>' . $requestor->fullname. '</option>';

        }

        return $html;

    }

    public function update_sequence(Request $request){

        foreach ($request->newSequence as $data){
            // $approver = SetupApprover::where('status', 1)->where('id', $data['id'])->get(['id', 'sequence']);
    
            // $approver->sequence = $data['newSec'];
    
            // $approver->update();

            SetupApprover::where('status', 1)->where('id', $data['id'])->update([
                'sequence' => $data['newSec']
            ]);
    
        }
        
    }


    public function add_zero_sequence(Request $request){

        SetupApprover::create([
            'company_id' => $request->company,
            'request_type' => $request->requestType,
            'area' => $request->area,
            'requestor' => $request->requestor,
            'sequence' => 0,
            'user_id' => 1,
        ]);

        
    }

    public function fetch_users_admin(Request $request){

        if ($request->has('status') && ($request->status === '0' || $request->status === '1')) {
            $status = (int) $request->status;
        
            $users = User::leftJoin('companies', 'companies.id', 'users.comp_id')
                ->leftJoin('departments', 'departments.id', 'users.dept_id')
                ->leftJoin('positions', 'positions.id', 'users.pos_id')
                ->select('users.*', 'companies.code', 'departments.department_name', 'positions.position', 'users.status as us')
                ->where('users.status', $status)
                ->get();
        } else {
            $users = User::leftJoin('companies', 'companies.id', 'users.comp_id')
                ->leftJoin('departments', 'departments.id', 'users.dept_id')
                ->leftJoin('positions', 'positions.id', 'users.pos_id')
                ->select('users.*', 'companies.code', 'departments.department_name', 'positions.position', 'users.status as us')
                ->get();
        }
        

        return DataTables::of($users)

        ->addColumn('action', function($row){
            return '<button type="button" data-id="' . $row->id . '" data-empid="' . $row->emp_id . '" data-email="' . $row->email . '" data-fullname="' . $row->fullname . '" data-usertype="' . $row->user_type_id . '" data-area="' . $row->area . '" data-posid="' . $row->pos_id . '" data-deptid="' . $row->dept_id . '" data-compid="' . $row->comp_id . '" class="editUserBtn btn btn-sm btn-info js-bs-tooltip-enabled" data-bs-toggle="tooltip" aria-label="Edit" data-bs-original-title="Edit">
            <i class="fa fa-pencil-alt"></i>
          </button>
          <button type="button" data-id="' . $row->id . '" class="deleteToolsBtn btn btn-sm btn-danger js-bs-tooltip-enabled" data-bs-toggle="tooltip" aria-label="Delete" data-bs-original-title="Delete">
            <i class="fa fa-user-slash"></i>
          </button>';
        })

        ->addColumn('user_status', function($row){

            $status = $row->us;

            if($status == 1){
                return '<span class="badge bg-success">active</span>';
            }else{
                return '<span class="badge bg-warning">Inactive</span>';
            }
        })


        ->rawColumns(['action', 'user_status'])
        ->toJson();

    }

    public function change_status(Request $request){
        User::find($request->userId)->update(['status' => 0]);
    }


    public function user_add_edit(Request $request){

        if($request->hiddenTriggerBy == 'add'){
            
            $is_email_not_unique = User::where('status', 1)->where('email', $request->email)->first();
            $is_emp_not_unique = User::where('status', 1)->where('emp_id', $request->empId)->first();

            if($is_email_not_unique){
                return 1;
            }

            if($is_emp_not_unique){
                return 2;
            }


            User::create([
                'emp_id' => $request->empId,
                'fullname' => $request->fullname,
                'comp_id' => $request->company,
                'dept_id' => $request->department,
                'pos_id' => $request->position,
                'user_type_id' => $request->userType,
                'email' => $request->email,
                'username' => $request->username,
                'password' => $request->password,
                'area' => $request->area,
            ]);
        }else{
            User::where('status', 1)->where('id', $request->hiddenId)->update([
                'emp_id' => $request->empId,
                'fullname' => $request->fullname,
                'comp_id' => $request->company,
                'dept_id' => $request->department,
                'pos_id' => $request->position,
                'user_type_id' => $request->userType,
                'email' => $request->email,
                'area' => $request->area,
            ]);
        }
       
    }

    public function fetch_project_site_list(Request $request){

        $projects = DB::table('project_sites as ps')
    ->leftJoin('assigned_projects as pm', function($join) {
        $join->on('ps.id', '=', 'pm.project_id')
            ->where('pm.pos', '=', 'pm')
            ->where('pm.status', 1);
    })
    ->leftJoin('users as pm_user', 'pm.user_id', '=', 'pm_user.id')
    ->leftJoin('assigned_projects as pe', function($join) {
        $join->on('ps.id', '=', 'pe.project_id')
            ->where('pe.pos', '=', 'pe')
            ->where('pe.status', 1);
    })
    ->leftJoin('users as pe_user', 'pe.user_id', '=', 'pe_user.id')
    ->leftJoin('companies as c', 'ps.company_id', '=', 'c.id') // join companies table
    ->select(
        'ps.id',
        'c.code as company_code', // get company code here
        'ps.project_code',
        'ps.project_name',
        'ps.project_address',
        'pe_user.fullname as pe_name',
        'pm_user.fullname as pm_name',
        'ps.area',
        'ps.progress',
    )
    ->where('ps.status', 1)
    ->get();




        return DataTables::of($projects)->toJson();
    }


    public function add_project_code(Request $request){
         
        
        ProjectSites::create([
            'company_id' => $request->company,
            'area' => $request->area, 
            'project_code' => $request->pcode,
            'project_name' => $request->pname,
            'project_location' => $request->plocation,
            'project_address' => $request->paddress,
            'customer_name' => $request->customerName
        ]);
    }
}
