<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Models\{Country, State, City};
use App\Models\{User,Role,Decease,Inventory};
use App\Models\Company;
use App\Models\CompanyUserMapping;
use App\Imports\UsersImport;
use App\Exports\UsersExport;
use App\Exports\AdminExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Permission;
use Helper;


class InventoryController extends Controller
{
    public function index(Request $request)
    {
        //echo"index inventory";die;
        $userType = auth()->user()->role()->first()->name;
        $listUrl = 'company-admin-list';
        if($userType=="Admin")
        {
            $listUrl = 'admin-inventory-list';
        }
        $deleteUrl = 'inventory-delete';
        if($userType=="Admin")
        {
            $deleteUrl = 'admin-inventory-delete';
        }
        $perpage = config('app.perpage');
        $breadcrumbs = [
            ['link' => "modern", 'name' => "Home"], ['link' => "javascript:void(0)", 'name' => __('locale.inventory')], ['name' => __('locale.inventory').__('locale.List')]];
        //Pageheader set true for breadcrumbs
        $pageConfigs = ['pageHeader' => true];
        $pageTitle = 'Inventory master';
        // $usersResult = User::whereHas(
        //     'role', function($q){
        //         $q->where('name', 'company-admin');
        //     }
        // )->select(['id','name','email','phone','address1','image','website_url','blocked'])->orderBy('id','DESC');
        $inventoryResult = Inventory::select(['id','code','name','type','option'])->orderBy('id','DESC');
        if(auth()->user()->role()->first()->name=="Admin")
        {
          $inventoryResult = Inventory::select(['id','code','name','type','option'])->orderBy('id','DESC');

        }
        $editUrl = 'inventory-edit';
        if($userType=="Admin")
        {
            $editUrl = 'admin-inventory-edit';
        }
        if($request->ajax()){
            $inventoryResult = $inventoryResult->when($request->seach_term, function($q)use($request){
                $q->where('id', 'like', '%'.$request->seach_term.'%')
                            ->orWhere('name', 'like', '%'.$request->seach_term.'%')
                            ->orWhere('type', 'like', '%'.$request->seach_term.'%');
                           
                        }) ->paginate($perpage);
            $perPage = $perpage;
            $page = $inventoryResult->currentPage();
                        
            return view('pages.inventory.inventory-list-ajax', compact('inventoryResult','editUrl','deleteUrl','page','perPage'))->render();
        }

        $inventoryResult = $inventoryResult->paginate($perpage);
        $perPage = $perpage;
        $page = $inventoryResult->currentPage();
        
        return view('pages.inventory.inventory-list', ['pageConfigs' => $pageConfigs], ['breadcrumbs' => $breadcrumbs,'inventoryResult'=>$inventoryResult,'pageTitle'=>$pageTitle,'userType'=>$userType,'editUrl'=>$editUrl,'deleteUrl'=>$deleteUrl,'page'=>$page,'perPage'=>$perPage]);
    }

    public function create($id='')
    {
       // echo"hi inventory";die;
        $userType = auth()->user()->role()->first()->name;
        $formUrl = 'inventory-create';
        $inventoryResult=$states=$cities=false;
        $breadcrumbs = [
            ['link' => "modern", 'name' => "Home"], ['link' => "javascript:void(0)", 'name' => __('locale.inventory')], ['name' => (($id!='') ? __('locale.Edit') : __('locale.Create') )]];
        //Pageheader set true for breadcrumbs
        $pageConfigs = ['pageHeader' => true];
        $countries = Country::get(["name", "id"]);
        $companies = Company::get(["company_name", "id","company_code"]);
        $roles=Role::get(["id","name"]);
        $companyCode = Helper::setNumber();
        $pageTitle = __('locale.inventory'); 
        if($id!=''){
            //$permission_arr = [];
            $inventoryResult = Inventory::find($id);
            if($userType=="superadmin")
            {
                $formUrl = 'inventory-update';
            }
            if($userType=="Admin")
            {
            $formUrl = 'admin-inventory-update';
            }
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
            
        }
        // dd($user_result);
        return view('pages.inventory.inventory-create', ['pageConfigs' => $pageConfigs], ['breadcrumbs' => $breadcrumbs,'countries'=>$countries,'pageTitle'=>$pageTitle,'companies'=>$companies,'inventoryResult'=>$inventoryResult,'states'=>$states,'cities'=>$cities,'userType'=>$userType,'formUrl'=>$formUrl,'companyCode'=>$companyCode,'roles'=>$roles]);
    }

    public function store(Request $request){
        
       // echo '<pre>';print_r($request->all()); exit();

        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:inventory|max:250',
            'type' => 'max:250',
            'code'=>'unique:inventory',
            
            
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
            ->withErrors($validator)
            ->withInput();
        }

        // $role = Role::where('name','=',$request['typeselect'])->first();
        // $random_password = Str::random(6);
        // $request['password'] = Hash::make($random_password);
        $inventory = Inventory::create($request->all());
        
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
            $backUrl='inventory-list';
        }
        if(auth()->user()->role()->first()->name=="Admin")
        {
            $backUrl='admin-inventory-list';
        }
        
        return redirect()->route($backUrl)->with('success',__('locale.inventory_created_successfully'));
    }

    public function update(Request $request,$id)
    {
       // echo"hi update";die;
        $userType = auth()->user()->role()->first()->name;
        $listUrl = 'superadmin.product-subcategory.index';
        if($userType!=config('custom.superadminrole')){
            $listUrl = 'product-subcategory.index';
        }
        $inventory = Inventory::find($id);
        if ($request->has('code') && $request->input('code') != $inventory->code) {
            $inventory->code = $request->input('code');
        }
        
        // echo '<pre>'; print_r($request->all()); die;
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:250',
            'type' => 'max:250',
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
       $inventory = Inventory::where('id', $id)->update([
        'name' => $request->input('name'),
        'type' => $request->input('type'),
        'option'=>$request->input('option'),
        'code'=>$inventory->code,
        // Add other columns and values as needed
    ]);
    if(auth()->user()->role()->first()->name=="superadmin")
        {
            $backUrl='inventory-list';
        }
        if(auth()->user()->role()->first()->name=="Admin")
        {
            $backUrl='admin-inventory-list';
        }
    
    // Check if the update was successful
    return redirect()->route($backUrl)->with('success', __('locale.inventory_updated_successfully'));
    
    
    }
    
    public function destroy($id)
    {   
        //echo"delete";die;
        if(Inventory::where('id',$id)->delete()){
            return redirect()->back()->with('success',__('locale.delete_message'));
        }else{
            return redirect()->back()->with('error',__('locale.try_again'));
        }
    }

}
 
 ?>