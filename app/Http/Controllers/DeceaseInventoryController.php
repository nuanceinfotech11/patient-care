<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;
use App\Models\ProductCompanyMapping as ProductCompanyMappingModel;
use App\Models\{Products,ProductsVariations,ProductImagesModel,ProductsVariationsOptions};
use App\Models\{ProductCategoryModel,ProductSubCategory};
use App\Models\DeceaseInventoryMapping;
use App\Models\Decease;
use App\Models\Inventory;
use Illuminate\Support\Facades\Validator;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Helper;
use File;
use Image;

class DeceaseInventoryController extends Controller
{
    public function index(Request $request)
    {
        //echo"disease inventory map";die;
        $userType = auth()->user()->role()->first()->name;
        $perpage = config('app.perpage');
        $productResultResponse = [];
        
        // Breadcrumbs
        $breadcrumbs = [
            ['link' => "/superadmin", 'name' => "Home"], ['link' => "superadmin/inventory-mapping", 'name' => __('locale.inventory mapping')], ['name' => "Add"],
        ];
        //Pageheader set true for breadcrumbs
        $pageConfigs = ['pageHeader' => true];
        $pageTitle = __('locale.Inventory mapping');
        $editUrl = 'inventory-mapping.edit';
        if($userType=="Admin")
        {
            $editUrl = 'admin-inventory-mapping-edit';
        }
        $deleteUrl = 'inventory-mapping.delete';
        if($userType=="Admin")
        {
            $deleteUrl = 'admin-inventory-mapping-delete';
        }
        $paginationUrl = 'inventory-mapping.index';
        $listUrl='inventory-mapping.index';
        if($userType=="Admin")
        {
            $listUrl = 'admin-inventory-mapping-list';
        }
        $deceaseResult = Decease::select(['id','code','name'])->get();
        $inventoryResult = Inventory::select(['id','code','name'])->get();
        $deceaseinventoryMappingResult = DeceaseInventoryMapping::with(['decease','inventory'])->orderBy('id','desc');

        if(auth()->user()->role()->first()->name=="Admin")
        {
          $deceaseinventoryMappingResult = DeceaseInventoryMapping::with(['decease','inventory']);
        }
        
        // dd($deceaseinventoryMappingResult->get()); exit();
        if($request->ajax()){
            $deceaseinventoryMappingResult = $deceaseinventoryMappingResult->whereHas('decease',function($query) use ($request) {
                $query->where('name','like', '%'.$request->seach_term.'%');
            })->orWhereHas('inventory',function($q) use($request){
                $q->where('name', 'like', '%'.$request->seach_term.'%');
            })->paginate($perpage);
            $perPage = $perpage;
            $page = $deceaseinventoryMappingResult->currentPage();
            // $deceaseinventoryMappingResult->get
            return view('pages.inventory-mapping.ajax-list', compact('deceaseinventoryMappingResult','editUrl','deleteUrl','page','perPage'))->render();
        }
        $deceaseinventoryMappingResult = $deceaseinventoryMappingResult->paginate($perpage);
        $perPage = $perpage;
        $page = $deceaseinventoryMappingResult->currentPage();

        // $productMappingResponse  = [];
        // if($productMappingResult->count()>0){
        //     $productMappingResponse = $productMappingResult;
        // }
        return view('pages.inventory-mapping.list',['breadcrumbs' => $breadcrumbs], ['pageConfigs' => $pageConfigs,'pageTitle'=>$pageTitle,'deceaseinventoryMappingResult'=>$deceaseinventoryMappingResult,'userType'=>$userType,'editUrl'=>$editUrl,'deleteUrl'=>$deleteUrl,'page'=>$page,'perPage'=>$perPage]);
    }

    public function create($id='')
    {
       // echo"ji";die;
       $userType = auth()->user()->role()->first()->name;
       if($userType=="superadmin")
        {
        $formUrl = 'inventory-mapping.store';
        }
        if($userType=="Admin")
        {
        $formUrl = 'admin-inventory-mapping-create';
        }
        $breadcrumbs = [
            ['link' => "/", 'name' => "Home"], ['link' => "superadmin/inventory-mapping", 'name' => __("locale.inventory mapping")], ['name' => "Add"],
        ];
        //Pageheader set true for breadcrumbs

        $deceaseResult = Decease::select(['id','name','code'])->get();
        $inventoryResult = Inventory::select(['id','code','name'])->get();
        

        $pageConfigs = ['pageHeader' => true];
        $pageTitle = __('locale.Disease inventory add');
        return view('pages.inventory-mapping.create',['breadcrumbs' => $breadcrumbs], ['pageConfigs' => $pageConfigs,'pageTitle'=>$pageTitle,'deceaseResult'=>$deceaseResult,'inventoryResult'=>$inventoryResult,'formUrl'=>$formUrl]);
    }

