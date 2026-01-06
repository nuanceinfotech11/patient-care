<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Models\{Country, State, City};
use App\Models\{User,Role,Decease,Inventory,Medicine};
use App\Models\Company;
use App\Models\CompanyUserMapping;
use App\Imports\UsersImport;
use App\Exports\UsersExport;
use App\Exports\AdminExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Permission;
use Helper;

class MedicineController extends Controller
{
    
    public function index(Request $request)
    {
        //echo"index medicine";die;
        $userType = auth()->user()->role()->first()->name;
        $listUrl = 'company-admin-list';
        $listUrl='medicine-list';
        if($userType=="Admin")
        {
            $listUrl = 'admin-medicine-list';
        }
        if($userType=="Manager")
        {
            $listUrl = 'manager-medicine-list';
        }
        $deleteUrl = 'medicine-delete';
        if($userType=="Admin")
        {
            $deleteUrl = 'admin-medicine-delete';
        }
        if($userType=="Manager")
        {
            $deleteUrl = 'manager-medicine-delete';
        }
        $perpage = config('app.perpage');
        $breadcrumbs = [
            ['link' => "modern", 'name' => "Home"], ['link' => "javascript:void(0)", 'name' => __('locale.medicine')], ['name' => __('locale.medicine').__('locale.List')]];
        //Pageheader set true for breadcrumbs
        $pageConfigs = ['pageHeader' => true];
        $pageTitle = 'Medicine master';
        // $usersResult = User::whereHas(
        //     'role', function($q){
        //         $q->where('name', 'company-admin');
        //     }
        // )->select(['id','name','email','phone','address1','image','website_url','blocked'])->orderBy('id','DESC');
        $medicineResult = Medicine::select(['id','medicine_name','quantity','company'])->orderBy('id','DESC');
       
        if(auth()->user()->role()->first()->name=="Admin")
        {
          $medicineResult = Medicine::with('comp')->whereHas('comp', function ($company_q) {
            $company_q->where('id', '=',auth()->user()->company()->first()->id);})->select(['id','medicine_name','quantity','company'])->orderBy('id','DESC');
        }
        if(auth()->user()->role()->first()->name=="Manager")
        {
          $medicineResult = Medicine::with('comp')->whereHas('comp', function ($company_q) {
            $company_q->where('id', '=',auth()->user()->company()->first()->id);})->select(['id','medicine_name','quantity','company'])->orderBy('id','DESC');
        }
       
       
        $editUrl = 'medicine-edit';
        if($userType=="Admin")
        {
            $editUrl = 'admin-medicine-edit';
        }
        if($userType=="Manager")
        {
            $editUrl = 'manager-medicine-edit';
        }
        if($request->ajax()){
            $medicineResult = $medicineResult->when($request->seach_term, function($q)use($request){
                $q->where('id', 'like', '%'.$request->seach_term.'%')
                            ->orWhere('medicine_name', 'like', '%'.$request->seach_term.'%');
                        }) ->paginate($perpage);
                        $perPage = $perpage;
                        $page = $medicineResult->currentPage();
                        
            return view('pages.medicine.medicine-list-ajax', compact('medicineResult','editUrl','deleteUrl','page','perPage'))->render();
        }

        $medicineResult = $medicineResult->paginate($perpage);
        $perPage = $perpage;
        $page = $medicineResult->currentPage();
        
        return view('pages.medicine.medicine-list', ['pageConfigs' => $pageConfigs], ['breadcrumbs' => $breadcrumbs,'medicineResult'=>$medicineResult,'pageTitle'=>$pageTitle,'userType'=>$userType,'editUrl'=>$editUrl,'deleteUrl'=>$deleteUrl,'page'=>$page,'perPage'=>$perPage]);
    }
    public function create($id='')
    {
        //echo"hi med";die;
        $userType = auth()->user()->role()->first()->name;
        $formUrl = 'medicine-create';
        $medicineResult=$states=$cities=false;
        $breadcrumbs = [
            ['link' => "modern", 'name' => "Home"], ['link' => "javascript:void(0)", 'name' => __('locale.medicine')], ['name' => (($id!='') ? __('locale.Edit') : __('locale.Create') )]];
        //Pageheader set true for breadcrumbs
        $pageConfigs = ['pageHeader' => true];
        $countries = Country::get(["name", "id"]);
        $companies = Company::get(["company_name", "id","company_code"]);
        $roles=Role::get(["id","name"]);
        $companyCode = Helper::setNumber();
        $pageTitle = __('locale.medicine'); 
        if($id!=''){
            //$permission_arr = [];
            $medicineResult = Medicine::find($id);
            // if($user_result->permission->count()>0){
            //     foreach($user_result->permission as $permission_val){
            //         $permission_arr[$permission_val->name][] = $permission_val->guard_name;
            //     }
            // }
           // $user_result->permission = $permission_arr;
            // echo '<pre>';print_r($user_result);exit();
           // if($user_result){
            //$states = State::where('country_id',$user_result->country)->get(["name", "id"]);
           // $cities = City::where('state_id',$user_result->state)->get(["name", "id"]);
           // }
           //$formUrl = 'medicine-update';
           if($userType=="superadmin")
            {
                $formUrl = 'medicine-update';
            }
            if($userType=="Admin")
            {
                $formUrl = 'admin-medicine-update';
            }
            if($userType=="Manager")
            {
                $formUrl = 'manager-medicine-update';
            }
        }
        // dd($medicineResult);
        return view('pages.medicine.medicine-create', ['pageConfigs' => $pageConfigs], ['breadcrumbs' => $breadcrumbs,'countries'=>$countries,'pageTitle'=>$pageTitle,'companies'=>$companies,'medicineResult'=>$medicineResult,'states'=>$states,'cities'=>$cities,'userType'=>$userType,'formUrl'=>$formUrl,'companyCode'=>$companyCode,'roles'=>$roles]);
    }
    public function store(Request $request){
        
//echo '<pre>';print_r($request->all()); exit();
 
         $validator = Validator::make($request->all(), [
             'medicine_name' => 'required|max:250',
        ]);
         
         if ($validator->fails()) {
             return redirect()->back()
             ->withErrors($validator)
             ->withInput();
         }
 
         // $role = Role::where('name','=',$request['typeselect'])->first();
         // $random_password = Str::random(6);
         // $request['password'] = Hash::make($random_password);
         $medicine = Medicine::create($request->all());
         
        // $id = $user->id;
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
         if(auth()->user()->role()->first()->name=="superadmin")
         {
             $backUrl='medicine-list';
         }
         if(auth()->user()->role()->first()->name=="Admin")
         {
             $backUrl='admin-medicine-list';
         }
         if(auth()->user()->role()->first()->name=="Manager")
         {
             $backUrl='manager-medicine-list';
         }
         
         return redirect()->route($backUrl)->with('success',__('locale.medicine_created_successfully'));
     }
 
