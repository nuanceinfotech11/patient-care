{{-- layout --}}
@extends('layouts.contentLayoutMaster')

{{-- page title --}}
@include('panels.page-title')

{{-- vendor styles --}}
@section('vendor-style')
<link rel="stylesheet" type="text/css" href="{{asset('vendors/select2/select2.min.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('vendors/select2/select2-materialize.css')}}">
<!-- Add these in the head section of your HTML file -->
<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>


@endsection

{{-- page style --}}
@section('page-style')
<link rel="stylesheet" type="text/css" href="{{asset('css/pages/page-users.css')}}">
@endsection

{{-- page content --}}
@section('content')
<!-- users edit start -->
<div class="section users-edit">
  <div class="card">
    <div class="card-content">
      <!-- <div class="card-body"> -->
      
      <div class="row">
        <div class="col s12" id="account">
          
          <!-- users edit media object ends -->
          <!-- users edit account form start -->
          @include('panels.flashMessages')
          @if(isset($patient_carer_result->id))
          <?php //$formUrl = (isset($formUrl) && $formUrl!='') ? $formUrl : 'company-admin-update'; ?>
            <form class="formValidate" action="{{route($formUrl,$patient_carer_result->id)}}" id="formValidateCompany" method="post">
            {!! method_field('post') !!}
            @else
            <?php //$formUrl = (isset($formUrl) && $formUrl!='') ? $formUrl : 'company-admin-create'; ?>
          <form id="accountForm"  method="post">
            @endif
            @csrf()
            <div class="row">
            <div class="input-field col s12">
            <label for="serach">{{__('locale.Select patient')}}</label><br>
            <div class="input-field">
                <select name="patient_id" id="patient" required>
                <option value="Select" disabled selected>{{__('locale.Select patient')}} *</option>
                @if(isset($patient) && !empty($patient))
                @foreach($patient as $patient_val)
                
                <option value="{{$patient_val->id}}">{{ $patient_val->name }}</option>
                @endforeach
                @endif
                </select>
                @error('company_id')
                <div style="color:red">{{$message}}</div>
                @enderror
            </div> 
            
            </div>
            @if(isset($userType) && $userType!=config('custom.superadminrole'))
            <input type="hidden" name="company" value="{{Helper::loginUserCompanyId()}}"/>
            @else
                  <div class="col s12 input-field">
                    <select class="error" id="company" name="company" data-error=".errorTxt7" required>
                      <option value="">Choose {{__('locale.code')}}</option>
                      @if(isset($companies) && !empty($companies))
                        @foreach ($companies as $company_value)
                          <option value="{{$company_value->id}}">{{$company_value->company_name}} ({{$company_value->company_code}})</option>
                        @endforeach
                      @endif
                    </select>
                    <label for="company">{{__('locale.Care home code')}}</label>
                    <small class="errorTxt7"></small>
                  </div>
            @endif
                <!-- <div class="input-field col m6 s12">
                  <input id="datepicker" class="validate datepicker" name="date" type="text"  data-error=".errorTxt1" value="{{(isset($patient_schedule_result->date)) ? $patient_schedule_result->date : old('date')}}">
                  <label for="name">{{__('locale.date')}}</label>
                  <small class="errorTxt1"></small>
                </div> -->

                

                <div class="input-field col s12">
                <label for="serach">{{__('locale.Select carer name')}}</label><br>
                <div class="input-field">
                <select name="carer_id" id="carer" required>
                <option value="Select" disabled selected>{{__('locale.Select carer name')}} *</option>
                @if(isset($carer) && !empty($carer))
                @foreach($carer as $carer_val)
                
                <option value="{{ $carer_val->id }}">{{ $carer_val->name }}</option>
                @endforeach
                @endif
                </select>
                @error('company_id')
                <div style="color:red">{{$message}}</div>
                @enderror
            </div> 
            </div>

            
                  <!-- <input id="name" class="validate" name="carer_assigned_by" type="hidden" data-error=".errorTxt1" value="{{auth()->user()->id}}" readonly> -->
                  
                 

                <!-- <div class="input-field col m6 s12">
                <label for="serach">{{__('locale.assign_by')}}</label><br>
                <div class="input-field">
                  <select name="carer_assigned_by" id="carer_assign">
                  <option value="Select" disabled selected>{{__('locale.assign_by')}}*</option> -->
                  <!-- <input id="name" class="validate" name="carer_assigned_by" type="text" data-error=".errorTxt1" value="{{auth()->user()->id}}"> -->
                  
                      <!-- </select>
                  <small class="errorTxt1"></small>
                </div>
                </div> -->
                
            <!-- <div class="input-field col m6 s12">
                  <input id="name" class="validate" name="remarks" type="text" data-error=".errorTxt1" value="{{(isset($patient_schedule_result->remarks)) ? $patient_schedule_result->remarks : old('remarks')}}">
                  <label for="name">{{__('locale.remark')}}</label>
                  <small class="errorTxt1"></small>
                </div>
                <div class="input-field col m6 s12">
                  <input id="name" class="validate" name="attended_remarks" type="text" data-error=".errorTxt1" value="{{(isset($patient_schedule_result->attended_remarks)) ? $patient_schedule_result->attended_remarks : old('attended_remarks')}}">
                  <label for="name">{{__('locale.attended_remarks')}}</label>
                  <small class="errorTxt1"></small>
                </div>
                <div class="input-field col m6 s12">
                  <input id="name" class="validate" name="attended_on_time" type="time" data-error=".errorTxt1" value="{{(isset($patient_schedule_result->attended_on_time)) ? $patient_schedule_result->attended_on_time : old('attended_on_time')}}">
                  <label for="name">{{__('locale.time')}}</label>
                  <small class="errorTxt1"></small>
                </div>
                <div class="input-field col m12 s12">
                    <select name="attended">
                    <option value="yes">{{__('locale.yes')}}</option>
                    <option value="no">{{__('locale.no')}}</option>
                    </select>
                    <label>{{__('locale.attended')}}</label>
                </div> -->
                




                
                <div class="input-field col s12">
                  <button class="btn waves-effect waves-light right submit" type="submit" name="action">Submit
                    <i class="material-icons right">send</i>
                  </button>
                </div>
              </div>
          </form>
          <!-- users edit account form ends -->
        </div>
      </div>
      <!-- </div> -->
    </div>
  </div>
