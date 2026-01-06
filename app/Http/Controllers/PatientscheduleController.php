<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Models\{Country, State, City};
use App\Models\{User,Role,Patient_schedule};
use App\Models\Company;
use App\Models\CompanyUserMapping;
use App\Imports\UsersImport;
use App\Exports\UsersExport;
use App\Exports\AdminExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Permission;
use Helper;

class PatientscheduleController extends Controller
{
    public function patientscheduleList(Request $request)
    {
        //echo"admin patient schedule";die;

        $userType = auth()->user()->role()->first()->name;
        $listUrl = 'company-admin-list';
        $deleteUrl = 'admin-patient-schedule-delete';
        $perpage = config('app.perpage');
        $breadcrumbs = [
            ['link' => "modern", 'name' => "Home"], ['link' => "javascript:void(0)", 'name' => __('locale.patient_schedule')], ['name' => __('locale.patient_schedule_list')]];
        //Pageheader set true for breadcrumbs
        $pageConfigs = ['pageHeader' => true];
        $pageTitle = 'Patient schedule';
        // $usersResult = User::whereHas(
        //     'role', function($q){
        //         $q->where('name', 'company-admin');
        //     }
        // )->select(['id','name','email','phone','address1','image','website_url','blocked'])->orderBy('id','DESC');
        $roles=Role::get(["id","name"]);
        $patientscheduleResult = Patient_schedule::with(['patientname','carername','alternatecarername','role','comp'])->where(function ($company_q) {
            $company_q->where('carer_assigned_by', '=',auth()->user()->id);})->select(['patient_schedule.id','patient_id','date','time','carer_code','carer_assigned_by','alternate_carer_code','remarks','attended','attended_remarks','attended_on_time','company'])->orderBy('id','desc');
       //echo"<pre>";print_r($patientscheduleResult);die;
       if(auth()->user()->role()->first()->name=="Admin")
       {
        $patientscheduleResult = Patient_schedule::with(['patientname','carername','alternatecarername','role','comp'])->whereHas('comp', function ($company_q) {
               $company_q->where('id', '=',auth()->user()->company()->first()->id);})->select(['patient_schedule.id','patient_id','date','time','carer_code','carer_assigned_by','alternate_carer_code','remarks','attended','attended_remarks','attended_on_time','company'])->orderBy('id','DESC');
       }
        $editUrl = 'admin-patient-schedule-edit';
        if($request->ajax()){
            // $patientscheduleResult = $patientscheduleResult->when($request->seach_term, function($q)use($request){
            //     $q->where('id', 'like', '%'.$request->seach_term.'%')
            //                 ->orWhere('name', 'like', '%'.$request->seach_term.'%');
                            
            // })->paginate($perpage);
            $patientscheduleResult = $patientscheduleResult->whereHas('patientname', function($q)use($request){
                $q->where('id', 'like', '%'.$request->seach_term.'%')
                            ->orWhere('name', 'like', '%'.$request->seach_term.'%');
                            
            })->paginate($perpage);//search in laravel through relationship
            $perPage = $perpage;
            $page = $patientscheduleResult->currentPage();            
            return view('pages.admin-patient-schedule.patient-schedule-list-ajax', compact('patientscheduleResult','editUrl','deleteUrl','page','perPage'))->render();
        }

        $patientscheduleResult = $patientscheduleResult->paginate($perpage);
        $perPage = $perpage;
        $page = $patientscheduleResult->currentPage();
        
        return view('pages.admin-patient-schedule.patient-schedule-list', ['pageConfigs' => $pageConfigs], ['breadcrumbs' => $breadcrumbs,'patientscheduleResult'=>$patientscheduleResult,'pageTitle'=>$pageTitle,'userType'=>$userType,'editUrl'=>$editUrl,'deleteUrl'=>$deleteUrl,'roles'=>$roles,'page'=>$page,'perPage'=>$perPage]);
    }