    public function store(Request $request)
    {
       // echo"<pre>";print_r($request->all());die;
       // echo"store";die;
        $validator = Validator::make($request->all(), [
            'desease_id' => 'required',
            "inventory_id"    => "required",
        ]);
        unset($request['_token']);
        DeceaseInventoryMapping::create($request->all());
        // if ($validator->fails()) {
        //     return redirect()->back()
        //     ->withErrors($validator)
        //     ->withInput();
        // }
        // $productMappingData = [];
        // for($p=0;$p<count($request->product_ids);$p++){
        //     $productMappingData[] = ['company_id'=>$request->company_id,'product_id'=>$request->product_ids[$p]];
        // }
        // if(!empty($productMappingData)){
        //     ProductCompanyMappingModel::insert($productMappingData);
        //     return redirect()->route('product-mapping.index')->with('success',__('locale.success common add'));
        // }
        if(auth()->user()->role()->first()->name=="superadmin")
        {
            $backUrl='inventory-mapping.index';
        }
        if(auth()->user()->role()->first()->name=="Admin")
        {
            $backUrl='admin-inventory-mapping-list';
        }
        return redirect()->route($backUrl)->with('success',__('locale.success common add'));
    }

    public function edit($id)
    {
       // echo"edit";die;
        $userType = auth()->user()->role()->first()->name;
        $productIds = [];
        $breadcrumbs = [
            ['link' => "/", 'name' => "Home"], ['link' => "superadmin/product-mapping", 'name' => __("locale.inventory mapping")], ['name' => "Edit"],
        ];
        //Pageheader set true for breadcrumbs
        
        $deceaseResult = Decease::select(['id','name','code'])->get();
        $inventoryResult = Inventory::select(['id','code','name'])->get();
        $mappingResult = DeceaseInventoryMapping::with('decease','inventory')->select('id','decease_id','inventory_id')->where('id',$id)->get();
        // foreach($mappingResult as $map_val){
            //     $productIds[] = $map_val->decease_id;
            // }
            // echo"<pre>";print_r($mappingResult);die;
            $pageConfigs = ['pageHeader' => true];
            $pageTitle = __('locale.Disease inventory edit');
            if($id!=''){
                //$permission_arr = [];
                $mappingResult = DeceaseInventoryMapping::find($id);
            if($userType=="superadmin")
            {   
                $formUrl = 'inventory-mapping.update';
            }
            if($userType=="Admin")
            {   
                $formUrl = 'admin-inventory-mapping-update';
            }
        }
       // dd($mappingResult);
        return view('pages.inventory-mapping.create',['breadcrumbs' => $breadcrumbs], ['pageConfigs' => $pageConfigs,'pageTitle'=>$pageTitle,'formUrl'=>$formUrl,'mappingResult'=>$mappingResult,'productIds'=>$productIds,'deceaseResult'=>$deceaseResult,'inventoryResult'=>$inventoryResult]);
    }
    public function update(Request $request, $id)
    {
         //echo"<pre>";print_r($request->all());die;
        $validator = Validator::make($request->all(), [
            'decease_id' => 'required',
            "inventory_id" => "required",
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
            ->withErrors($validator)
            ->withInput();
        }
        unset($request['_token']);
        unset($request['_method']);
        unset($request['action']);
        $mapedData = DeceaseInventoryMapping::where('id',$id)->update($request->all());
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
            $backUrl='inventory-mapping.index';
        }
        if(auth()->user()->role()->first()->name=="Admin")
        {
            $backUrl='admin-inventory-mapping-list';
        }
        return redirect()->route($backUrl)->with('success',__('locale.success common update'));
    }

    public function destroy($id)
    {
        //echo"dell";die;
        $mappingResult = DeceaseInventoryMapping::where('id',$id)->count();
        
        if($mappingResult>0){
            DeceaseInventoryMapping::where('id',$id)->delete();
            return redirect()->back()->with('success',__('locale.delete_message'));
        }else{
            return redirect()->back()->with('error',__('locale.try_again'));
        }
    }

}
