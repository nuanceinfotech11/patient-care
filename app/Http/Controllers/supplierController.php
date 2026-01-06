<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Models\{Country, State, City,Usermeta,Supplier};
use App\Models\{User,Role};
use App\Models\Company;
use App\Models\CompanyUserMapping;
use App\Imports\UsersImport;
use App\Exports\UsersExport;
use App\Exports\AdminExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Permission;
use App\Http\Middleware\Admin;
use Helper;

class supplierController extends Controller
{
    public function index(Request $request)
    {
        // echo" supplier index";die;
        $userType = auth()->user()->role()->first()->name;
       
        $paginationUrl = 'supplier-list';
        $listUrl='supplier-list';
        if($userType=="Admin")
        {
            $listUrl = 'admin-supplier-list';
        }
       
        
        $deleteUrl = 'supplier-delete';
        if($userType=="Admin")
        {
            $deleteUrl = 'admin-supplier-delete';
        }
        $perpage = config('app.perpage');
        $breadcrumbs = [
            ['link' => "modern", 'name' => "Home"], ['link' => "javascript:void(0)", 'name' => __('locale.supplier')], ['name' => __('locale.supplier').__('locale.list')]];
        //Pageheader set true for breadcrumbs
        $pageConfigs = ['pageHeader' => true];
        $pageTitle = 'Supplier master';
        // $usersResult = User::whereHas(
        //     'role', function($q){
        //         $q->where('name', 'company-admin');
        //     }
        // )->select(['id','name','email','phone','address1','image','website_url','blocked'])->orderBy('id','DESC');
        $supplierResult = Supplier::with(['countryname','statename', 'cityname'])->select(['id','code','name','email','phone','address1','country','state','city','zipcode'])->orderBy('id','DESC');
        if(auth()->user()->role()->first()->name=="Admin")
        {
            $supplierResult = Supplier::with(['countryname','statename', 'cityname'])->select(['id','code','name','email','phone','address1','country','state','city','zipcode'])->orderBy('id','DESC');

        }
        $editUrl = 'supplier-edit';
        if($userType=="Admin")
        {
            $editUrl = 'admin-supplier-edit';
        }
        if($request->ajax()){
            $supplierResult = $supplierResult->when($request->seach_term, function($q)use($request){
                $q->where('id', 'like', '%'.$request->seach_term.'%')
                            ->orWhere('name', 'like', '%'.$request->seach_term.'%')
                            ->orWhere('email', 'like', '%'.$request->seach_term.'%')
                            ->orWhere('phone', 'like', '%'.$request->seach_term.'%')
                            ->orWhere('address1', 'like', '%'.$request->seach_term.'%');
                        })
                        // ->when($request->status, function($q)use($request){
                        //     $q->where('users.blocked',$request->status);
                        // })
                        ->paginate($perpage);
                        $perPage = $perpage;
                        $page = $supplierResult->currentPage();
                        
            return view('pages.supplier.supplier-list-ajax', compact('supplierResult','editUrl','deleteUrl','page','perPage'))->render();
        }

        $supplierResult = $supplierResult->paginate($perpage);
        $perPage = $perpage;
        $page = $supplierResult->currentPage();
       // echo $editUrl;die;
        return view('pages.supplier.supplier-list', ['pageConfigs' => $pageConfigs], ['breadcrumbs' => $breadcrumbs,'supplierResult'=>$supplierResult,'pageTitle'=>$pageTitle,'userType'=>$userType,'editUrl'=>$editUrl,'deleteUrl'=>$deleteUrl,'paginationUrl'=>$paginationUrl,'page'=>$page,'perPage'=>$perPage]);
    }
    public function create($id='')
    {
        //echo"hi supplier create";die;
        $userType = auth()->user()->role()->first()->name;
        $formUrl = 'supplier-create';
        $user_result=$states=$cities=$supplierResult=false;
        $breadcrumbs = [
            ['link' => "modern", 'name' => "Home"], ['link' => "javascript:void(0)", 'name' => __('locale.supplier')], ['name' => (($id!='') ? __('locale.Edit') : __('locale.Create') )]];
        //Pageheader set true for breadcrumbs
        $pageConfigs = ['pageHeader' => true];
        $countries = Country::get(["name", "id"]);
        // $states = State::where('country_id',$company_result->country)->get(["name", "id"]);
        // $cities = City::where('state_id',$company_result->state)->get(["name", "id"]);
        $companies = Company::get(["company_name", "id","company_code"]);
        $roles=Role::where('name','!=','superadmin')->get(["id","name"]);
        $companyCode = Helper::setNumber();
        $pageTitle = __('locale.supplier'); 
        if($id!=''){
            $permission_arr = [];
            $supplierResult = Supplier::find($id);
            $states = State::where('country_id',$supplierResult->country)->get(["name", "id"]);
            $cities = City::where('state_id',$supplierResult->state)->get(["name", "id"]);
            // if($user_result->permission->count()>0){
            //     foreach($user_result->permission as $permission_val){
            //         $permission_arr[$permission_val->name][] = $permission_val->guard_name;
            //     }
            // }
            // $user_result->permission = $permission_arr;
            // // echo '<pre>';print_r($user_result);exit();
            // if($user_result){
            // $states = State::where('country_id',$user_result->country)->get(["name", "id"]);
            // $cities = City::where('state_id',$user_result->state)->get(["name", "id"]);
            // }
            if($userType=="superadmin")
            {
                $formUrl = 'supplier-update';
            }
            if($userType=="Admin")
            {
            $formUrl = 'admin-supplier-update';
            }
            
        }
        //dd($states);
        return view('pages.supplier.supplier-create', ['pageConfigs' => $pageConfigs], ['breadcrumbs' => $breadcrumbs,'countries'=>$countries,'pageTitle'=>$pageTitle,'companies'=>$companies,'user_result'=>$user_result,'states'=>$states,'cities'=>$cities,'userType'=>$userType,'formUrl'=>$formUrl,'companyCode'=>$companyCode,'roles'=>$roles,'supplierResult'=>$supplierResult]);
    }
    
    
    public function store(Request $request){
        
        
       //echo '<pre>';print_r($request->all()); exit();
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:250',
            //'password2'=>'required|max:250',
            'email' => 'required|unique:users|max:250',
           // 'code'=>'required|unique:users',
            'phone' => 'required',
            'address' => 'max:250',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
            ->withErrors($validator)
            ->withInput();
        }

