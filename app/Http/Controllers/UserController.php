<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Models\{Country, State, City,Usermeta};
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


class UserController extends Controller
{
    function __construct()
    {
        $this->middleware(Admin::class);
        //  $this->middleware('permission:company-user-create|company-user-list|company-user-edit|company-user-delete', ['only' => ['index','show']]);
        //  $this->middleware('permission:company-user-create', ['only' => ['create','store']]);
        //  $this->middleware('permission:product-edit', ['only' => ['edit','update']]);
        //  $this->middleware('permission:product-delete', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        // echo"index";die;
        $userType = auth()->user()->role()->first()->name;
       
        $paginationUrl = 'company-admin-list';
       
        
        $deleteUrl = 'superadmin.company-admin-delete';
        $perpage = config('app.perpage');
        $breadcrumbs = [
            ['link' => "modern", 'name' => "Home"], ['link' => "javascript:void(0)", 'name' => __('locale.Company Admin')], ['name' => __('locale.Company Admin').__('locale.list')]];
        //Pageheader set true for breadcrumbs
        $pageConfigs = ['pageHeader' => true];
        $pageTitle = 'User master';
        // $usersResult = User::whereHas(
        //     'role', function($q){
        //         $q->where('name', 'company-admin');
        //     }
        // )->select(['id','name','email','phone','address1','image','website_url','blocked'])->orderBy('id','DESC');
        $usersResult = User::select(['id','name','email','phone','address1','image','website_url','blocked','typeselect','option_for_block'])->orderBy('id','DESC');
        $editUrl = 'superadmin.company-admin-edit';
        if($request->ajax()){
            $usersResult = $usersResult->when($request->seach_term, function($q)use($request){
                $q->where('id', 'like', '%'.$request->seach_term.'%')
                            ->orWhere('name', 'like', '%'.$request->seach_term.'%')
                            ->orWhere('email', 'like', '%'.$request->seach_term.'%')
                            ->orWhere('phone', 'like', '%'.$request->seach_term.'%')
                            ->orWhere('address', 'like', '%'.$request->seach_term.'%');
                        })
                        ->when($request->status, function($q)use($request){
                            $q->where('users.blocked',$request->status);
                        })
                        ->paginate($perpage);
                        $perPage = $perpage; // Number of items per page
                       $page = $usersResult->currentPage();
                        
            return view('pages.users.users-list-ajax', compact('usersResult','editUrl','deleteUrl','page','perPage'))->render();
        }

        $usersResult = $usersResult->paginate($perpage);
        $perPage = $perpage; // Number of items per page
        $page = $usersResult->currentPage();
        
        return view('pages.users.users-list', ['pageConfigs' => $pageConfigs], ['breadcrumbs' => $breadcrumbs,'usersResult'=>$usersResult,'pageTitle'=>$pageTitle,'userType'=>$userType,'editUrl'=>$editUrl,'deleteUrl'=>$deleteUrl,'paginationUrl'=>$paginationUrl,'page'=>$page,'perPage'=>$perPage]);
    }


    public function create($id='')
    {
        //echo"hi comp admin create";die;
        $userType = auth()->user()->role()->first()->name;
        $formUrl = 'company-admin-create';
        $user_result=$states=$cities=false;
        $breadcrumbs = [
            ['link' => "modern", 'name' => "Home"], ['link' => "javascript:void(0)", 'name' => __('locale.Company Admin')], ['name' => (($id!='') ? __('locale.Edit') : __('locale.Create') )]];
        //Pageheader set true for breadcrumbs
        $pageConfigs = ['pageHeader' => true];
        $countries = Country::get(["name", "id"]);
        $companies = Company::get(["company_name", "id","company_code"]);
        $roles=Role::where('name','!=','superadmin')->get(["id","name"]);
        $companyCode = Helper::setNumber();
        $pageTitle = __('locale.Company Admin'); 
        if($id!=''){
            $permission_arr = [];
            $user_result = User::with(['company','permission'])->find($id);
            if($user_result->permission->count()>0){
                foreach($user_result->permission as $permission_val){
                    $permission_arr[$permission_val->name][] = $permission_val->guard_name;
                }
            }
            $user_result->permission = $permission_arr;
            // echo '<pre>';print_r($user_result);exit();
            if($user_result){
            $states = State::where('country_id',$user_result->country)->get(["name", "id"]);
            $cities = City::where('state_id',$user_result->state)->get(["name", "id"]);
            }
            $formUrl = 'company-admin-update';
        }
         //dd($user_result);
        return view('pages.users.users-create', ['pageConfigs' => $pageConfigs], ['breadcrumbs' => $breadcrumbs,'countries'=>$countries,'pageTitle'=>$pageTitle,'companies'=>$companies,'user_result'=>$user_result,'states'=>$states,'cities'=>$cities,'userType'=>$userType,'formUrl'=>$formUrl,'companyCode'=>$companyCode,'roles'=>$roles]);
    }
    
    
    public function store(Request $request){
        
        
    //echo '<pre>';print_r($request->all()); exit();
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:250',
            'password'=>'required|max:250',
            'email' => 'required|unique:users|max:250',
            'code'=>'required|unique:users',
            'phone' => 'required|max:10',
            'address' => 'max:250',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
            ->withErrors($validator)
            ->withInput();
        }

        $role = Role::where('name','=',$request['typeselect'])->first();
        // $random_password = Str::random(6);
        $random_password = $request['password'] ;
        $request['password2'] = $random_password;
        $request['password'] = Hash::make($random_password);
        
// Insert $date into the database
        
        $user = User::create($request->all());
        
        $id = $user->id;
        //echo $role->id;die;
        $user->company()->attach($request->company);
        $user->role()->attach($role->id);
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
        $backurl = 'superadmin.'.strtolower($request->typeselect).'-list';
        // superadmin.paitent-list
        //echo $backurl;
       // exit();
        return redirect()->route($backurl)->with('success',__('locale.created_successfully'));
        