     public function update(Request $request,$id)
     {
        // echo"hi update";die;
         $userType = auth()->user()->role()->first()->name;
         $listUrl = 'superadmin.product-subcategory.index';
         if($userType!=config('custom.superadminrole')){
             $listUrl = 'product-subcategory.index';
         }
        //  $inventory = Inventory::find($id);
        //  if ($request->has('code') && $request->input('code') != $inventory->code) {
        //      $inventory->code = $request->input('code');
        //  }
         
         // echo '<pre>'; print_r($request->all()); die;
         $validator = Validator::make($request->all(), [
             'medicine_name' => 'required|max:250',
             //'type' => 'max:250',
             //'code'=>'unique:inventory',
         ]);
         
         if ($validator->fails()) {
             return redirect()->back()
             ->withErrors($validator)
             ->withInput();
         }
         // if(User::where('email',$request->email)->where('id','!=',$id)->count()>0){
         //     return redirect()->back()
         //     ->with('email','The Email Has Already Been Taken.')
         //     ->withInput();
         // }
         
          unset($request['_method']);
          unset($request['_token']);
          unset($request['action']);
         // unset($request['company']);
         // unset($request['importcompany']);
         
         // dd($request->input('permission_allow'));
         // if ($request->has('permission_allow')) {
         //     Permission::where('user_id',$id)->delete();
         //     foreach ($request->input('permission_allow') as $key => $permissionVal) {
         //         //echo '<pre>'; print_r($permissionVal['guard_name']); die;  
 
         //         if (isset($permissionVal['guard_name'])) {
         //             $guardNames = $permissionVal['guard_name'];
         
         //             foreach ($guardNames as $guardName) {
         //                 Permission::updateOrInsert(
         //                     [
         //                         'name' => $key,
         //                         'guard_name' => $guardName,
         //                         'user_id' => $id,
         //                     ]
                           
         //                 );
         //             }
         //         }
         //     }
         // }else{
         //     Permission::where('user_id',$id)->delete();
         // }
         
         // unset($request['permission_allow']);
         // if(isset($request['password']) && $request['password']!=''){
         //     $request['password'] = Hash::make($request['password']);
         // }else{
         //     unset($request['password']);
         // }
 
         //$decease = Decease::where('id',$id)->update($request->all());
 
        // return redirect()->route('decease-list')->with('success',__('locale.company_admin_update_success'));
        $medicine = Medicine::where('id', $id)->update([
         'medicine_name' => $request->input('medicine_name'),
         'company'=>$request->input('company'),
         'quantity'=>$request->input('quantity'),
        //  'type' => $request->input('type'),
        //  'option'=>$request->input('option'),
        //  'code'=>$inventory->code,
         // Add other columns and values as needed
     ]);

     if(auth()->user()->role()->first()->name=="superadmin")
     {
         $backUrl='medicine-list';
     }
     if(auth()->user()->role()->first()->name=="Admin")
     {
         $backUrl='admin-medicine-list';
     }
     if(auth()->user()->role()->first()->name=="Manager")
     {
         $backUrl='manager-medicine-list';
     }
     
     return redirect()->route($backUrl)->with('success', __('locale.medicine_update_success'));

    }
     
     public function destroy($id)
     {   
         //echo"delete";die;
         if(Medicine::where('id',$id)->delete()){
             return redirect()->back()->with('success',__('locale.delete_message'));
         }else{
             return redirect()->back()->with('error',__('locale.try_again'));
         }
     }
}
