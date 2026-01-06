<?php

namespace App\Http\Controllers;

use App\Models\{User,Role,Decease,Company,Patient_disease};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Models\{Country, State, City,Usermeta};

use App\Models\CompanyUserMapping;
use App\Imports\UsersImport;
use App\Exports\UsersExport;
use App\Exports\AdminExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Permission;
use App\Http\Middleware\Admin;
use Helper;



class PatientdiseaseController extends Controller
{
    public function patientdiseaseList(Request $request)
    {
        //echo"patientdiseaseList";die;
        $userType = auth()->user()->role()->first()->name;
        $listUrl = 'company-admin-list';
        $deleteUrl = 'admin-patient-disease-delete';
        $perpage = config('app.perpage');
        $breadcrumbs = [
            ['link' => "modern", 'name' => "Home"], ['link' => "javascript:void(0)", 'name' => __('locale.patient_disease')], ['name' =>__('locale.List')]];
        //Pageheader set true for breadcrumbs
        $pageConfigs = ['pageHeader' => true];
        $pageTitle = 'Patient Disease';
        // $usersResult = User::whereHas(
        //     'role', function($q){
        //         $q->where('name', 'company-admin');
        //     }
        // )->select(['id','name','email','phone','address1','image','website_url','blocked'])->orderBy('id','DESC');
        $deceaseResult = Patient_disease::with(['patientname','disease','carehome'])->select(['id','disease_code','patient_code','c_home_code','remark','updated_by_user'])->orderBy('id','DESC');

        //echo"<pre>";print_r($deceaseResult);die;
        if(auth()->user()->role()->first()->name=="Admin")
        {
            $deceaseResult = Patient_disease::with(['patientname','disease','carehome'])->whereHas('carehome', function ($company_q) {
                $company_q->where('id', '=',auth()->user()->company()->first()->id);})->select(['id','disease_code','patient_code','c_home_code','remark','updated_by_user'])->orderBy('id','DESC');
        }
        //$deceaseResult = Decease::select(['id','code','name'])->get();
        $editUrl = 'admin-patient-disease-edit';
        if($request->ajax()){
            $deceaseResult = $deceaseResult->whereHas('disease', function($q)use($request){
                $q->where('id', 'like', '%'.$request->seach_term.'%')
                            ->orWhere('name', 'like', '%'.$request->seach_term.'%');
                            
                           
                        }) ->paginate($perpage);
                        $perPage = $perpage;
                        $page = $deceaseResult->currentPage();
                        
            return view('pages.patient-disease.patient-disease-list-ajax', compact('deceaseResult','editUrl','deleteUrl','page','perPage'))->render();
        }

        $deceaseResult = $deceaseResult->paginate($perpage);
        $perPage = $perpage;
        $page = $deceaseResult->currentPage();
        
        return view('pages.patient-disease.patient-disease-list', ['pageConfigs' => $pageConfigs], ['breadcrumbs' => $breadcrumbs,'deceaseResult'=>$deceaseResult,'pageTitle'=>$pageTitle,'userType'=>$userType,'editUrl'=>$editUrl,'deleteUrl'=>$deleteUrl,'page'=>$page,'perPage'=>$perPage]);
    }

    public function createPatientdisease($id='')
    {
        //echo"ji";die;
        $formUrl = 'admin-patient-disease-create';
        $breadcrumbs = [
            ['link' => "/", 'name' => "Home"], ['link' => "admin/patient-disease-list", 'name' => __("locale.create_patient_disease")], ['name' => (($id!='') ? __('locale.Edit') : __('locale.Create') )],
        ];
        //Pageheader set true for breadcrumbs
        $deceaseResponse='';
        $companyCode = Helper::setNumber();
        $deceaseResult = Decease::select(['id','name','code'])->get();
        $patientResult = User::select(['id','code','name'])->where('typeselect','=','Patient')->get();
        $companies = Company::get(["company_name", "id","company_code"]);
        if($id!=''){
            //$permission_arr = [];
            $deceaseResponse = Patient_disease::find($id);
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
            $formUrl = 'admin-patient-disease-update';
        }
        
        // dd($deceaseResult);
        $pageConfigs = ['pageHeader' => true];
        $pageTitle = __('locale.patient_disease');
        return view('pages.patient-disease.patient-disease-create',['breadcrumbs' => $breadcrumbs], ['pageConfigs' => $pageConfigs,'pageTitle'=>$pageTitle,'deceaseResult'=>$deceaseResult,'deceaseResponse'=>$deceaseResponse,'patientResult'=>$patientResult,'companies'=>$companies,'formUrl'=>$formUrl]);
    }

    public function storePatientdisease(Request $request)
    {
       // echo"<pre>";print_r($request->all());die;
       // echo"store";die;
        $validator = Validator::make($request->all(), [
            'disease_code' => 'required',
            "patient_code"=> "required",
            "c_home_code"=> "required",
        ]);
        if ($validator->fails()) {
            return redirect()->back()
            ->withErrors($validator)
            ->withInput();
        }
        unset($request['_token']);
        Patient_disease::create($request->all());
      
        return redirect()->route('admin.patient-disease-list')->with('success',__('locale.patient_disease_created_successfully'));
    }

    public function updatePatientdisease(Request $request,$id)
    {
        //echo"on update";die;
        $validator = Validator::make($request->all(), [
            'disease_code' => 'required',
            "patient_code" => "required",
            "c_home_code"=>"required",
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
            ->withErrors($validator)
            ->withInput();
        }
        unset($request['_token']);
        unset($request['_method']);
        unset($request['action']);
        $patient_disease = Patient_disease::where('id',$id)->update($request->all());
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
        return redirect()->route('admin.patient-disease-list')->with('success',__('locale.patient_disease_update'));
    }

    public function destroyPatientdisease($id)
    {   
         if(Patient_disease::where('id',$id)->delete()){
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