        $role = Role::where('name','=',$request['typeselect'])->first();
        // $random_password = Str::random(6);
        $random_password ='123456';
        $request['password'] = Hash::make($random_password);
        
// Insert $date into the database
        
        $supplier = Supplier::create($request->all());
        
       // $id = $user->id;
        //echo $role->id;die;
       // $user->company()->attach($request->company);
       // $user->role()->attach($role->id);
        if($request->has('permission_allow')){
            $i=0;
            $permissionInsert = [];
            foreach($request->input('permission_allow') as $key => $permissionVal){
                // echo '<pre>';print_r($permissionVal['guard_name']);
                if(isset($permissionVal['guard_name'])){
                    for($g=0;$g<count($permissionVal['guard_name']);$g++){
                        $permissionInsert[$i]['user_id'] = $id;
                        $permissionInsert[$i]['name'] = $key;
                        $permissionInsert[$i]['guard_name'] = $permissionVal['guard_name'][$g];
                        $i++;
                    }
                }
            }
            if(!empty($permissionInsert)){
                Permission::where('user_id',$user->id)->delete();
                Permission::insert($permissionInsert);
            }
        }
        if(auth()->user()->role()->first()->name=="superadmin")
        {
            $backUrl='supplier-list';
        }
        if(auth()->user()->role()->first()->name=="Admin")
        {
            $backUrl='admin-supplier-list';
        }
        
        return redirect()->route($backUrl)->with('success',__('locale.supplier_created_successfully'));
    }

    public function update(Request $request, $id){
        
        //$backurl='';
        $userType = auth()->user()->role()->first()->name;
        $listUrl = 'superadmin.product-subcategory.index';
        if($userType!=config('custom.superadminrole')){
            $listUrl = 'product-subcategory.index';
        }
        
        // echo '<pre>'; print_r($request->all()); die;
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:250',
            'email' => 'required|max:250',
           // 'code'=>'required|unique:users',
            'phone' => 'required|max:10',
            'address' => 'max:250',
            
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
            ->withErrors($validator)
            ->withInput();
        }
        if(User::where('email',$request->email)->where('id','!=',$id)->count()>0){
            return redirect()->back()
            ->with('email','The Email Has Already Been Taken.')
            ->withInput();
        }
        
        unset($request['_method']);
        unset($request['_token']);
        unset($request['action']);
        unset($request['company']);
        unset($request['importcompany']);
        unset($request['company_code']);
        
        // dd($request->input('permission_allow'));
        if ($request->has('permission_allow')) {
            Permission::where('user_id',$id)->delete();
            foreach ($request->input('permission_allow') as $key => $permissionVal) {
                //echo '<pre>'; print_r($permissionVal['guard_name']); die;  

                if (isset($permissionVal['guard_name'])) {
                    $guardNames = $permissionVal['guard_name'];
        
                    foreach ($guardNames as $guardName) {
                        Permission::updateOrInsert(
                            [
                                'name' => $key,
                                'guard_name' => $guardName,
                                'user_id' => $id,
                            ]
                          
                        );
                    }
                }
            }
        }else{
            Permission::where('user_id',$id)->delete();
        }
        
        unset($request['permission_allow']);
        if(isset($request['password']) && $request['password']!=''){
            $request['password'] = Hash::make($request['password']);
        }else{
            unset($request['password']);
        }

        $supplier =Supplier::where('id',$id)->update($request->all());

        //$backurl = 'superadmin.'.strtolower($request->typeselect).'-list';
        // superadmin.paitent-list
        // exit();
        //$backurl='';
        if(auth()->user()->role()->first()->name=="superadmin")
        {
            $backUrl='supplier-list';
            return redirect()->route($backUrl)->with('success',__('locale.supplier_update_success'));
        }
        if(auth()->user()->role()->first()->name=="Admin")
        {
            $backUrl='admin-supplier-list';
            return redirect()->route($backUrl)->with('success',__('locale.supplier_update_success'));
        }
    
    // Check if the update was successful
    
        //return redirect()->route($backurl)->with('success',__('locale.supplier_update_success'));
    }

    public function destroy($id)
    {   
         if(Supplier::where('id',$id)->delete()){
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