</div>
<!-- users edit ends -->
@endsection

{{-- vendor scripts --}}
@section('vendor-script')
<script src="{{asset('vendors/select2/select2.full.min.js')}}"></script>
<script src="{{asset('vendors/jquery-validation/jquery.validate.min.js')}}"></script>
@endsection

{{-- page scripts --}}
@section('page-script')
<script src="{{asset('js/scripts/page-users.js')}}"></script>
<script src="{{asset('js/scripts/form-validation.js')}}"></script>
<!-- Add these in the head section of your HTML file -->

<!-- <script src="{{asset('js/scripts/form-elements.js')}}"></script> -->

<script>
  window.onload=function(){
    var company_value = "{{(isset($patient_carer_result->company) && $patient_carer_result->company!='NULL') ? $patient_carer_result->company : old('company')}}";
    
    var patient_value = "{{(isset($patient_carer_result->patient_id) && $patient_carer_result->patient_id!='NULL') ? $patient_carer_result->patient_id : old('patient_id')}}";
    
    var carer_value = "{{(isset($patient_carer_result->carer_id) && $patient_carer_result->carer_id!='NULL') ? $patient_carer_result->carer_id : old('carer_id')}}";

   // var carer_assign_value="{{(isset($patient_schedule_result->carer_assigned_by) && $patient_schedule_result->carer_assigned_by!='NULL') ? $patient_schedule_result->carer_assigned_by : old('carer_assigned_by')}}";

   // var alternate_carer_value = "{{(isset($patient_schedule_result->alternate_carer_code) && $patient_schedule_result->alternate_carer_code!='NULL') ? $patient_schedule_result->alternate_carer_code : old('alternate_carer_code')}}";
    
    console.log(company_value,'company_value');

    $('#company').val(company_value);
    $('#company').formSelect();

    $('#patient').val(patient_value);
    $('#patient').formSelect();

    $('#carer').val(carer_value);
    $('#carer').formSelect();

    $('#carer_assign').val(carer_assign_value);
    $('#carer_assign').formSelect();

    $('#alternate_carer').val(alternate_carer_value);
    $('#alternate_carer').formSelect();
    
  }
    $(document).ready(function () {


      $("#datepicker").datepicker({
      dateFormat: "YYYY-MM-DD", // Format the date as needed (e.g., YYYY-MM-DD).
      changeYear: true, // Enable year dropdown selection.
      changeMonth: true, // Enable month dropdown selection.
      // You can add more options as needed.
    });
      
      

        $('#country').on('change', function () {
            var idCountry = this.value;
            console.log(idCountry);
            $("#state").html('');
            $.ajax({
                url: "{{url('api/fetch-states')}}",
                type: "POST",
                data: {
                    country_id: idCountry,
                    _token: '{{csrf_token()}}'
                },
                dataType: 'json',
                success: function (result) {
                    $('#state').html('<option value="">Select State</option>');
                    $.each(result.states, function (key, value) {
                        $("#state").append('<option value="' + value
                            .id + '">' + value.name + '</option>');
                    });
                    $('#state').formSelect();
                    $('#city').html('<option value="">Select City</option>');
                }
            });
        });
        $('#state').on('change', function () {
            var idState = this.value;
            $("#city").html('');
            $.ajax({
                url: "{{url('api/fetch-cities')}}",
                type: "POST",
                data: {
                    state_id: idState,
                    _token: '{{csrf_token()}}'
                },
                dataType: 'json',
                success: function (res) {
                    $('#city').html('<option value="">Select City</option>');
                    $.each(res.cities, function (key, value) {
                        $("#city").append('<option value="' + value
                            .id + '">' + value.name + '</option>');
                    });
                    $('#city').formSelect();
                }
            });
        });

       
    
  
    });
</script>
@endsection