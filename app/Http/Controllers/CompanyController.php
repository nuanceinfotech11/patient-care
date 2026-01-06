<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{Company,User,companyUserMapping,ProductCompanyMapping};
use Illuminate\Support\Facades\Validator;
use App\Imports\CompanyImport;
use App\Exports\CompanyExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\{Country, State, City};
use Helper;

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //echo"hi";die;
        $perpage = config('app.perpage');
        $companyResultResponse = [];
        // Breadcrumbs
        $breadcrumbs = [
            ['link' => "/", 'name' => "Home"], ['link' => "company", 'name' => "Care home master"], ['name' => "List"],
        ];
        //Pageheader set true for breadcrumbs
        $pageConfigs = ['pageHeader' => true];
        $pageTitle = __('locale.Companies');
        $companyResult = Company::with(['countryname','statename', 'cityname'])->select(['id','company_name','company_code','address1','country','state','city','contact_person','contact_mobile','licence_valid_till','blocked','phone_no','license_to','option_for_block'])->orderBy('id','DESC');
        //$perPage = 2; // Number of items per page
       // $page = $companyResult->currentPage(); // Get the current page number
        if($request->ajax()){
            $companyResult = $companyResult->when($request->seach_term, function($q)use($request){
                            $q->Where('company_name', 'like', '%'.$request->seach_term.'%')
                            ->orWhere('company_code', 'like', '%'.$request->seach_term.'%');
                        })->paginate($perpage);
                       $perPage = $perpage; // Number of items per page
                       $page = $companyResult->currentPage(); // Get the current page number
            return view('pages.company.company-table-list', compact('companyResult','page','perPage'))->render();//**on click on page 2,it will run**//
        }
        if($companyResult->count()>0){
            $companyResultResponse = $companyResult->paginate($perpage);
            $perPage = $perpage;
            $page = $companyResultResponse->currentPage();//**on page load it will run**//
        }
        // dd($companyResultResponse);
        // echo '<pre>';print_r($companyResultResponse[0]->cityname);exit();
        return view('pages.company.list',['breadcrumbs' => $breadcrumbs], ['pageConfigs' => $pageConfigs,'pageTitle'=>$pageTitle,'companyResult'=>$companyResultResponse,'page'=>$page,'perPage'=>$perPage]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //echo"hi";die;
        $formUrl = 'company.create';
        $breadcrumbs = [
            ['link' => "/", 'name' => "Home"], ['link' => "company", 'name' => "Company"], ['name' => "Add"],
        ];
        //Pageheader set true for breadcrumbs
        $pageConfigs = ['pageHeader' => true];
        $pageTitle = __('locale.Company Add');
        $countries = Country::get(["name", "id"]);
        
        $companyCode = Helper::setNumber();
        // $companies = Company::get(["company_name", "id","company_code"]);
        return view('pages.company.create',['breadcrumbs' => $breadcrumbs], ['pageConfigs' => $pageConfigs,'pageTitle'=>$pageTitle,'countries'=>$countries,'companyCode'=>$companyCode,'formUrl'=>$formUrl]);
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
            
            'company_name' => 'required|max:250',
            'company_code'=>'required|unique:companies',
            'address1' => 'required|max:250',
            'address2' => 'required|max:250',
            'pincode' => 'required',
            
        //     //'contact_person' => 'required|max:250',
        //     //'contact_mobile' => 'required|max:20',
        //     //'licence_valid_till' => 'required|date'
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
            ->withErrors($validator)
            ->withInput();
        }
        // echo '<pre>';print_r($request->all());  exit();
        $request['license_from']=date('Y-m-d',strtotime($request['license_from']));
        $request['license_to']=date('Y-m-d',strtotime($request['license_to']));
        $company = Company::create($request->all());
        if($company){
            return redirect()->route('company.index')->with('success',__('locale.company_create_success'));
        }else{
            return redirect()->back()->with('error',__('locale.try_again'));
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        exit('show');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        // Breadcrumbs
        $formUrl = 'company.create';
        $breadcrumbs = [
            ['link' => "/", 'name' => "Home"], ['link' => "buyer", 'name' => "Care home master"], ['name' => "Edit"],
        ];
        //Pageheader set true for breadcrumbs
        $pageConfigs = ['pageHeader' => true];
        $pageTitle = __('locale.Companies');
        $company_result = Company::find($id);
        $countries = Country::get(["name", "id"]);
        $companyCode = Helper::setNumber();
        $states = State::where('country_id',$company_result->country)->get(["name", "id"]);
        $cities = City::where('state_id',$company_result->state)->get(["name", "id"]);
        if(!$company_result){
            return redirect('/company')->with('error','Company id not match');
        }
        return view('pages.company.create',['breadcrumbs' => $breadcrumbs], ['pageConfigs' => $pageConfigs,'pageTitle'=>$pageTitle,'company_result'=>$company_result,'countries'=>$countries,'states'=>$states,'cities'=>$cities,'companyCode'=>$companyCode,'formUrl'=>$formUrl]);
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

        // echo '<pre>';print_r($request->all()); exit();
        $company = Company::find($id);
        if ($request->has('company_code') && $request->input('company_code') != $company->company_code) {
            $company->company_code = $request->input('company_code');
        }
        $validator = Validator::make($request->all(), [
            'company_name' => 'required|max:250',
            //'company_code'=>'unique:companies',
            'address1' => 'required|max:250',
            'address2' => 'max:250',
            'country' => 'required',
            'state' => 'required',
            'city' => 'required',
            'pincode' => 'required',
            // 'contact_person' => 'required|max:250',
            // 'contact_mobile' => 'required|max:20',
            // 'licence_valid_till' => 'required|date'
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
            ->withErrors($validator)
            ->withInput();
        }
        unset($request['_method']);
        unset($request['_token']);
        unset($request['action']);
        //$requestData = $request->except(['company_code']);
        
        $company = Company::where('id',$id)->update($request->all());
        if($company){
            return redirect()->route('company.index')->with('success',__('locale.company_update_success'));
        }else{
            return redirect()->back()->with('error',__('locale.try_again'));
        }
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if(companyUserMapping::where('company_id',$id)->count()==0 && ProductCompanyMapping::where('company_id',$id)->count()==0){
            if(Company::where('id',$id)->delete()){
                return redirect()->back()->with('success',__('locale.delete_message'));
            }else{
                return redirect()->back()->with('error',__('locale.try_again'));
            }
        }else{
            return redirect()->back()->with('error',__('locale.company_delete_error_msg'));
        }
    }

    public function companyImport(Request $request)
    {
        //echo"<pre>";print_r($request->all());die;
        try{
            $import = new CompanyImport;
            Excel::import($import,request()->file('importcompany'));
           // die;
            // print_r($import); exit();
            return redirect()->route('company.index')->with('success', __('locale.import_message'));
        }catch(\Maatwebsite\Excel\Validators\ValidationException $e){
            
            return redirect()->route('company.index')->with('error', __('locale.try_again'));
        }
            
    }

    public function companyExport() 
    {
        //echo"in comp export";die;
        return Excel::download(new CompanyExport, 'company-'.time().'.xlsx');
    }

    public function fetchState(Request $request)
    {
        $data['states'] = State::where("country_id",$request->country_id)->get(["name", "id"]);
        return response()->json($data);
    }
    public function fetchCity(Request $request)
    {
        $data['cities'] = City::where("state_id",$request->state_id)->get(["name", "id"]);
        return response()->json($data);
    }
}