    public function createPatientschedule($id='')
    {
       // echo"hi patient schedule form";die;
        //echo auth()->user()->id;die;
        $userType = auth()->user()->role()->first()->name;
        $formUrl = 'admin-patient-schedule-create';
        $patient_schedule_result=$states=$cities=false;
        $breadcrumbs = [
            ['link' => "modern", 'name' => "Home"], ['link' => "javascript:void(0)", 'name' => __('locale.patient schedule')], ['name' => (($id!='') ? __('locale.Edit') : __('locale.Create') )]];
        //Pageheader set true for breadcrumbs
        $pageConfigs = ['pageHeader' => true];
        $patient = User::where('typeselect','=','Patient')->get(["name", "id"]);
        $carer = User::where('typeselect','=','Carer')->get(["name", "id"]);
        $roles=Role::where('name','!=','superadmin')->get(["id","name"]);
        $companies = Company::get(["company_name", "id","company_code"]);
        //$companyCode = Helper::setNumber();
        $pageTitle = __('locale.Patient schedule name'); 
        if($id!=''){
            //$permission_arr = [];
            $patient_schedule_result = Patient_schedule::with('comp')->find($id);
            // if($user_result->permission->count()>0){
            //     foreach($user_result->permission as $permission_val){
            //         $permission_arr[$permission_val->name][] = $permission_val->guard_name;
            //     }
            // }
            // $user_result->permission = $permission_arr;
            // echo '<pre>';print_r($user_result);exit();
            // if($user_result){
            // $states = State::where('country_id',$user_result->country)->get(["name", "id"]);
            // $cities = City::where('state_id',$user_result->state)->get(["name", "id"]);
            // }
            $formUrl = 'admin-patient-schedule-update';
        }
        //dd($patient_schedule_result);
        return view('pages.admin-patient-schedule.patient-schedule-create', ['pageConfigs' => $pageConfigs], ['breadcrumbs' => $breadcrumbs,'patient'=>$patient,'pageTitle'=>$pageTitle,'patient_schedule_result'=>$patient_schedule_result,'states'=>$states,'cities'=>$cities,'userType'=>$userType,'formUrl'=>$formUrl,'carer'=>$carer,'roles'=>$roles,'companies'=>$companies]);
    }

    public function storePatientschedule(Request $request){
        
        //echo '<pre>';print_r($request->all()); exit();

        $validator = Validator::make($request->all(), [
            'patient_id' => 'required|max:250',
            //'email' => 'required|unique:users|max:250',
            'carer_code' => 'required|max:20',
            
            
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
            ->withErrors($validator)
            ->withInput();
        }

        // $role = Role::where('name','=',$request['typeselect'])->first();
        // $random_password = Str::random(6);
        // $request['password'] = Hash::make($random_password);
        unset($request['_token']);
        unset($request['action']);
        $request['date'] = date('Y-m-d', strtotime($_POST['date']));
        $patientschedule = Patient_schedule::create($request->all());
        
        //$id = $user->id;
        //echo $role->id;die;
       // $user->company()->attach($request->company);
       // $user->role()->attach($role->id);
        // if($request->has('permission_allow')){
        //     $i=0;
        //     $permissionInsert = [];
        //     foreach($request->input('permission_allow') as $key => $permissionVal){
        //         // echo '<pre>';print_r($permissionVal['guard_name']);
        //         if(isset($permissionVal['guard_name'])){
        //             for($g=0;$g<count($permissionVal['guard_name']);$g++){
        //                 $permissionInsert[$i]['user_id'] = $id;
        //                 $permissionInsert[$i]['name'] = $key;
        //                 $permissionInsert[$i]['guard_name'] = $permissionVal['guard_name'][$g];
        //                 $i++;
        //             }
        //         }
        //     }
        //     if(!empty($permissionInsert)){
        //         Permission::where('user_id',$user->id)->delete();
        //         Permission::insert($permissionInsert);
        //     }
        // }
        
        return redirect()->route('admin.patient-schedule-list')->with('success',__('locale.patient_schedule_create_success'));
    }

    public function updatePatientschedule(Request $request, $id)
    {
        //echo"update";die;
       //echo"<pre>";print_r($request->all());die;
        $validator = Validator::make($request->all(), [
            //'patient_id' => 'required',
            //"carer_code" => "required",
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
            ->withErrors($validator)
            ->withInput();
        }
        unset($request['_token']);
        unset($request['_method']);
        unset($request['action']);

        $patientscheduleData = Patient_schedule::where('id',$id)->update($request->all());
        // dd($request->all());
        // $MappingData = [];
        // for($p=0;$p<count($request->inventory_id);$p++){
        //     $MappingData[] = ['decease_id'=>$request->company_id,'inventory_id'=>$request->inventory_id];
        // }
        // if(!empty($productMappingData)){
        //     DeceaseInventoryMapping::where('id',$id)->delete();
        //     DeceaseInventoryMapping::insert($MappingData);
        //     return redirect()->route('inventory-mapping.index')->with('success',__('locale.success common update'));
        // }
        return redirect()->route('admin.patient-schedule-list')->with('success',__('locale.success common update'));
    }

    public function destroyPatientschedule($id)
    {   
         if(Patient_schedule::where('id',$id)->delete()){
           return redirect()->back()->with('success',__('locale.delete_message'));
        }
        else{
        return redirect()->back()->with('error',__('locale.try_again'));
        }

        
        // $companyId = companyUserMapping::where('user_id',$id)->first()->company_id;
        // if(companyUserMapping::where('company_id',$companyId)->where('user_id','!=',$id)->count()==0){
        //     if(User::where('id',$id)->delete()){
        //         return redirect()->back()->with('success',__('locale.delete_message'));
        //     }else{
        //         return redirect()->back()->with('error',__('locale.try_again'));
        //     }
        // }else{
        //     return redirect()->back()->with('error',__('locale.company_admin_delete_error_msg'));
        // }
    }


}
