<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{User,Role,Decease,Company,Patient_disease,Patient_medicine,Medicine};

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

class PatientmedicineController extends Controller
{
    public function patientmedicineList(Request $request)
    {
       // echo"patientmedicineList";die;
        $userType = auth()->user()->role()->first()->name;
        $listUrl = 'company-admin-list';
        $deleteUrl = 'admin-patient-medicine-delete';
        $perpage = config('app.perpage');
        $breadcrumbs = [
            ['link' => "modern", 'name' => "Home"], ['link' => "javascript:void(0)", 'name' => __('locale.patient_medicine')], ['name' =>__('locale.List')]];
        //Pageheader set true for breadcrumbs
        $pageConfigs = ['pageHeader' => true];
        $pageTitle = 'Patient medicine';
        // $usersResult = User::whereHas(
        //     'role', function($q){
        //         $q->where('name', 'company-admin');
        //     }
        // )->select(['id','name','email','phone','address1','image','website_url','blocked'])->orderBy('id','DESC');
        $medicineResult = Patient_medicine::with(['patientname','medicine','carehome'])->select(['id','medicine_code','patient_code','c_home_code','remark','updated_by_user','doses'])->orderBy('id','DESC');

       // echo"<pre>";print_r($medicineResult);die;
        if(auth()->user()->role()->first()->name=="Admin")
        {
           // echo"admin login";
            $medicineResult = Patient_medicine::with(['patientname','medicine','carehome'])->whereHas('carehome', function ($company_q) {
                $company_q->where('id', '=',auth()->user()->company()->first()->id);})->select(['id','medicine_code','patient_code','c_home_code','remark','updated_by_user','doses'])->orderBy('id','desc');
        }
        //echo"<pre>";print_r($medicineResult);die;
        //$deceaseResult = Decease::select(['id','code','name'])->get();
        $editUrl = 'admin-patient-medicine-edit';
        if($request->ajax()){
            $medicineResult = $medicineResult->whereHas('medicine', function($q)use($request){
                $q->where('id', 'like', '%'.$request->seach_term.'%')
                            ->orWhere('medicine_name', 'like', '%'.$request->seach_term.'%');
                         }) ->paginate($perpage);
                         $perPage = $perpage;
                         $page = $medicineResult->currentPage();
                        
            return view('pages.patient-medicine.patient-medicine-list-ajax', compact('medicineResult','editUrl','deleteUrl','page','perPage'))->render();
        }

        $medicineResult = $medicineResult->paginate($perpage);
        $perPage = $perpage;
        $page = $medicineResult->currentPage();
        
        return view('pages.patient-medicine.patient-medicine-list', ['pageConfigs' => $pageConfigs], ['breadcrumbs' => $breadcrumbs,'medicineResult'=>$medicineResult,'pageTitle'=>$pageTitle,'userType'=>$userType,'editUrl'=>$editUrl,'deleteUrl'=>$deleteUrl,'page'=>$page,'perPage'=>$perPage]);
    }

    public function createPatientmedicine($id='')
    {
       // echo"ji";die;
        $formUrl = 'admin-patient-medicine-create';
        $breadcrumbs = [
            ['link' => "/", 'name' => "Home"], ['link' => "admin/patient-medicine-list", 'name' => __("locale.create_patient_medicine")], ['name' => (($id!='') ? __('locale.Edit') : __('locale.Create') )],
        ];
        //Pageheader set true for breadcrumbs
        $medicineResponse='';
        $companyCode = Helper::setNumber();
        $medicineResult = Medicine::select(['id','medicine_name'])->get();
        $patientResult = User::select(['id','code','name'])->where('typeselect','=','Patient')->get();
        $companies = Company::get(["company_name", "id","company_code"]);
        if($id!=''){
            //$permission_arr = [];
            $medicineResponse = Patient_medicine::find($id);
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
            $formUrl = 'admin-patient-medicine-update';
        }
        
        // dd($deceaseResult);
        $pageConfigs = ['pageHeader' => true];
        $pageTitle = __('locale.patient_medicine');
        return view('pages.patient-medicine.patient-medicine-create',['breadcrumbs' => $breadcrumbs], ['pageConfigs' => $pageConfigs,'pageTitle'=>$pageTitle,'medicineResult'=>$medicineResult,'medicineResponse'=>$medicineResponse,'patientResult'=>$patientResult,'companies'=>$companies,'formUrl'=>$formUrl]);
    }

    public function storePatientmedicine(Request $request)
    {
        //echo"<pre>";print_r($request->all());die;
       // echo"store";die;
        $validator = Validator::make($request->all(), [
            'medicine_code' => 'required',
            "patient_code"=> "required",
            "c_home_code"=> "required",
        ]);
        if ($validator->fails()) {
            return redirect()->back()
            ->withErrors($validator)
            ->withInput();
        }
        unset($request['_token']);
        Patient_medicine::create($request->all());
      
        return redirect()->route('admin.patient-medicine-list')->with('success',__('locale.patient_medicine_created_successfully'));
    }

    public function updatePatientmedicine(Request $request,$id)
    {
        //echo"on update";die;
        $validator = Validator::make($request->all(), [
            'medicine_code' => 'required',
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
        $patient_disease = Patient_medicine::where('id',$id)->update($request->all());
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
        return redirect()->route('admin.patient-medicine-list')->with('success',__('locale.patient_medicine_update'));
    }

    public function destroyPatientmedicine($id)
    {   
         if(Patient_medicine::where('id',$id)->delete()){
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
