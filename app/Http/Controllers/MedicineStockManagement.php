<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{Medicine,Company,MedicineStockModel};
use Illuminate\Support\Facades\Validator;
use Helper;

class MedicineStockManagement extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        
        $userType = auth()->user()->role()->first()->name;

        $addUrl = (strtolower($userType)).'.medicine-stock-management.create';
        $editUrl = (strtolower($userType)).'.medicine-stock-management.edit';
        $deleteUrl = (strtolower($userType)).'.medicine-stock-management.delete';
        
        $breadcrumbs = [
            ['link' => "modern", 'name' => "Home"], ['link' => "javascript:void(0)", 'name' => __('locale.medicine-stock')], ['name' => __('locale.medicine-stock').' '.__('locale.List')]];
        //Pageheader set true for breadcrumbs
        $pageConfigs = ['pageHeader' => true];
        $perpage = config('app.perpage');
        $pageTitle = __('locale.medicine-stock');

        $medicinestockResult = MedicineStockModel::with('company','medicine')->select(['id','company_id','medicine_id','quantity','purchase_issue_type','dates'])->orderBy('id','desc');

        if (auth()->user()->role()->first()->name == "Admin") {
          //  echo"adm";die;
            $userTypeid =auth()->user()->company()->first()->id; // Assuming 'company_id' is the field on the User model
        
            $medicinestockResult = MedicineStockModel::with('company','medicine')->whereHas('company', function ($company_q) {
                            $company_q->where('id', '=',auth()->user()->company()->first()->id);
                        })->select(['id','company_id','medicine_id','quantity','purchase_issue_type','dates'])->orderBy('id','desc');
                        // Rest of your code...
            }
            if (auth()->user()->role()->first()->name == "Manager") {
            
                $userTypeid =auth()->user()->company()->first()->id; // Assuming 'company_id' is the field on the User model
            
                $medicinestockResult = MedicineStockModel::with('company','medicine')->whereHas('company', function ($company_q) {
                                $company_q->where('id', '=',auth()->user()->company()->first()->id);
                            })->select(['id','company_id','medicine_id','quantity','purchase_issue_type','dates'])->orderBy('id','desc');
                            // Rest of your code...
                }
        
        if($request->ajax()){
            if($userType=="superadmin")
            {
            $medicinestockResult = $medicinestockResult->wherehas('company',function($q)use($request){
                $q->where('company_name', 'like', '%'.$request->seach_term.'%');
            }) ->orWhereHas('medicine',function($q)use($request){
                $q->where('medicine_name', 'like', '%'.$request->seach_term.'%');
            }) ->paginate($perpage);
        }else{
            $medicinestockResult = $medicinestockResult
            ->wherehas('company',function($q)use($request){
                $q->where('id', '=',auth()->user()->company()->first()->id)->where('company_name', 'like', '%'.$request->seach_term.'%');
            }) ->orWhereHas('medicine',function($q)use($request){
                $q->where('company', '=',auth()->user()->company()->first()->id)->where('medicine_name', 'like', '%'.$request->seach_term.'%');
            }) ->paginate($perpage);

        }
        //echo"<pre>";print_r($medicinestockResult);die;
        //  $sqlQuery = $medicinestockResult->toSql();
        //  dd($sqlQuery);

       
            $perPage = $perpage;
            $page = $medicinestockResult->currentPage();
                        
            return view('pages.medicine-stock-management.ajax', compact('medicinestockResult','editUrl','deleteUrl','page','perPage'))->render();
        }

        $medicinestockResult = $medicinestockResult->paginate($perpage);
        $perPage = $perpage;
        $page = $medicinestockResult->currentPage();

        // if($userType!=config('custom.superadminrole')){
        //     $addUrl = 'medicine-stock-management.create';
        // }
       // echo"<pre>";print_r($medicinestockResult);die;

        return view('pages.medicine-stock-management.list',compact('breadcrumbs','pageConfigs','pageTitle','addUrl','userType','medicinestockResult','editUrl','deleteUrl','page','perPage'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id='')
    {
        $userType = auth()->user()->role()->first()->name;
        //$formUrl = 'superadmin.medicine-stock-management.store';
        $formUrl = (strtolower($userType)).'.medicine-stock-management.store';
        // if($userType=="Admin")
        // {
        //     $formUrl = 'admin.medicine-stock-management.store';
        // }
        // if($userType=="Manager")
        // {
        //     $formUrl = 'manager.medicine-stock-management.store';
        // }
        $addUrl = (strtolower($userType)).'.medicine-stock-management.create';
        $breadcrumbs = [
            ['link' => "modern", 'name' => "Home"], ['link' => "javascript:void(0)", 'name' => __('locale.medicine-stock')], ['name' => __('locale.medicine-stock').' '.__('locale.List')]];
        //Pageheader set true for breadcrumbs
        $pageConfigs = ['pageHeader' => true];
        $pageTitle = __('locale.medicine-stock');
        $medicinestockResult=false;
        $medicine_result = Medicine::get();
        $getmedicine_ajax = 'superadmin.getMedicine';
        if($userType!=config('custom.superadminrole')){
            //     $addUrl = 'medicine-stock-management.create';
            $company_id = Helper::loginUserCompanyId();
            $medicine_result = $medicine_result->where('company',$company_id);
        }
        $companies = Company::get(["company_name", "id","company_code"]);
        // dd($medicine_result);
        $purchase_issue = config('custom.purchase_issue');
        if($id!=''){
            //$permission_arr = [];
            $medicinestockResult = MedicineStockModel::find($id);
            $formUrl=(strtolower($userType)).'.medicine-stock-management.update';
            // if($userType=="superadmin")
            // {
            //     $formUrl = 'inventory-update';
            // }
            // if($userType=="Admin")
            // {
            // $formUrl = 'admin-inventory-update';
            // }
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
        // echo"<pre>"; print_r($medicinestockResult); die;

        return view('pages.medicine-stock-management.create',compact('breadcrumbs','pageConfigs','pageTitle','addUrl','userType','purchase_issue','medicine_result','medicinestockResult','companies','getmedicine_ajax','formUrl'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
      // echo"<pre>";print_r($request->all());die;

        $validator = Validator::make($request->all(), [
            'company_id' => 'required',
            'medicine_id' => 'required',
            'quantity' => 'required',
            'dates' => 'required',
            'purchase_issue_type' => 'required',
       ]);
        
        if ($validator->fails()) {
            return redirect()->back()
            ->withErrors($validator)
            ->withInput();
        }

        $medicine = MedicineStockModel::create($request->all());
      
        if(auth()->user()->role()->first()->name=="superadmin")
        {
            $backUrl='superadmin.medicine-stock-management.list';
        }
        if(auth()->user()->role()->first()->name=="Admin")
        {
            $backUrl='admin.medicine-stock-management.list';
        }
        if(auth()->user()->role()->first()->name=="Manager")
        {
            $backUrl='manager.medicine-stock-management.list';
        }
        
        return redirect()->route($backUrl)->with('success',__('locale.created_successfully'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
       // echo"<pre>";print_r($request->all());die;
        $validator = Validator::make($request->all(), [
            'company_id' => 'required|max:250',
            'medicine_id' => 'max:250',
            'quantity'=>'max:250',
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

         $medicinestockResult = MedicinestockModel::where('id',$id)->update($request->all());

         if(auth()->user()->role()->first()->name=="superadmin")
        {
            $backUrl='superadmin.medicine-stock-management.list';
        }
        if(auth()->user()->role()->first()->name=="Admin")
        {
            $backUrl='admin.medicine-stock-management.list';
        }
        if(auth()->user()->role()->first()->name=="Manager")
        {
            $backUrl='manager.medicine-stock-management.list';
        }
    
    // Check if the update was successful
        return redirect()->route($backUrl)->with('success', __('locale.success common update'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if(MedicineStockModel::where('id',$id)->delete()){
            return redirect()->back()->with('success',__('locale.delete_message'));
        }else{
            return redirect()->back()->with('error',__('locale.try_again'));
        }
    }

    public function getMedicine(Request $request)
    {
        $data['medicine'] = Medicine::where("company",$request->id)->get(["medicine_name", "id"]);
        // echo"<pre>"; print_r($data); die;
        return response()->json($data);
    }
}
