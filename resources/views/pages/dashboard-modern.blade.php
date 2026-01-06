{{-- layout extend --}}
@extends('layouts.contentLayoutMaster')

{{-- page title --}}
@section('title','Dashboard Modern')

{{-- vendor styles --}}
@section('vendor-style')
<link rel="stylesheet" type="text/css" href="{{asset('vendors/animate-css/animate.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('vendors/chartist-js/chartist.min.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('vendors/chartist-js/chartist-plugin-tooltip.css')}}">
@endsection

{{-- page styles --}}
@section('page-style')
<link rel="stylesheet" type="text/css" href="{{asset('css/pages/dashboard-modern.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('css/pages/intro.css')}}">
@endsection

{{-- page content --}}
@section('content')
<div class="section">
   <!-- Current balance & total transactions cards-->
  
   <!--/ Current balance & total transactions cards-->

   <!-- User statistics & appointment cards-->
   
   <!--/ Current balance & appointment cards-->

   <div class="row">
      <div class="col s12 m6 l3">
         <div class="card padding-4 animate fadeLeft">
            <div class="row">
               <div class="col s5 m5 custom-tab">
                  <h5 class="mb-0">Admin</h5>
                  <p class="mb-0 pt-8">1,12,900</p>
               </div>
               <div class="col s7 m7 right-align">
                  <i
                     class="material-icons background-round mt-5 mb-5 gradient-45deg-purple-amber gradient-shadow white-text">perm_identity</i>
                  <p class="mb-0">Total Clients</p>
               </div>
            </div>
         </div>
         {{--
         <div id="chartjs" class="card pt-0 pb-0 animate fadeLeft">
            <div class="dashboard-revenue-wrapper padding-2 ml-2">
               <span class="new badge gradient-45deg-indigo-purple gradient-shadow mt-2 mr-2">+ $900</span>
               <p class="mt-2 mb-0 font-weight-600">Today's revenue</p>
               <p class="no-margin grey-text lighten-3">$40,512 avg</p>
               <h5>$ 22,300</h5>
            </div>
            <div class="sample-chart-wrapper card-gradient-chart">
               <canvas id="custom-line-chart-sample-three" class="center"></canvas>
            </div>
         </div>
         --}}
      </div>
      <div class="col s12 m6 l3">
         <div class="card padding-4 animate fadeLeft">
            <div class="row">
               <div class="col s5 m5 custom-tab">
                  <h5 class="mb-0">Patient</h5>
                  <p class="mb-0 pt-8">1,12,900</p>
               </div>
               <div class="col s7 m7 right-align">
                  <i
                     class="material-icons background-round mt-5 mb-5 gradient-45deg-purple-amber gradient-shadow white-text">perm_identity</i>
                  <p class="mb-0">Total Clients</p>
               </div>
            </div>
         </div>
         {{--
         <div id="chartjs" class="card pt-0 pb-0 animate fadeLeft">
            <div class="dashboard-revenue-wrapper padding-2 ml-2">
               <span class="new badge gradient-45deg-indigo-purple gradient-shadow mt-2 mr-2">+ $900</span>
               <p class="mt-2 mb-0 font-weight-600">Today's revenue</p>
               <p class="no-margin grey-text lighten-3">$40,512 avg</p>
               <h5>$ 22,300</h5>
            </div>
            <div class="sample-chart-wrapper card-gradient-chart">
               <canvas id="custom-line-chart-sample-three" class="center"></canvas>
            </div>
         </div>
         --}}
      </div>
      <div class="col s12 m6 l3">
         <div class="card padding-4 animate fadeLeft">
            <div class="row">
               <div class="col s5 m5 custom-tab">
                  <h5 class="mb-0">Manager</h5>
                  <p class="mb-0 pt-8">1,12,900</p>
               </div>
               <div class="col s7 m7 right-align">
                  <i
                     class="material-icons background-round mt-5 mb-5 gradient-45deg-purple-amber gradient-shadow white-text">perm_identity</i>
                  <p class="mb-0">Total Clients</p>
               </div>
            </div>
         </div>
         {{--
         <div id="chartjs" class="card pt-0 pb-0 animate fadeLeft">
            <div class="dashboard-revenue-wrapper padding-2 ml-2">
               <span class="new badge gradient-45deg-indigo-purple gradient-shadow mt-2 mr-2">+ $900</span>
               <p class="mt-2 mb-0 font-weight-600">Today's revenue</p>
               <p class="no-margin grey-text lighten-3">$40,512 avg</p>
               <h5>$ 22,300</h5>
            </div>
            <div class="sample-chart-wrapper card-gradient-chart">
               <canvas id="custom-line-chart-sample-three" class="center"></canvas>
            </div>
         </div>
         --}}
      </div>
      <div class="col s12 m6 l3">
         <div class="card padding-4 animate fadeLeft">
            <div class="row">
               <div class="col s5 m5 custom-tab">
                  <h5 class="mb-0">Carer</h5>
                  <p class="mb-0 pt-8">1,12,900</p>
               </div>
               <div class="col s7 m7 right-align">
                  <i
                     class="material-icons background-round mt-5 mb-5 gradient-45deg-purple-amber gradient-shadow white-text">perm_identity</i>
                  <p class="mb-0">Total Clients</p>
               </div>
            </div>
         </div>
         {{--
         <div id="chartjs" class="card pt-0 pb-0 animate fadeLeft">
            <div class="dashboard-revenue-wrapper padding-2 ml-2">
               <span class="new badge gradient-45deg-indigo-purple gradient-shadow mt-2 mr-2">+ $900</span>
               <p class="mt-2 mb-0 font-weight-600">Today's revenue</p>
               <p class="no-margin grey-text lighten-3">$40,512 avg</p>
               <h5>$ 22,300</h5>
            </div>
            <div class="sample-chart-wrapper card-gradient-chart">
               <canvas id="custom-line-chart-sample-three" class="center"></canvas>
            </div>
         </div>
         --}}
      </div>
      <div class="col s12 m12 l12">
         <div class="card subscriber-list-card animate fadeRight">
            <div class="card-content pb-1">
               <!-- <h4 class="card-title mb-0">Patient List<i class="material-icons float-right">more_vert</i></h4> -->
            </div>
            <table class="subscription-table responsive-table highlight">
               <thead>
                  <tr>
                     <th>Name</th>
                     <th>Company</th>
                     <th>Start Date</th>
                     <th>Status</th>
                     <th>Amount</th>
                     <th>Action</th>
                  </tr>
               </thead>
               <tbody>
                  <tr>
                     <td colspan="10"><p class="center">{{__('locale.no_record_found')}}</p></td>
                  </tr>
                  {{--   
                  <tr>
                     <td>Michael Austin</td>
                     <td>ABC Fintech LTD.</td>
                     <td>Jan 1,2019</td>
                     <td><span class="badge pink lighten-5 pink-text text-accent-2">Close</span></td>
                     <td>$ 1000.00</td>
                     <td class="center-align"><a href="#"><i class="material-icons pink-text">clear</i></a></td>
                  </tr>
                  <tr>
                     <td>Aldin Rakić</td>
                     <td>ACME Pvt LTD.</td>
                     <td>Jan 10,2019</td>
                     <td><span class="badge green lighten-5 green-text text-accent-4">Open</span></td>
                     <td>$ 3000.00</td>
                     <td class="center-align"><a href="#"><i class="material-icons pink-text">clear</i></a></td>
                  </tr>
                  <tr>
                     <td>İris Yılmaz</td>
                     <td>Collboy Tech LTD.</td>
                     <td>Jan 12,2019</td>
                     <td><span class="badge green lighten-5 green-text text-accent-4">Open</span></td>
                     <td>$ 2000.00</td>
                     <td class="center-align"><a href="#"><i class="material-icons pink-text">clear</i></a></td>
                  </tr>
                  <tr>
                     <td>Lidia Livescu</td>
                     <td>My Fintech LTD.</td>
                     <td>Jan 14,2019</td>
                     <td><span class="badge pink lighten-5 pink-text text-accent-2">Close</span></td>
                     <td>$ 1100.00</td>
                     <td class="center-align"><a href="#"><i class="material-icons pink-text">clear</i></a></td>
                  </tr>
                  --}}
               </tbody>
            </table>
         </div>
      </div>
      <div class="col s12 m12 l12">
         <div class="card subscriber-list-card animate fadeRight">
            <div class="card-content pb-1">
               <!-- <h4 class="card-title mb-0">Admin List<i class="material-icons float-right">more_vert</i></h4> -->
            </div>
            <table class="subscription-table responsive-table highlight">
               <thead>
                  <tr>
                     <th>Name</th>
                     <th>Company</th>
                     <th>Start Date</th>
                     <th>Status</th>
                     <th>Amount</th>
                     <th>Action</th>
                  </tr>
               </thead>
               <tbody>
                  <tr>
                     <td colspan="10"><p class="center">{{__('locale.no_record_found')}}</p></td>
                  </tr>
                  {{--   
                  <tr>
                     <td>Michael Austin</td>
                     <td>ABC Fintech LTD.</td>
                     <td>Jan 1,2019</td>
                     <td><span class="badge pink lighten-5 pink-text text-accent-2">Close</span></td>
                     <td>$ 1000.00</td>
                     <td class="center-align"><a href="#"><i class="material-icons pink-text">clear</i></a></td>
                  </tr>
                  <tr>
                     <td>Aldin Rakić</td>
                     <td>ACME Pvt LTD.</td>
                     <td>Jan 10,2019</td>
                     <td><span class="badge green lighten-5 green-text text-accent-4">Open</span></td>
                     <td>$ 3000.00</td>
                     <td class="center-align"><a href="#"><i class="material-icons pink-text">clear</i></a></td>
                  </tr>
                  <tr>
                     <td>İris Yılmaz</td>
                     <td>Collboy Tech LTD.</td>
                     <td>Jan 12,2019</td>
                     <td><span class="badge green lighten-5 green-text text-accent-4">Open</span></td>
                     <td>$ 2000.00</td>
                     <td class="center-align"><a href="#"><i class="material-icons pink-text">clear</i></a></td>
                  </tr>
                  <tr>
                     <td>Lidia Livescu</td>
                     <td>My Fintech LTD.</td>
                     <td>Jan 14,2019</td>
                     <td><span class="badge pink lighten-5 pink-text text-accent-2">Close</span></td>
                     <td>$ 1100.00</td>
                     <td class="center-align"><a href="#"><i class="material-icons pink-text">clear</i></a></td>
                  </tr>
                  --}}
               </tbody>
            </table>
         </div>
      </div>
   </div>
</div>

@endsection

{{-- vendor scripts --}}
@section('vendor-script')
<script src="{{asset('vendors/chartjs/chart.min.js')}}"></script>
<script src="{{asset('vendors/chartist-js/chartist.min.js')}}"></script>
<script src="{{asset('vendors/chartist-js/chartist-plugin-tooltip.js')}}"></script>
<script src="{{asset('vendors/chartist-js/chartist-plugin-fill-donut.min.js')}}"></script>
@endsection

{{-- page scripts --}}
@section('page-script')
<script src="{{asset('js/scripts/dashboard-modern.js')}}"></script>
<script src="{{asset('js/scripts/intro.js')}}"></script>
@endsection