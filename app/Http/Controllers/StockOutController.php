<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Models\{Country, State, City};
use App\Models\{User,Role,Patient_schedule,Inventory,Stockin,Stockout};
use App\Models\Company;
use App\Models\CompanyUserMapping;
use App\Imports\UsersImport;
use App\Exports\UsersExport;
use App\Exports\AdminExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Permission;
use Helper;


class StockOutController extends Controller
{
    public function index(Request $request)
    {
       //echo"index stock";die;

        $userType = auth()->user()->role()->first()->name;
        $listUrl = 'company-admin-list';
        $listUrl='stockout-list';
        if($userType=="Admin")
        {
            $listUrl = 'admin-stockout-list';
        }
        $deleteUrl = 'company-admin-delete';
        if($userType=="Admin")
        {
            $deleteUrl = 'admin-stockout-delete';
        }
        $perpage = config('app.perpage');
        $breadcrumbs = [
            ['link' => "modern", 'name' => "Home"], ['link' => "javascript:void(0)", 'name' => __('locale.stock_out')], ['name' => __('locale.stock_out_list')]];
        //Pageheader set true for breadcrumbs
        $pageConfigs = ['pageHeader' => true];
        $pageTitle = 'Stock-out';
        // $usersResult = User::whereHas(
        //     'role', function($q){
        //         $q->where('name', 'company-admin');
        //     }
        // )->select(['id','name','email','phone','address1','image','website_url','blocked'])->orderBy('id','DESC');
        $roles=Role::get(["id","name"]);
        // $stockResult = Stockout::with('inventoryname')->select('*')->orderBy('id','DESC');
        $stockoutResult = Stockout::with(['patientname','inventorynameout','carername'])->select('*')->orderBy('id','DESC');
        //echo"<pre>";print_r($stockResult);die;
        if(auth()->user()->role()->first()->name=="Admin")
        {
            $stockoutResult = Stockout::with(['patientname','inventorynameout','carername'])->select('*')->orderBy('id','DESC');
        }
        $editUrl = 'stockout-edit';
        if($userType=="Admin")
        {
            $editUrl = 'admin-stockout-edit';
        }
        if($request->ajax()){
                // $stockoutResult = $stockoutResult->when($request->seach_term, function($q)use($request){
                //      $q->where('id', 'like', '%'.$request->seach_term.'%')
                //                 ->orWhere('doc_no', 'like', '%'.$request->seach_term.'%');
                
                //  })->paginate($perpage);
                 $stockoutResult = $stockoutResult->whereHas('inventorynameout', function($q)use($request){
                    $q->where('id', 'like', '%'.$request->seach_term.'%')
                             ->orWhere('doc_no', 'like', '%'.$request->seach_term.'%');
                            
                         })->paginate($perpage);//search in laravel through relationship
                         $perPage = $perpage;
                         $page = $stockoutResult->currentPage();
                        return view('pages.stockout.stockout-list-ajax', compact('stockoutResult','editUrl','deleteUrl','page','perPage'))->render();
                    }
                    
                    $stockoutResult = $stockoutResult->paginate($perpage);
                    $perPage = $perpage;
                    $page = $stockoutResult->currentPage();
                    //echo"<pre>";print_r($stockResult);die;
        
        return view('pages.stockout.stockout-list', ['pageConfigs' => $pageConfigs], ['breadcrumbs' => $breadcrumbs,'pageTitle'=>$pageTitle,'userType'=>$userType,'editUrl'=>$editUrl,'deleteUrl'=>$deleteUrl,'roles'=>$roles,'stockoutResult'=>$stockoutResult,'page'=>$page,'perPage'=>$perPage]);
    }
    public function create($id='')
    {
        //echo"stock out create";die;
        //echo auth()->user()->id;die;
        $userType = auth()->user()->role()->first()->name;
        $formUrl = 'company-admin-create';
        $stockout_result=$states=$cities=false;
        $breadcrumbs = [
            ['link' => "modern", 'name' => "Home"], ['link' => "javascript:void(0)", 'name' => __('locale.stock_out')], ['name' => (($id!='') ? __('locale.Edit') : __('locale.Create') )]];
        //Pageheader set true for breadcrumbs
        $pageConfigs = ['pageHeader' => true];
        $patient = User::where('typeselect','=','Patient')->get(["name", "id"]);
        $carer = User::where('typeselect','=','Carer')->get(["name", "id"]);
        $roles=Role::get(["id","name"]);
        $inventory=Inventory::get(["id","name"]);
        //$companyCode = Helper::setNumber();
        $pageTitle = __('locale.stock_out'); 
        if($id!=''){
            //$permission_arr = [];
            $stockout_result = Stockout::find($id);
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
            //$formUrl = 'stockout-update';
            if($userType=="superadmin")
            {
                $formUrl = 'stockout-update';
            }
            if($userType=="Admin")
            {
            $formUrl = 'admin-stockout-update';
            }
        }
        //dd($patient_schedule_result);
        return view('pages.stockout.stockout-create', ['pageConfigs' => $pageConfigs], ['breadcrumbs' => $breadcrumbs,'patient'=>$patient,'pageTitle'=>$pageTitle,'inventory'=>$inventory,'stockout_result'=>$stockout_result,'states'=>$states,'cities'=>$cities,'userType'=>$userType,'formUrl'=>$formUrl,'carer'=>$carer,'roles'=>$roles]);
    }

    public function store(Request $request){
        
        // echo '<pre>';print_r($request->all()); exit();
 
         $validator = Validator::make($request->all(), [
             'doc_no' => 'required|max:250',
             'patient_code' => 'required|max:20',
             'carer_code' => 'required|max:20',
             'inventory_code'=>'required|max:20'
             
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
         $stockOut = Stockout::create($request->all());
         
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
         if(auth()->user()->role()->first()->name=="superadmin")
         {
             $backUrl='stockout-list';
         }
         if(auth()->user()->role()->first()->name=="Admin")
         {
             $backUrl='admin-stockout-list';
         }
         
         return redirect()->route($backUrl)->with('success',__('locale.stock_created_successfully'));
     }

     public function update(Request $request, $id)
    {
        //echo"update";die;
        //echo"<pre>";print_r($request->all());die;
        $validator = Validator::make($request->all(), [
             'doc_no' => 'required|max:250',
             'patient_code' => 'required|max:20',
             'inventory_code'=>'required|max:20',
             'carer_code'=>'required|max:20'
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
            ->withErrors($validator)
            ->withInput();
        }
        unset($request['_token']);
        unset($request['_method']);
        unset($request['action']);

        $stockoutData = Stockout::where('id',$id)->update($request->all());
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
        if(auth()->user()->role()->first()->name=="superadmin")
        {
            $backUrl='stockout-list';
        }
        if(auth()->user()->role()->first()->name=="Admin")
        {
            $backUrl='admin-stockout-list';
        }
        return redirect()->route($backUrl)->with('success',__('locale.success common update'));
    }




}

?>
