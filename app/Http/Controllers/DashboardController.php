<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\{Country, State, City};
use App\Models\{ProductCategoryModel,ProductSubCategory};
use Helper;
use File;
use Image;
use Response;

class DashboardController extends Controller
{
    public function dashboardModern()
    {
       // echo"hi hello";die;
       //echo Auth::user()->role()->first()->id;die;
        /*if(Auth::user()->role()->first()->id=='1'){
           return redirect()->route('superadmin.dashboard');
         // return view('/pages/dashboard-modern');
        }
        if(Auth::user()->role()->first()->id==2){
          //  return redirect('/admin');
        };*/
        return view('/pages/dashboard-modern');
    }

    public function dashboardSuperadminModern()
    {
       // echo"dash";die;
        
        return view('/pages/dashboard-modern');
    }

    public function dashboardEcommerce()
    {
        // navbar large
        $pageConfigs = ['navbarLarge' => false];

        return view('/pages/dashboard-ecommerce', ['pageConfigs' => $pageConfigs]);
    }

    public function dashboardAnalytics()
    {
        // navbar large
        $pageConfigs = ['navbarLarge' => false];

        return view('/pages/dashboard-analytics', ['pageConfigs' => $pageConfigs]);
    }

    public function displayImage($filename)
    {
        // dd($filename);exit();
        $path = storage_path('product/images/'.$filename);
        if (!File::exists($path)) {
            abort(404);
        }

        $file = File::get($path);

        $type = File::mimeType($path);

    

        $response = Response::make($file, 200);

        $response->header("Content-Type", $type);

    

        return $response;

    }

    public function user_fetchState(Request $request)
    {
        $data['states'] = State::where("country_id",$request->country_id)->get(["name", "id"]);
        return response()->json($data);
    }
    public function user_fetchCity(Request $request)
    {
        $data['cities'] = City::where("state_id",$request->state_id)->get(["name", "id"]);
        return response()->json($data);
    }

    public function product_fetchSubcategory(Request $request)
    {
        $data['subcategory'] = ProductSubCategory::where("procat_id",$request->category_id)->get(["subcat_name", "id","procat_id"]);
        return response()->json($data);
    }

   
}
