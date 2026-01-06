<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Models\{Country, State, City};
use App\Models\{User,Role,Decease};
use App\Models\Company;
use App\Models\CompanyUserMapping;
use App\Imports\UsersImport;
use App\Exports\UsersExport;
use App\Exports\AdminExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Permission;
use Helper;


class DeceaseController extends Controller
{
    public function index(Request $request)
    {
        //echo"index";die;
        $userType = auth()->user()->role()->first()->name;
        $listUrl = 'company-admin-list';
        if($userType=="Admin")
        {
            $listUrl = 'admin-decease-list';
        }
        $deleteUrl = 'decease-delete';
        if($userType=="Admin")
        {
            $deleteUrl = 'admin-decease-delete';
        }
        $perpage = config('app.perpage');
        $breadcrumbs = [
            ['link' => "modern", 'name' => "Home"], ['link' => "javascript:void(0)", 'name' => __('locale.disease')], ['name' => __('locale.disease').__('locale.List')]];
        //Pageheader set true for breadcrumbs
        $pageConfigs = ['pageHeader' => true];
        $pageTitle = 'Disease master';
        // $usersResult = User::whereHas(
        //     'role', function($q){
        //         $q->where('name', 'company-admin');
        //     }
        // )->select(['id','name','email','phone','address1','image','website_url','blocked'])->orderBy('id','DESC');
        $deceaseResult = Decease::select(['id','code','name','symptoms','note'])->orderBy('id','DESC');
        if(auth()->user()->role()->first()->name=="Admin")
        {
            $deceaseResult=Decease::select(['id','code','name','symptoms','note'])->orderBy('id','DESC');
        }
        $editUrl = 'decease-edit';
        if($userType=="Admin")
        {
            $editUrl = 'admin-decease-edit';
        }
        if($request->ajax()){
            $deceaseResult = $deceaseResult->when($request->seach_term, function($q)use($request){
                $q->where('id', 'like', '%'.$request->seach_term.'%')
                            ->orWhere('name', 'like', '%'.$request->seach_term.'%')
                            ->orWhere('symptoms', 'like', '%'.$request->seach_term.'%');
                           
                        }) ->paginate($perpage);
                        $perPage = $perpage;
            $page = $deceaseResult->currentPage(); 
                        
            return view('pages.decease.decease-list-ajax', compact('deceaseResult','editUrl','deleteUrl','page','perPage'))->render();
        }

        $deceaseResult = $deceaseResult->paginate($perpage);
        $perPage = $perpage;
        $page = $deceaseResult->currentPage(); 
        
        return view('pages.decease.decease-list', ['pageConfigs' => $pageConfigs], ['breadcrumbs' => $breadcrumbs,'deceaseResult'=>$deceaseResult,'pageTitle'=>$pageTitle,'userType'=>$userType,'editUrl'=>$editUrl,'deleteUrl'=>$deleteUrl,'page'=>$page,'perPage'=>$perPage]);
    }

    public function create($id='')
    {
        //echo"hi des";die;
        $userType = auth()->user()->role()->first()->name;
        $formUrl = 'decease-create';
        $deceaseResult=$states=$cities=false;
        $breadcrumbs = [
            ['link' => "modern", 'name' => "Home"], ['link' => "javascript:void(0)", 'name' => __('locale.disease')], ['name' => (($id!='') ? __('locale.Edit') : __('locale.Create') )]];
        //Pageheader set true for breadcrumbs
        $pageConfigs = ['pageHeader' => true];
        $countries = Country::get(["name", "id"]);
        $companies = Company::get(["company_name", "id","company_code"]);
        $roles=Role::get(["id","name"]);
        $companyCode = Helper::setNumber();
        $pageTitle = __('locale.disease'); 
        if($id!=''){
            //$permission_arr = [];
            $deceaseResult = Decease::find($id);
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
           if($userType=="superadmin")
            {
                $formUrl = 'decease-update';
            }
            if($userType=="Admin")
            {
            $formUrl = 'admin-decease-update';
            }
           // }
           // $formUrl = 'decease-update';
        }
        // dd($user_result);
        return view('pages.decease.decease-create', ['pageConfigs' => $pageConfigs], ['breadcrumbs' => $breadcrumbs,'countries'=>$countries,'pageTitle'=>$pageTitle,'companies'=>$companies,'deceaseResult'=>$deceaseResult,'states'=>$states,'cities'=>$cities,'userType'=>$userType,'formUrl'=>$formUrl,'companyCode'=>$companyCode,'roles'=>$roles]);
    }
    
    
    public function store(Request $request){
        
        //echo '<pre>';print_r($request->all()); exit();

        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:disease|max:250',
            'symptoms' => 'max:250',
            'code'=>'required|unique:disease',
            
            
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
            ->withErrors($validator)
            ->withInput();
        }

        // $role = Role::where('name','=',$request['typeselect'])->first();
        // $random_password = Str::random(6);
        // $request['password'] = Hash::make($random_password);
        $disease = Decease::create($request->all());
        
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
            $backUrl='decease-list';
        }
        if(auth()->user()->role()->first()->name=="Admin")
        {
            $backUrl='admin-decease-list';
        }
        
        return redirect()->route($backUrl)->with('success',__('locale.disease_created_successfully'));
    }

    public function update(Request $request,$id){

        $userType = auth()->user()->role()->first()->name;
        $listUrl = 'superadmin.product-subcategory.index';
        if($userType!=config('custom.superadminrole')){
            $listUrl = 'product-subcategory.index';
        }
        
        $decease = Decease::find($id);
        if ($request->has('code') && $request->input('code') != $decease->code) {
            $decease->code = $request->input('code');
        }

       //die;
        
        // echo '<pre>'; print_r($request->all()); die;
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:250',
            'symptoms' => 'max:250',
            //'code'=>'unique:disease',
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
        // if ($request->has('code') && $request->input('code') != $decease->code) {
        //     $decease->code = $request->input('code');
        // }
        $decease = Decease::where('id', $id)->update([
        'name' => $request->input('name'),
        'symptoms' => $request->input('symptoms'),
        'note'=>$request->input('note'),
        'code'=>$decease->code,
        // Add other columns and values as needed
    ]);
    if(auth()->user()->role()->first()->name=="superadmin")
        {
            $backUrl='decease-list';
        }
        if(auth()->user()->role()->first()->name=="Admin")
        {
            $backUrl='admin-decease-list';
        }
    // Check if the update was successful
    return redirect()->route($backUrl)->with('success', __('locale.disease_update_success'));
    
    
    }

    public function destroy($id)
    {   
        //echo"delete";die;
        if(Decease::where('id',$id)->delete()){
            return redirect()->back()->with('success',__('locale.delete_message'));
        }else{
            return redirect()->back()->with('error',__('locale.try_again'));
        }
    }
}

?>