       // return redirect()->route('company-admin-list')->with('success',__('locale.company_admin_create_success'));
    }

    public function update(Request $request, $id){

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
            $request['password2'] = $request['password'];
            $request['password'] = Hash::make($request['password']);
        }else{
            unset($request['password']);
        }
       // dd($request->all());
        $user = User::where('id',$id)->update($request->all());
       // echo url()->previous();die;
       // return redirect()->route('company-admin-list')->with('success',__('locale.success common update'));
        $backurl = 'superadmin.'.strtolower($request->typeselect).'-list';
        // superadmin.paitent-list
        //echo $backurl;
       // exit();
        return redirect()->route($backurl)->with('success',__('locale.success common update'));
    }

    
    public function companyUserImport()
    {
       // echo"hi";die;
        try{
            $import = new UsersImport;
            Excel::import($import, request()->file('importcompany'));
            //die;
            // print_r($import); exit();
            return redirect()->back()->with('success', __('locale.import_message'));
        }catch(\Maatwebsite\Excel\Validators\ValidationException $e){
            $userType = auth()->user()->role()->first()->name;
            $returnUrl = 'superadmin.company-user-list';
            if($userType!=config('custom.superadminrole')){
                $returnUrl = 'company-user-list';
            }
            return redirect()->route($returnUrl)->with('error', __('locale.try_again'));
        }            
    }

    public function companyUserExport($type='superadmin') 
    {
        //echo"export";die;
        if($type=='superadmin'){
            $companyUser = new AdminExport;
            
        }else{
            $type = 'user';
            $companyUser = new UsersExport;
        }
        //echo"<pre>";print_r($companyUser);die;
        return Excel::download($companyUser, 'company-'.$type.time().'.xlsx');
    }

    


    public function usersList(Request $request)
    {
        //echo"hi";die;
        $userType = auth()->user()->role()->first()->name;
        $deleteUrl = 'superadmin.company-user-delete';
        $perpage = config('app.perpage');
        $breadcrumbs = [
            ['link' => "modern", 'name' => "Home"], ['link' => "javascript:void(0)", 'name' => __('locale.Company User')], ['name' => __('locale.Company User').__('locale.List')]];
        //Pageheader set true for breadcrumbs
        $pageConfigs = ['pageHeader' => true];
        $pageTitle = 'Company User';
        $paginationUrl = 'superadmin.company-user-list';
        $editUrl = 'superadmin.company-user-edit';
        
        if($userType!=config('custom.superadminrole')){
            $paginationUrl = 'superadmin.company-user-list';
            $editUrl = 'company-user-edit';
            $deleteUrl = 'company-user-delete';
        }
        
        $usersResult = User::with('company')->whereHas(
            'role', function($role_q){
                $role_q->where('name', 'company-admin');
            }
        )->select(['name','email','phone','address','image','website_url','id','blocked'])->orderBy('id','DESC');
        // $usersResult = User::with('company')->select(['name','email','phone','address','image','website_url','id','blocked'])->orderBy('id','DESC');
        $currentPage = 1;
        if($request->ajax()){
            
            $currentPage = $request->get('page');
            $usersResult = $usersResult->when($request->seach_term, function($q)use($request){
                $q->where('id', 'like', '%'.$request->seach_term.'%')
                            ->orWhere('name', 'like', '%'.$request->seach_term.'%')
                            ->orWhere('email', 'like', '%'.$request->seach_term.'%')
                            ->orWhere('phone', 'like', '%'.$request->seach_term.'%')
                            ->orWhere('address', 'like', '%'.$request->seach_term.'%');
                        })
                        ->when($request->status, function($q)use($request){
                            $q->where('users.blocked',$request->status);
                        })
                        ->paginate($perpage);
                        
            return view('pages.users.users-list-ajax', compact('usersResult','currentPage','editUrl','deleteUrl'))->render();
        }

        $usersResult = $usersResult->paginate($perpage);
        
        return view('pages.users.users-list', ['pageConfigs' => $pageConfigs], ['breadcrumbs' => $breadcrumbs,'usersResult'=>$usersResult,'pageTitle'=>$pageTitle,'paginationUrl'=>$paginationUrl,'currentPage'=>$currentPage,'userType'=>$userType,'editUrl'=>$editUrl,'deleteUrl'=>$deleteUrl]);
    }

    public function usersCreate($id='')
    {
        //echo"hi for edit";die;
        
        $user_result=$states=$cities=false;
        $userType = auth()->user()->role()->first()->name;
        
        $breadcrumbs = [
            ['link' => "modern", 'name' => "Home"], ['link' => "javascript:void(0)", 'name' => __('locale.Company User')], ['name' => (($id!='') ? __('locale.Edit') : __('locale.Create') )]];
        //Pageheader set true for breadcrumbs
        $pageConfigs = ['pageHeader' => true];
        $formUrl = 'superadmin.company-user-create';
        if($userType!=config('custom.superadminrole')){
            $company_id = Helper::loginUserCompanyId();
            $formUrl = 'company-user-create';
        }
        $countries = Country::get(["name", "id"]);
        $companies = Company::get(["company_name", "id","company_code"]);
        $roles=Role::where('name','!=','superadmin')->get(["id","name"]);
        $pageTitle = __('locale.Company User'); 
        if($id!=''){

            $permission_arr = [];
            $user_result = User::with(['company','permission'])->find($id);
            if($user_result->permission->count()>0){
                foreach($user_result->permission as $permission_val){
                    $permission_arr[$permission_val->name][] = $permission_val->guard_name;
                }
            }
            $user_result->permission = $permission_arr;

            // $user_result = User::with('company')->find($id);
            if($user_result){
                $states = State::where('country_id',$user_result->country)->get(["name", "id"]);
                $cities = City::where('state_id',$user_result->state)->get(["name", "id"]);
            }

            $formUrl = 'superadmin.company-user-update';

            if($userType!=config('custom.superadminrole')){
                $company_id = Helper::loginUserCompanyId();
                $formUrl = 'company-user-update';
            }
        }
   
        return view('pages.users.users-create', ['pageConfigs' => $pageConfigs], ['breadcrumbs' => $breadcrumbs,'countries'=>$countries,'pageTitle'=>$pageTitle,'companies'=>$companies,'user_result'=>$user_result,'states'=>$states,'cities'=>$cities,'formUrl'=>$formUrl,'userType'=>$userType,'roles'=>$roles]);
    }

    public function usersUpdate(Request $request, $id){
       // echo"usersUpdate";die;
       echo"<pre>";print_r($request->all());die;
        $userType = auth()->user()->role()->first()->name;
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:250',
            'email' => 'required|max:250',
            'phone' => 'required|max:20',
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
             
        $listUrl = 'company-admin-list';
        if($userType!=config('custom.superadminrole')){
            $listUrl = 'company-user-list';
        }
        

        //echo $listUrl;die;
    
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
        }
        
        unset($request['permission_allow']);
        if(isset($request['password']) && $request['password']!=''){
            $request['password'] = Hash::make($request['password']);
        }else{
            unset($request['password']);
        }

        $user = User::where('id',$id)->update($request->all());
       // return redirect()->route('')->with('success',__('locale.company_user_update_success'));
        return redirect()->route('superadmin.admin-list')->with('success',__('locale.company_user_update_success'));
    }

    public function userStore(Request $request){
        $userType = auth()->user()->role()->first()->name;
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:250',
            'email' => 'required|unique:users|max:250',
            'phone' => 'required|max:20',
            'address' => 'max:250',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
            ->withErrors($validator)
            ->withInput();
        }
        $role = Role::where('name', 'company-user')->first();
        $random_password = Str::random(6);
        $request['password'] = Hash::make($random_password);
        $user = User::create($request->all());
        $id   = $user->id;
        $user->company()->attach($request->company);
        $user->role()->attach( $role->id);
        $listUrl = 'superadmin.company-user-list';
        if($userType!=config('custom.superadminrole')){
            
            $listUrl = 'company-user-list';
        }
        
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

        return redirect()->route($listUrl)->with('success',__('locale.company_user_create_success'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {   
         if(User::where('id',$id)->delete()){
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

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroyUser($id)
    {   
        if(User::where('id',$id)->delete()){
            return redirect()->back()->with('success',__('locale.delete_message'));
        }else{
            return redirect()->back()->with('error',__('locale.try_again'));
        }
    }

    public function patientList(Request $request)
    {
        //echo"patient list";die;
        //$paginationUrl = 'company-admin-list';
        $paginationUrl='';
        $userType = auth()->user()->role()->first()->name;
        $deleteUrl = 'superadmin.company-user-delete';
        if(auth()->user()->role()->first()->name=="Admin"){
            $deleteUrl = 'admin-patient-delete';
        }
        
        $perpage = config('app.perpage');
        $breadcrumbs = [
            ['link' => "modern", 'name' => "Home"], ['link' => "javascript:void(0)", 'name' => __('locale.patient')], ['name' => __('locale.patient').__('locale.List')]];
        //Pageheader set true for breadcrumbs
        $pageConfigs = ['pageHeader' => true];
        $pageTitle = 'Patient list';
        if(auth()->user()->role()->first()->name=="superadmin"){
        $paginationUrl = 'superadmin.patient-list';
        }
        
        $editUrl = 'superadmin.company-admin-edit';
        if(auth()->user()->role()->first()->name=="Admin"){
            $editUrl = 'admin-patient-edit';
        }
        
        $patientResult=User::with('company')->whereHas('role',function($role_q){
            $role_q->where('name','Patient');
        })->select(['name','email','phone','address1','image','website_url','id','blocked'])->orderBy('id','DESC');
        if (auth()->user()->role()->first()->name == "Admin") {
            
            $userTypeid =auth()->user()->company()->first()->id; // Assuming 'company_id' is the field on the User model
        
            $patientResult = User::with('company')->whereHas('company', function ($company_q) {
                            $company_q->where('id', '=',auth()->user()->company()->first()->id)->where('typeselect','=','Patient');
                        })->select(['name', 'email', 'phone', 'address1', 'image', 'website_url', 'id', 'blocked','option_for_block'])->orderBy('id', 'DESC');
                        // Rest of your code...
            }
                    
            if($request->ajax()){
                
                // $currentPage = $request->get('page');
                //    $patientResult = $patientResult->whereHas('company', function($q)use($request){
                //         $q->where('id', 'like', '%'.$request->seach_term.'%')
                //                     ->orWhere('name', 'like', '%'.$request->seach_term.'%')
                //                     ->orWhere('email', 'like', '%'.$request->seach_term.'%')
                //                     ->orWhere('phone', 'like', '%'.$request->seach_term.'%')
                //                     ->orWhere('address', 'like', '%'.$request->seach_term.'%');
                //                 })->paginate($perpage);
                
                //     return view('pages.paitent.paitent-list-ajax', compact('patientResult','editUrl','deleteUrl'))->render();
                // echo $patientResult->toSql(); exit();
                $patientResult = $patientResult->where(function($q) use($request){
                    $q->where('id', 'like', '%'.$request->seach_term.'%')
                    ->orWhere('name', 'like', '%'.$request->seach_term.'%')
                    ->orWhere('email', 'like', '%'.$request->seach_term.'%')
                    ->orWhere('phone', 'like', '%'.$request->seach_term.'%')
                    ->orWhere('address', 'like', '%'.$request->seach_term.'%');
                })->paginate($perpage);
                $perPage = $perpage; // Number of items per page
                $page = $patientResult->currentPage();
        
                return view('pages.paitent.paitent-list-ajax', compact('patientResult','editUrl','deleteUrl','page','perPage'))->render();
            }
            $patientResult = $patientResult->paginate($perpage);
            $perPage = $perpage; // Number of items per page
            $page = $patientResult->currentPage();
       // echo"<pre>";print_r($patientResult);die;
        // echo $patientResult=User::with('company')->whereHas('role',function($role_q){
        //     $role_q->where('name','Patient');
        // })->select(['name','email','phone','address','image','website_url','id','blocked'])->toSql();
        // die;

        return view('pages.paitent.paitent-list', ['pageConfigs' => $pageConfigs], ['breadcrumbs' => $breadcrumbs,'patientResult'=>$patientResult,'pageTitle'=>$pageTitle,'paginationUrl'=>$paginationUrl,'userType'=>$userType,'editUrl'=>$editUrl,'deleteUrl'=>$deleteUrl,'page'=>$page,'perPage'=>$perPage]);
    }
    public function createPatient($id='')
    {
       // echo"hi admin patient create";die;
        $userType = auth()->user()->role()->first()->name;
        $formUrl = 'admin-patient-create';
        $user_result=$states=$cities=$usermeta=false;
        $breadcrumbs = [
            ['link' => "modern", 'name' => "Home"], ['link' => "javascript:void(0)", 'name' => __('locale.patient')], ['name' => (($id!='') ? __('locale.Edit') : __('locale.Create') )]];
        //Pageheader set true for breadcrumbs
        $pageConfigs = ['pageHeader' => true];
        $countries = Country::get(["name", "id"]);
        $companies = Company::get(["company_name", "id","company_code"]);
        $roles=Role::where('name','=','Patient')->get(["id","name"]);
        $companyCode = Helper::setNumber();
        $pageTitle = __('locale.patient'); 
        if($id!=''){
            $permission_arr = [];
            //echo $id;
            $user_result = User::with(['company','permission'])->find($id);
            $usermeta=Usermeta::where('u_id','=',$id)->get();
            
            if($user_result->permission->count()>0){
                foreach($user_result->permission as $permission_val){
                    $permission_arr[$permission_val->name][] = $permission_val->guard_name;
                }
            }
            $user_result->permission = $permission_arr;
            // echo '<pre>';print_r($user_result);exit();
            if($user_result){
                $states = State::where('country_id',$user_result->country)->get(["name", "id"]);
                $cities = City::where('state_id',$user_result->state)->get(["name", "id"]);
            }
            $formUrl = 'admin-patient-update';
        }
       // dd($usermeta);
        return view('pages.admin-patient.admin-patient-create', ['pageConfigs' => $pageConfigs], ['breadcrumbs' => $breadcrumbs,'countries'=>$countries,'pageTitle'=>$pageTitle,'companies'=>$companies,'user_result'=>$user_result,'states'=>$states,'cities'=>$cities,'userType'=>$userType,'formUrl'=>$formUrl,'companyCode'=>$companyCode,'roles'=>$roles,'usermeta'=>$usermeta]);
    }
    
    
    public function storePatient(Request $request){
        
        
       //echo '<pre>';print_r($request->all()); exit();
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:250',
            'password2'=>'required|max:250',
            'email' => 'required|unique:users|max:250',
            'code'=>'required|unique:users',
            'phone' => 'required|max:10',
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
        $user = User::create($request->all());
       // echo"<pre>";print_r($user);die;
        $usermeta= new Usermeta;
        $usermeta->u_id=$user['id'];
        $usermeta->care_home_code=$request['company'];
        $usermeta->father_husband_name=$request['father_husband_name'];
        $usermeta->dob=$request['dob'];
        $usermeta->marital_status=$request['marital_status'];
        $usermeta->anniversary=$request['anniversary'];
        $usermeta->special_instructions=$request['special_instructions'];
        $usermeta->updated_by_user=auth()->user()->id;
        $usermeta->save();
        
        $id = $user->id;
        //echo $role->id;die;
        $user->company()->attach($request->company);
        $user->role()->attach($role->id);
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
        
        return redirect()->route('admin.paitent-list')->with('success',__('locale.patient_create_success'));
    }
    public function destroyPatient($id)
    {   
         if(User::where('id',$id)->delete()){
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
    public function updatePatient(Request $request, $id){

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

        $user = User::where('id',$id)->update([
            "code"=>$request['code'],
            "typeselect"=>$request['typeselect'],
            "name"=>$request['name'],
            "address1"=>$request['address1'],
            "address2"=>$request['address2'],
            "address3"=>$request['address3'],
            "country"=>$request['country'],
            "state"=>$request['state'],
            "city"=>$request['city'],
            "zipcode"=>$request['zipcode'],
            "phone"=>$request['phone'],
            "email"=>$request['email'],
            "password2"=>$request['password2'],
            "option_for_block"=>$request['option_for_block']
        ]);
        $usermeta = Usermeta::where('u_id',$id)->update([
            "father_husband_name"=>$request['father_husband_name'],
            "dob"=>$request['dob'],
            "marital_status"=>$request['marital_status'],
            "anniversary"=>$request['anniversary'],
            "special_instructions"=>$request['special_instructions'],
        ]);


        //$backurl = 'superadmin.'.strtolower($request->typeselect).'-list';
        // superadmin.paitent-list
        // exit();
        $backurl='admin.paitent-list';
        return redirect()->route($backurl)->with('success',__('locale.patient_update_success'));
    }

    public function managerpatientList(Request $request)
    {
        //echo"patient list";die;
        //$paginationUrl = 'company-admin-list';
        $paginationUrl='';
        $userType = auth()->user()->role()->first()->name;
        $deleteUrl = 'superadmin.company-user-delete';
        if(auth()->user()->role()->first()->name=="Manager"){
            $deleteUrl = 'manager-patient-delete';
        }
        
        $perpage = config('app.perpage');
        $breadcrumbs = [
            ['link' => "modern", 'name' => "Home"], ['link' => "javascript:void(0)", 'name' => __('locale.patient')], ['name' => __('locale.patient').__('locale.List')]];
        //Pageheader set true for breadcrumbs
        $pageConfigs = ['pageHeader' => true];
        $pageTitle = 'Patient list';
        if(auth()->user()->role()->first()->name=="superadmin"){
        $paginationUrl = 'superadmin.patient-list';
        }
        
        $editUrl = 'superadmin.company-admin-edit';
        if(auth()->user()->role()->first()->name=="Manager"){
            $editUrl = 'manager-patient-edit';
        }
        
        $patientResult=User::with('company')->whereHas('role',function($role_q){
            $role_q->where('name','Patient');
        })->select(['name','email','phone','address1','image','website_url','id','blocked'])->orderBy('id','DESC');
        if (auth()->user()->role()->first()->name == "Manager") {
            
            $userTypeid =auth()->user()->company()->first()->id; // Assuming 'company_id' is the field on the User model
        
            $patientResult = User::with('company')->whereHas('company', function ($company_q) {
                            $company_q->where('id', '=',auth()->user()->company()->first()->id)->where('typeselect','=','Patient');
                        })->select(['name', 'email', 'phone', 'address1', 'image', 'website_url', 'id', 'blocked','option_for_block'])->orderBy('id', 'DESC');
                        // Rest of your code...
            }
                    
            if($request->ajax()){
                
                // $currentPage = $request->get('page');
                //    $patientResult = $patientResult->whereHas('company', function($q)use($request){
                //         $q->where('id', 'like', '%'.$request->seach_term.'%')
                //                     ->orWhere('name', 'like', '%'.$request->seach_term.'%')
                //                     ->orWhere('email', 'like', '%'.$request->seach_term.'%')
                //                     ->orWhere('phone', 'like', '%'.$request->seach_term.'%')
                //                     ->orWhere('address', 'like', '%'.$request->seach_term.'%');
                //                 })->paginate($perpage);
                
                //     return view('pages.paitent.paitent-list-ajax', compact('patientResult','editUrl','deleteUrl'))->render();
                // echo $patientResult->toSql(); exit();
                $patientResult = $patientResult->where(function($q) use($request){
                    $q->where('id', 'like', '%'.$request->seach_term.'%')
                    ->orWhere('name', 'like', '%'.$request->seach_term.'%')
                    ->orWhere('email', 'like', '%'.$request->seach_term.'%')
                    ->orWhere('phone', 'like', '%'.$request->seach_term.'%')
                    ->orWhere('address', 'like', '%'.$request->seach_term.'%');
                })->paginate($perpage);
                $perPage = $perpage;
                $page = $patientResult->currentPage();
        
                return view('pages.manager-patient.manager-patient-list-ajax', compact('patientResult','editUrl','deleteUrl','page','perPage'))->render();
            }
            $patientResult = $patientResult->paginate($perpage);
            $perPage = $perpage;
            $page = $patientResult->currentPage();
       // echo"<pre>";print_r($patientResult);die;
        // echo $patientResult=User::with('company')->whereHas('role',function($role_q){
        //     $role_q->where('name','Patient');
        // })->select(['name','email','phone','address','image','website_url','id','blocked'])->toSql();
        // die;

        return view('pages.manager-patient.manager-patient-list', ['pageConfigs' => $pageConfigs], ['breadcrumbs' => $breadcrumbs,'patientResult'=>$patientResult,'pageTitle'=>$pageTitle,'paginationUrl'=>$paginationUrl,'userType'=>$userType,'editUrl'=>$editUrl,'deleteUrl'=>$deleteUrl,'page'=>$page,'perPage'=>$perPage]);
    }
    public function managercreatePatient($id='')
    {
       // echo"hi admin patient create";die;
        $userType = auth()->user()->role()->first()->name;
        $formUrl = 'manager-patient-create';
        $user_result=$states=$cities=$usermeta=false;
        $breadcrumbs = [
            ['link' => "modern", 'name' => "Home"], ['link' => "javascript:void(0)", 'name' => __('locale.patient')], ['name' => (($id!='') ? __('locale.Edit') : __('locale.Create') )]];
        //Pageheader set true for breadcrumbs
        $pageConfigs = ['pageHeader' => true];
        $countries = Country::get(["name", "id"]);
        $companies = Company::get(["company_name", "id","company_code"]);
        $roles=Role::where('name','!=','superadmin')->get(["id","name"]);
        $companyCode = Helper::setNumber();
        $pageTitle = __('locale.patient'); 
        if($id!=''){
            $permission_arr = [];
            //echo $id;
            $user_result = User::with(['company','permission'])->find($id);
            $usermeta=Usermeta::where('u_id','=',$id)->get();
            
            if($user_result->permission->count()>0){
                foreach($user_result->permission as $permission_val){
                    $permission_arr[$permission_val->name][] = $permission_val->guard_name;
                }
            }
            $user_result->permission = $permission_arr;
            // echo '<pre>';print_r($user_result);exit();
            if($user_result){
                $states = State::where('country_id',$user_result->country)->get(["name", "id"]);
                $cities = City::where('state_id',$user_result->state)->get(["name", "id"]);
            }
            $formUrl = 'admin-patient-update';
        }
       // dd($usermeta);
        return view('pages.manager-patient.manager-patient-create', ['pageConfigs' => $pageConfigs], ['breadcrumbs' => $breadcrumbs,'countries'=>$countries,'pageTitle'=>$pageTitle,'companies'=>$companies,'user_result'=>$user_result,'states'=>$states,'cities'=>$cities,'userType'=>$userType,'formUrl'=>$formUrl,'companyCode'=>$companyCode,'roles'=>$roles,'usermeta'=>$usermeta]);
    }
    
    
    public function managerstorePatient(Request $request){
        
        
      // echo '<pre>';print_r($request->all()); exit();
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:250',
            'password2'=>'required|max:250',
            'email' => 'required|unique:users|max:250',
            //'code'=>'required|unique:users',
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
        $user = User::create($request->all());
       // echo"<pre>";print_r($user);die;
        $usermeta= new Usermeta;
        $usermeta->u_id=$user['id'];
        $usermeta->care_home_code=$request['company'];
        $usermeta->father_husband_name=$request['father_husband_name'];
        $usermeta->dob=$request['dob'];
        $usermeta->marital_status=$request['marital_status'];
        $usermeta->anniversary=$request['anniversary'];
        $usermeta->special_instructions=$request['special_instructions'];
        $usermeta->updated_by_user=auth()->user()->id;
        $usermeta->save();
        
        $id = $user->id;
        //echo $role->id;die;
        $user->company()->attach($request->company);
        $user->role()->attach($role->id);
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
        
        return redirect()->route('manager.paitent-list')->with('success',__('locale.patient_create_success'));
    }
    public function managerdestroyPatient($id)
    {   
         if(User::where('id',$id)->delete()){
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
    public function managerupdatePatient(Request $request, $id){

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

        $user = User::where('id',$id)->update([
            "code"=>$request['code'],
            "typeselect"=>$request['typeselect'],
            "name"=>$request['name'],
            "address1"=>$request['address1'],
            "address2"=>$request['address2'],
            "address3"=>$request['address3'],
            "country"=>$request['country'],
            "state"=>$request['state'],
            "city"=>$request['city'],
            "zipcode"=>$request['zipcode'],
            "phone"=>$request['phone'],
            "email"=>$request['email'],
            "password2"=>$request['password2'],
            "option_for_block"=>$request['option_for_block']
        ]);
        $usermeta = Usermeta::where('u_id',$id)->update([
            "father_husband_name"=>$request['father_husband_name'],
            "dob"=>$request['dob'],
            "marital_status"=>$request['marital_status'],
            "anniversary"=>$request['anniversary'],
            "special_instructions"=>$request['special_instructions'],
        ]);


        //$backurl = 'superadmin.'.strtolower($request->typeselect).'-list';
        // superadmin.paitent-list
        // exit();
        $backurl='admin.paitent-list';
        return redirect()->route($backurl)->with('success',__('locale.patient_update_success'));
    }


    public function carerList(Request $request)
    {
       // echo"carer list";die;
       $paginationUrl='';
        $userType = auth()->user()->role()->first()->name;
        $deleteUrl = 'superadmin.company-admin-delete';
        if(auth()->user()->role()->first()->name=="Admin"){
            $deleteUrl = 'admin-carer-delete';
        }
        $perpage = config('app.perpage');
        $breadcrumbs = [
            ['link' => "modern", 'name' => "Home"], ['link' => "javascript:void(0)", 'name' => __('locale.carer')], ['name' => __('locale.carer').__('locale.List')]];
        //Pageheader set true for breadcrumbs
        $pageConfigs = ['pageHeader' => true];
        $pageTitle = 'Carer list';
        if(auth()->user()->role()->first()->name=="superadmin"){
        $paginationUrl = 'superadmin.carer-list';
        }
        $editUrl = 'superadmin.company-admin-edit';
        if(auth()->user()->role()->first()->name=="Admin"){
            $editUrl = 'admin-carer-edit';
        }

        $carerResult=User::with('company')->whereHas('role',function($role_q){
            $role_q->where('name','Carer');
        })->select(['name','email','phone','address1','image','website_url','id','blocked','option_for_block'])->orderBy('id','DESC');

        if (auth()->user()->role()->first()->name == "Admin") {
            
            $userTypeid =auth()->user()->company()->first()->id; // Assuming 'company_id' is the field on the User model
        
            $carerResult = User::with('company')->whereHas('company', function ($company_q) {
                            $company_q->where('id', '=',auth()->user()->company()->first()->id)->where('typeselect','=','Carer');
                        })->select(['name', 'email', 'phone', 'address1', 'image', 'website_url', 'id', 'blocked','option_for_block'])->orderBy('id', 'DESC');
                        // Rest of your code...
            }

        if($request->ajax()){
          
            // $carerResult = $carerResult->whereHas('company', function($q)use($request){
            //     $q->where('id', 'like', '%'.$request->seach_term.'%')
            //                 ->orWhere('name', 'like', '%'.$request->seach_term.'%');
                            
            // })->paginate($perpage);//search in laravel through relationship
            // $carerResult = $carerResult->whereHas('company', function($q)use($request){
            //          $q->where('id', 'like', '%'.$request->seach_term.'%')
            //                      ->orWhere('name', 'like', '%'.$request->seach_term.'%');
                                
            //      })->toSql();
                $carerResult = $carerResult->where(function($q)use($request)
                {$q->where('id', 'like', '%'.$request->seach_term.'%')
                ->orWhere('name', 'like', '%'.$request->seach_term.'%');
                })->paginate($perpage);
                $perPage = $perpage;
                $page = $carerResult->currentPage();
                //  print_r($carerResult);
                //  die;
                        
            return view('pages.carer.carer-list-ajax', compact('carerResult','editUrl','deleteUrl','page','perPage'))->render();
        }

        $carerResult = $carerResult->paginate($perpage);
        $perPage = $perpage;
        $page = $carerResult->currentPage();
        
       // echo"<pre>";print_r($patientResult);die;
        // echo $patientResult=User::with('company')->whereHas('role',function($role_q){
        //     $role_q->where('name','Patient');
        // })->select(['name','email','phone','address','image','website_url','id','blocked'])->toSql();
        // die;

        return view('pages.carer.carer-list', ['pageConfigs' => $pageConfigs], ['breadcrumbs' => $breadcrumbs,'carerResult'=>$carerResult,'pageTitle'=>$pageTitle,'paginationUrl'=>$paginationUrl,'userType'=>$userType,'editUrl'=>$editUrl,'deleteUrl'=>$deleteUrl,'page'=>$page,'perPage'=>$perPage]);
    }
    public function createCarer($id='')
    {
      // echo"hi admin carer create";die;
        $userType = auth()->user()->role()->first()->name;
        $formUrl = 'admin-carer-create';
        $user_result=$states=$cities=false;
        $breadcrumbs = [
            ['link' => "modern", 'name' => "Home"], ['link' => "javascript:void(0)", 'name' => __('locale.carer')], ['name' => (($id!='') ? __('locale.Edit') : __('locale.Create') )]];
        //Pageheader set true for breadcrumbs
        $pageConfigs = ['pageHeader' => true];
        $countries = Country::get(["name", "id"]);
        $companies = Company::get(["company_name", "id","company_code"]);
        $roles=Role::where('name','!=','superadmin')->get(["id","name"]);
        $companyCode = Helper::setNumber();
        $pageTitle = __('locale.Carer'); 
        if($id!=''){
            $permission_arr = [];
            $user_result = User::with(['company','permission'])->find($id);
            if($user_result->permission->count()>0){
                foreach($user_result->permission as $permission_val){
                    $permission_arr[$permission_val->name][] = $permission_val->guard_name;
                }
            }
            $user_result->permission = $permission_arr;
            // echo '<pre>';print_r($user_result);exit();
            if($user_result){
            $states = State::where('country_id',$user_result->country)->get(["name", "id"]);
            $cities = City::where('state_id',$user_result->state)->get(["name", "id"]);
            }
            $formUrl = 'admin-carer-update';
        }
        // dd($user_result);
        return view('pages.admin-carer.admin-carer-create', ['pageConfigs' => $pageConfigs], ['breadcrumbs' => $breadcrumbs,'countries'=>$countries,'pageTitle'=>$pageTitle,'companies'=>$companies,'user_result'=>$user_result,'states'=>$states,'cities'=>$cities,'userType'=>$userType,'formUrl'=>$formUrl,'companyCode'=>$companyCode,'roles'=>$roles]);
    }
    
    
    public function storeCarer(Request $request){
        
        
        //echo '<pre>';print_r($request->all()); exit();
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:250',
            'password2'=>'required|max:250',
            'email' => 'required|unique:users|max:250',
            'code'=>'required|unique:users',
            'phone' => 'required|max:10',
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
        $user = User::create($request->all());
        
        $id = $user->id;
        //echo $role->id;die;
        $user->company()->attach($request->company);
        $user->role()->attach($role->id);
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
        
        return redirect()->route('admin.carer-list')->with('success',__('locale.carer_create_success'));
    }
    public function destroyCarer($id)
    {   
         if(User::where('id',$id)->delete()){
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
    public function updateCarer(Request $request, $id){

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

        $user = User::where('id',$id)->update($request->all());

        //$backurl = 'superadmin.'.strtolower($request->typeselect).'-list';
        // superadmin.paitent-list
        // exit();
        $backurl='admin.carer-list';
        return redirect()->route($backurl)->with('success',__('locale.carer_update_success'));
    }

    public function managercarerList(Request $request)
    {
       // echo"carer list";die;
       $paginationUrl='';
        $userType = auth()->user()->role()->first()->name;
        $deleteUrl = 'superadmin.company-admin-delete';
        if(auth()->user()->role()->first()->name=="Manager"){
            $deleteUrl = 'manager-carer-delete';
        }
        $perpage = config('app.perpage');
        $breadcrumbs = [
            ['link' => "modern", 'name' => "Home"], ['link' => "javascript:void(0)", 'name' => __('locale.carer')], ['name' => __('locale.carer').__('locale.List')]];
        //Pageheader set true for breadcrumbs
        $pageConfigs = ['pageHeader' => true];
        $pageTitle = 'Carer list';
        if(auth()->user()->role()->first()->name=="superadmin"){
        $paginationUrl = 'superadmin.carer-list';
        }
        $editUrl = 'superadmin.company-admin-edit';
        if(auth()->user()->role()->first()->name=="Manager"){
            $editUrl = 'manager-carer-edit';
        }

        $carerResult=User::with('company')->whereHas('role',function($role_q){
            $role_q->where('name','Carer');
        })->select(['name','email','phone','address1','image','website_url','id','blocked','option_for_block'])->orderBy('id','DESC');

        if (auth()->user()->role()->first()->name == "Manager") {
            
            $userTypeid =auth()->user()->company()->first()->id; // Assuming 'company_id' is the field on the User model
        
            $carerResult = User::with('company')->whereHas('company', function ($company_q) {
                            $company_q->where('id', '=',auth()->user()->company()->first()->id)->where('typeselect','=','Carer');
                        })->select(['name', 'email', 'phone', 'address1', 'image', 'website_url', 'id', 'blocked','option_for_block'])->orderBy('id', 'DESC');
                        // Rest of your code...
            }

        if($request->ajax()){
          
            // $carerResult = $carerResult->whereHas('company', function($q)use($request){
            //     $q->where('id', 'like', '%'.$request->seach_term.'%')
            //                 ->orWhere('name', 'like', '%'.$request->seach_term.'%');
                            
            // })->paginate($perpage);//search in laravel through relationship
            // $carerResult = $carerResult->whereHas('company', function($q)use($request){
            //          $q->where('id', 'like', '%'.$request->seach_term.'%')
            //                      ->orWhere('name', 'like', '%'.$request->seach_term.'%');
                                
            //      })->toSql();
                $carerResult = $carerResult->where(function($q)use($request)
                {$q->where('id', 'like', '%'.$request->seach_term.'%')
                ->orWhere('name', 'like', '%'.$request->seach_term.'%');
                })->paginate($perpage);
                $perPage = $perpage;
                $page = $carerResult->currentPage();
                //  print_r($carerResult);
                //  die;
                        
            return view('pages.manager-carer.manager-carer-list-ajax', compact('carerResult','editUrl','deleteUrl','page','perPage'))->render();
        }

        $carerResult = $carerResult->paginate($perpage);
        $perPage = $perpage;
        $page = $carerResult->currentPage();
        
       // echo"<pre>";print_r($patientResult);die;
        // echo $patientResult=User::with('company')->whereHas('role',function($role_q){
        //     $role_q->where('name','Patient');
        // })->select(['name','email','phone','address','image','website_url','id','blocked'])->toSql();
        // die;

        return view('pages.manager-carer.manager-carer-list', ['pageConfigs' => $pageConfigs], ['breadcrumbs' => $breadcrumbs,'carerResult'=>$carerResult,'pageTitle'=>$pageTitle,'paginationUrl'=>$paginationUrl,'userType'=>$userType,'editUrl'=>$editUrl,'deleteUrl'=>$deleteUrl,'page'=>$page,'perPage'=>$perPage]);
    }
    public function managercreateCarer($id='')
    {
      // echo"hi admin carer create";die;
        $userType = auth()->user()->role()->first()->name;
        $formUrl = 'manager-carer-create';
        $user_result=$states=$cities=$usermeta=false;
        $breadcrumbs = [
            ['link' => "modern", 'name' => "Home"], ['link' => "javascript:void(0)", 'name' => __('locale.carer')], ['name' => (($id!='') ? __('locale.Edit') : __('locale.Create') )]];
        //Pageheader set true for breadcrumbs
        $pageConfigs = ['pageHeader' => true];
        $countries = Country::get(["name", "id"]);
        $companies = Company::get(["company_name", "id","company_code"]);
        $roles=Role::where('name','!=','superadmin')->get(["id","name"]);
        $companyCode = Helper::setNumber();
        $pageTitle = __('locale.Carer'); 
        if($id!=''){
            $permission_arr = [];
            $user_result = User::with(['company','permission'])->find($id);
            $usermeta=Usermeta::where('u_id','=',$id)->get();
            if($user_result->permission->count()>0){
                foreach($user_result->permission as $permission_val){
                    $permission_arr[$permission_val->name][] = $permission_val->guard_name;
                }
            }
            $user_result->permission = $permission_arr;
            // echo '<pre>';print_r($user_result);exit();
            if($user_result){
            $states = State::where('country_id',$user_result->country)->get(["name", "id"]);
            $cities = City::where('state_id',$user_result->state)->get(["name", "id"]);
            }
            $formUrl = 'manager-carer-update';
        }
        // dd($user_result);
        return view('pages.manager-carer.manager-carer-create', ['pageConfigs' => $pageConfigs], ['breadcrumbs' => $breadcrumbs,'countries'=>$countries,'pageTitle'=>$pageTitle,'companies'=>$companies,'user_result'=>$user_result,'states'=>$states,'cities'=>$cities,'userType'=>$userType,'formUrl'=>$formUrl,'companyCode'=>$companyCode,'roles'=>$roles,'usermeta'=>$usermeta]);
    }
    
    
    public function managerstoreCarer(Request $request){
        
        
       // echo '<pre>';print_r($request->all()); exit();
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:250',
            'password2'=>'required|max:250',
            'email' => 'required|unique:users|max:250',
           // 'code'=>'required|unique:users',
            'phone' => 'required|max:10',
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
        $user = User::create($request->all());
        $usermeta= new Usermeta;
        $usermeta->u_id=$user['id'];
        $usermeta->care_home_code=$request['company'];
        $usermeta->father_husband_name=$request['father_husband_name'];
        $usermeta->dob=$request['dob'];
        $usermeta->marital_status=$request['marital_status'];
        $usermeta->anniversary=$request['anniversary'];
        $usermeta->special_instructions=$request['special_instructions'];
        $usermeta->updated_by_user=auth()->user()->id;
        $usermeta->save();
        
       // $id = $user->id;
        //echo $role->id;die;
        $user->company()->attach($request->company);
        $user->role()->attach($role->id);
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
        
        return redirect()->route('manager.carer-list')->with('success',__('locale.carer_create_success'));
    }
    public function managerdestroyCarer($id)
    {   
         if(User::where('id',$id)->delete()){
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
    public function managerupdateCarer(Request $request, $id){

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

        //$user = User::where('id',$id)->update($request->all());
        $user = User::where('id',$id)->update([
            "code"=>$request['code'],
            "typeselect"=>$request['typeselect'],
            "name"=>$request['name'],
            "address1"=>$request['address1'],
            "address2"=>$request['address2'],
            "address3"=>$request['address3'],
            "country"=>$request['country'],
            "state"=>$request['state'],
            "city"=>$request['city'],
            "zipcode"=>$request['zipcode'],
            "phone"=>$request['phone'],
            "email"=>$request['email'],
            "password2"=>$request['password2'],
            "option_for_block"=>$request['option_for_block']
        ]);
        $usermeta = Usermeta::where('u_id',$id)->update([
            "father_husband_name"=>$request['father_husband_name'],
            "dob"=>$request['dob'],
            "marital_status"=>$request['marital_status'],
            "anniversary"=>$request['anniversary'],
            "special_instructions"=>$request['special_instructions'],
        ]);

        //$backurl = 'superadmin.'.strtolower($request->typeselect).'-list';
        // superadmin.paitent-list
        // exit();
        $backurl='admin.carer-list';
        return redirect()->route($backurl)->with('success',__('locale.carer_update_success'));
    }



    public function managerList(Request $request)
    {
       //echo"manager list";die;
        $paginationUrl='';
        $userType = auth()->user()->role()->first()->name;
        $userTypeid ='';
       // echo $userTypeid;die;
        $deleteUrl = 'superadmin.company-admin-delete';
        $perpage = config('app.perpage');
        if(auth()->user()->role()->first()->name=="Admin"){
            $deleteUrl = 'admin-manager-delete';
        }
        $perpage = config('app.perpage');
        $breadcrumbs = [
            ['link' => "modern", 'name' => "Home"], ['link' => "javascript:void(0)", 'name' => __('locale.manager')], ['name' => __('locale.manager').__('locale.List')]];
        //Pageheader set true for breadcrumbs
        $pageConfigs = ['pageHeader' => true];
        $pageTitle = 'Manager list';
        if(auth()->user()->role()->first()->name=="superadmin"){
            $paginationUrl = 'superadmin.manager-list';
        }
        $editUrl = 'superadmin.company-admin-edit';
        if(auth()->user()->role()->first()->name=="Admin"){
            $editUrl = 'admin-manager-edit';
        }

        $managerResult=User::with('company')->whereHas('role',function($role_q){
            $role_q->where('typeselect','Manager');
        })->select(['name','email','phone','address1','image','website_url','id','blocked','option_for_block'])->orderBy('id','desc');
      // echo"<pre>";print_r($managerResult);die;
        
        if (auth()->user()->role()->first()->name == "Admin") {
          //  echo "manager in";die;
            $userTypeid =auth()->user()->company()->first()->id; // Assuming 'company_id' is the field on the User model
        
            $managerResult = User::with('company')->whereHas('company', function ($company_q) {
                            $company_q->where('id', '=',auth()->user()->company()->first()->id)->where('typeselect','=','Manager');
                        })->select(['name', 'email', 'phone', 'address1', 'image', 'website_url', 'id', 'blocked','option_for_block'])->orderBy('id', 'DESC');
        
            // Rest of your code...
        }
        if($request->ajax()){
          
            $managerResult = $managerResult->whereHas('company', function($q)use($request){
                $q->where('id', 'like', '%'.$request->seach_term.'%')
                            ->orWhere('name', 'like', '%'.$request->seach_term.'%');
                            
            })->paginate($perpage);//search in laravel through relationship
            $perPage = $perpage; // Number of items per page
            $page = $managerResult->currentPage();          
            return view('pages.manager.manager-list-ajax', compact('managerResult','editUrl','deleteUrl','page','perPage'))->render();
        }

        $managerResult = $managerResult->paginate($perpage);
        $perPage = $perpage; // Number of items per page
        $page = $managerResult->currentPage(); 
       // echo"<pre>";print_r($managerResult);die;
        

        // echo $patientResult=User::with('company')->whereHas('role',function($role_q){
        //     $role_q->where('name','Patient');
        // })->select(['name','email','phone','address','image','website_url','id','blocked'])->toSql();
        // die;

        return view('pages.manager.manager-list', ['pageConfigs' => $pageConfigs], ['breadcrumbs' => $breadcrumbs,'managerResult'=>$managerResult,'pageTitle'=>$pageTitle,'paginationUrl'=>$paginationUrl,'userType'=>$userType,'editUrl'=>$editUrl,'deleteUrl'=>$deleteUrl,'page'=>$page,'perPage'=>$perPage]);
    }

    public function createManager($id='')
    {
        //echo"hi admin manager create";die;
        $userType = auth()->user()->role()->first()->name;
        $formUrl = 'admin-manager-create';
        $user_result=$states=$cities=false;
        $breadcrumbs = [
            ['link' => "modern", 'name' => "Home"], ['link' => "javascript:void(0)", 'name' => __('locale.manager')], ['name' => (($id!='') ? __('locale.Edit') : __('locale.Create') )]];
        //Pageheader set true for breadcrumbs
        $pageConfigs = ['pageHeader' => true];
        $countries = Country::get(["name", "id"]);
        $companies = Company::get(["company_name", "id","company_code"]);
        $roles=Role::where('name','!=','superadmin')->get(["id","name"]);
        $companyCode = Helper::setNumber();
        $pageTitle = __('locale.manager'); 
        if($id!=''){
            $permission_arr = [];
            $user_result = User::with(['company','permission'])->find($id);
            if($user_result->permission->count()>0){
                foreach($user_result->permission as $permission_val){
                    $permission_arr[$permission_val->name][] = $permission_val->guard_name;
                }
            }
            $user_result->permission = $permission_arr;
            // echo '<pre>';print_r($user_result);exit();
            if($user_result){
            $states = State::where('country_id',$user_result->country)->get(["name", "id"]);
            $cities = City::where('state_id',$user_result->state)->get(["name", "id"]);
            }
            $formUrl = 'admin-manager-update';
        }
        // dd($user_result);
        return view('pages.admin-manager.admin-manager-create', ['pageConfigs' => $pageConfigs], ['breadcrumbs' => $breadcrumbs,'countries'=>$countries,'pageTitle'=>$pageTitle,'companies'=>$companies,'user_result'=>$user_result,'states'=>$states,'cities'=>$cities,'userType'=>$userType,'formUrl'=>$formUrl,'companyCode'=>$companyCode,'roles'=>$roles]);
    }
    
    
    public function storeManager(Request $request){
        
        
       // echo '<pre>';print_r($request->all()); exit();
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:250',
            'password2'=>'required|max:250',
            'email' => 'required|unique:users|max:250',
            'code'=>'required|unique:users',
            'phone' => 'required|max:10',
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
        $user = User::create($request->all());
        
        $id = $user->id;
        //echo $role->id;die;
        $user->company()->attach($request->company);
        $user->role()->attach($role->id);
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
        //echo"here";die;
        return redirect()->route('admin.manager-list')->with('success',__('locale.manager_create_success'));
    }

    public function destroyManager($id)
    {   
         if(User::where('id',$id)->delete()){
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
    public function updateManager(Request $request, $id){

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

        $user = User::where('id',$id)->update($request->all());

        //$backurl = 'superadmin.'.strtolower($request->typeselect).'-list';
        // superadmin.paitent-list
        // exit();
        $backurl='admin.manager-list';
        return redirect()->route($backurl)->with('success',__('locale.manager_update_success'));
    }



    public function adminList(Request $request)
    {
       //echo"admin list";die;
       $paginationUrl='';
        $userType = auth()->user()->role()->first()->name;
        $deleteUrl = 'superadmin.company-user-delete';
        $perpage = config('app.perpage');
        $breadcrumbs = [
            ['link' => "modern", 'name' => "Home"], ['link' => "javascript:void(0)", 'name' => __('locale.admin')], ['name' => __('locale.admin').__('locale.List')]];
        //Pageheader set true for breadcrumbs
        $pageConfigs = ['pageHeader' => true];
        $pageTitle = 'Admin list';
        if(auth()->user()->role()->first()->name=="superadmin"){
        $paginationUrl = 'superadmin.admin-list';
        }
        $editUrl = 'superadmin.company-admin-edit';

        $adminResult=User::with('company')->whereHas('role',function($role_q){
            $role_q->where('name','Admin');
        })->select(['name','password2','password','email','phone','address1','image','website_url','id','blocked','option_for_block'])->orderBy('id','desc');

        //echo"<pre>";print_r($adminResult);die;
        
        if($request->ajax()){
           //print_r($request->seach_term);
           
            $adminResult = $adminResult->when($request->seach_term, function($q)use($request){$q->where('id', 'like', '%'.$request->seach_term.'%')
                            ->orWhere('name', 'like', '%'.$request->seach_term.'%');
                            
            })->paginate($perpage);//search in laravel through relationship
            $perPage = $perpage; // Number of items per page
            $page = $adminResult->currentPage();
            //print_r($adminResult);die;     
            return view('pages.admin.admin-list-ajax', compact('adminResult','editUrl','deleteUrl','page','perPage'))->render();
        }

        $adminResult = $adminResult->paginate($perpage);//pagination only
        $perPage = $perpage; // Number of items per page
        $page = $adminResult->currentPage();
       // echo"<pre>";print_r($patientResult);die;
        // echo $patientResult=User::with('company')->whereHas('role',function($role_q){
        //     $role_q->where('name','Patient');
        // })->select(['name','email','phone','address','image','website_url','id','blocked'])->toSql();
        // die;

        return view('pages.admin.admin-list', ['pageConfigs' => $pageConfigs], ['breadcrumbs' => $breadcrumbs,'adminResult'=>$adminResult,'pageTitle'=>$pageTitle,'paginationUrl'=>$paginationUrl,'userType'=>$userType,'editUrl'=>$editUrl,'deleteUrl'=>$deleteUrl,'page'=>$page,'perPage'=>$perPage]);
    }

}
